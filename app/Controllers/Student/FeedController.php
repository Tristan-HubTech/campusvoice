<?php

namespace App\Controllers\Student;

use App\Libraries\CommentImageStorage;
use App\Models\SocialCommentModel;
use App\Models\SocialPostModel;
use App\Models\SocialReactionModel;
use App\Models\SocialShareModel;

class FeedController extends StudentBaseController
{
    public function createPost()
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $rules = [
            'body'         => 'required|min_length[3]|max_length[4000]',
            'is_anonymous' => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()))->withInput();
        }

        $isAnonymous = (int) ($this->request->getPost('is_anonymous') ?? 0);
        if ($isAnonymous !== 1) {
            $profile = $this->ensureProfile((int) $this->viewer()['id']);
            $isAnonymous = (int) ($profile['is_anonymous'] ?? 0);
        }

        $postPayload = [
            'user_id'   => (int) $this->viewer()['id'],
            'body'      => trim(strip_tags((string) $this->request->getPost('body'))),
            'is_public' => 1,
        ];

        if (db_connect()->fieldExists('is_anonymous', 'social_posts')) {
            $postPayload['is_anonymous'] = $isAnonymous === 1 ? 1 : 0;
        }

        (new SocialPostModel())->insert($postPayload);

        return redirect()->to(site_url('users'))->with('success', 'Your post is now live.');
    }

    public function deletePost(int $postId)
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $postModel = new SocialPostModel();
        $post = $postModel->find($postId);

        if ($post === null || (int) $post['user_id'] !== (int) $this->viewer()['id']) {
            return redirect()->back()->with('error', 'Post not found or access denied.');
        }

        $postModel->delete($postId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['ok' => true]);
        }

        return redirect()->back()->with('success', 'Post deleted.');
    }

    public function react(int $postId)
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $reactionType = strtolower(trim((string) ($this->request->getPost('reaction_type') ?? '')));
        if (! in_array($reactionType, ['like', 'love', 'haha', 'wow', 'sad', 'angry'], true)) {
            return $this->redirectToReferrer('feed')->with('error', 'Unsupported reaction.');
        }

        $post = (new SocialPostModel())->find($postId);
        if ($post === null) {
            return $this->redirectToReferrer('feed')->with('error', 'Post not found.');
        }

        $viewerId = (int) $this->viewer()['id'];
        $reactionModel = new SocialReactionModel();
        $existing = $reactionModel
            ->where('post_id', $postId)
            ->where('user_id', $viewerId)
            ->first();

        if ($existing !== null && (string) $existing['reaction_type'] === $reactionType) {
            $reactionModel->delete((int) $existing['id']);
        } else {
            $payload = [
                'post_id'       => $postId,
                'user_id'       => $viewerId,
                'reaction_type' => $reactionType,
            ];

            if ($existing !== null) {
                $payload['id'] = (int) $existing['id'];
            }

            $reactionModel->save($payload);
        }

        $reactionRows = db_connect()->table('social_post_reactions')
            ->select('reaction_type, COUNT(*) as total')
            ->where('post_id', $postId)
            ->groupBy('reaction_type')
            ->get()->getResultArray();
        $breakdown = [];
        $reactionTotal = 0;
        foreach ($reactionRows as $r) {
            $breakdown[$r['reaction_type']] = (int) $r['total'];
            $reactionTotal += (int) $r['total'];
        }
        $viewerReaction = (new SocialReactionModel())
            ->where('post_id', $postId)
            ->where('user_id', $viewerId)
            ->first();

        return $this->response->setJSON([
            'ok'                 => true,
            'reaction_total'     => $reactionTotal,
            'breakdown'          => $breakdown,
            'reaction_breakdown' => $breakdown,
            'viewer_reaction'    => $viewerReaction ? (string) $viewerReaction['reaction_type'] : null,
        ]);
    }

    public function comment(int $postId)
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $rules = [
            'body'         => 'permit_empty|max_length[1000]',
            'is_anonymous' => 'permit_empty|in_list[0,1]',
            'parent_id'    => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['ok' => false, 'error' => implode(' ', $this->validator->getErrors())]);
            }

            return $this->redirectToReferrer('posts/' . $postId)->with('error', implode(' ', $this->validator->getErrors()))->withInput();
        }

        $post = (new SocialPostModel())->find($postId);
        if ($post === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['ok' => false, 'error' => 'Post not found.']);
            }

            return $this->redirectToReferrer('feed')->with('error', 'Post not found.');
        }

        $uploadedFile   = $this->request->getFile('image');
        $imagePath      = null;
        $hasImageColumn = db_connect()->fieldExists('image_path', 'social_post_comments');
        if ($hasImageColumn) {
            try {
                $imagePath = CommentImageStorage::tryStore($uploadedFile);
            } catch (\RuntimeException $e) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['ok' => false, 'error' => $e->getMessage()]);
                }

                return $this->redirectToReferrer('posts/' . $postId, 'post-' . $postId)->with('error', $e->getMessage());
            }
        } elseif ($uploadedFile !== null && $uploadedFile->isValid() && $uploadedFile->getError() !== UPLOAD_ERR_NO_FILE) {
            $msg = 'Comment images are not available until the database is updated. Run: php spark migrate';

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['ok' => false, 'error' => $msg]);
            }

            return $this->redirectToReferrer('posts/' . $postId, 'post-' . $postId)->with('error', $msg);
        }

        $body = trim(strip_tags((string) $this->request->getPost('body')));

        if ($body === '' && $imagePath === null) {
            $msg = 'Add a message or an image.';

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['ok' => false, 'error' => $msg]);
            }

            return $this->redirectToReferrer('posts/' . $postId, 'post-' . $postId)->with('error', $msg);
        }

        $isAnonymous = (int) ($this->request->getPost('is_anonymous') ?? 0);
        if ($isAnonymous !== 1) {
            $profile = $this->ensureProfile((int) $this->viewer()['id']);
            $isAnonymous = (int) ($profile['is_anonymous'] ?? 0);
        }

        $parentId = (int) ($this->request->getPost('parent_id') ?? 0);

        $commentPayload = [
            'post_id' => $postId,
            'user_id' => (int) $this->viewer()['id'],
            'body'    => $body,
        ];

        if ($hasImageColumn && $imagePath !== null) {
            $commentPayload['image_path'] = $imagePath;
        }

        if ($parentId > 0) {
            $parentComment = (new SocialCommentModel())->where('post_id', $postId)->find($parentId);
            if ($parentComment !== null) {
                $commentPayload['parent_id'] = $parentId;
            }
        }

        if (db_connect()->fieldExists('is_anonymous', 'social_post_comments')) {
            $commentPayload['is_anonymous'] = $isAnonymous === 1 ? 1 : 0;
        }

        $commentModel = new SocialCommentModel();
        if (! $commentModel->insert($commentPayload)) {
            CommentImageStorage::delete($imagePath);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['ok' => false, 'error' => 'Could not save comment.']);
            }

            return $this->redirectToReferrer('posts/' . $postId, 'post-' . $postId)->with('error', 'Could not save comment.');
        }

        if ($this->request->isAJAX()) {
            $viewer = $this->viewer();
            $profile = $this->ensureProfile((int) $viewer['id']);
            $isAnonComment = $isAnonymous === 1;
            $authorName = $isAnonComment
                ? $this->anonymousAlias((int) $viewer['id'])
                : trim((string) ($viewer['name'] ?? 'User'));
            $avatarColor = $isAnonComment ? 'violet' : (string) ($profile['avatar_color'] ?? 'blue');
            $initial = strtoupper(substr($authorName, 0, 1));
            $newCommentId = $commentModel->getInsertID();
            $commentTotal = $commentModel->where('post_id', $postId)->where('deleted_at', null)->countAllResults();
            $imageUrl = $imagePath ? CommentImageStorage::publicUrl($imagePath) : '';

            return $this->response->setJSON([
                'ok' => true,
                'comment' => [
                    'id'           => $newCommentId,
                    'parent_id'    => $commentPayload['parent_id'] ?? null,
                    'author_name'  => $authorName,
                    'avatar_color' => $avatarColor,
                    'initial'      => $initial,
                    'body'         => $body,
                    'created_at'   => date('M d, Y h:i A'),
                    'image_url'    => $imageUrl,
                ],
                'comment_total' => $commentTotal,
            ]);
        }

        return $this->redirectToReferrer('posts/' . $postId, 'post-' . $postId)->with('success', 'Comment added.');
    }

    public function commentReact(int $commentId)
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $reactionType = strtolower(trim((string) ($this->request->getPost('reaction_type') ?? '')));
        if (! in_array($reactionType, ['like', 'love', 'haha', 'wow', 'sad', 'angry'], true)) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Invalid reaction.']);
        }

        $comment = (new SocialCommentModel())->find($commentId);
        if ($comment === null) {
            return $this->response->setJSON(['ok' => false, 'error' => 'Comment not found.']);
        }

        $viewerId = (int) $this->viewer()['id'];
        $reactionModel = new \App\Models\CommentReactionModel();

        $existing = $reactionModel
            ->where('comment_id', $commentId)
            ->where('user_id', $viewerId)
            ->first();

        if ($existing !== null && $existing['reaction_type'] === $reactionType) {
            $reactionModel->delete((int) $existing['id']);
            $viewerReaction = null;
        } else {
            $payload = [
                'comment_id'    => $commentId,
                'user_id'       => $viewerId,
                'reaction_type' => $reactionType,
            ];
            if ($existing !== null) {
                $payload['id'] = (int) $existing['id'];
            }
            $reactionModel->save($payload);
            $viewerReaction = $reactionType;
        }

        $breakdownRows = db_connect()->table('comment_reactions')
            ->select('reaction_type, COUNT(*) as total')
            ->where('comment_id', $commentId)
            ->groupBy('reaction_type')
            ->get()->getResultArray();
        $reactionBreakdown = [];
        foreach ($breakdownRows as $br) {
            $reactionBreakdown[(string) $br['reaction_type']] = (int) $br['total'];
        }

        return $this->response->setJSON([
            'ok'                 => true,
            'viewer_reaction'    => $viewerReaction,
            'breakdown'          => $reactionBreakdown,
            'reaction_breakdown' => $reactionBreakdown,
        ]);
    }

    public function share(int $postId)
    {
        $guard = $this->requireUser();
        if ($guard !== null) {
            return $guard;
        }

        $post = (new SocialPostModel())->find($postId);
        if ($post === null) {
            return $this->redirectToReferrer('feed')->with('error', 'Post not found.');
        }

        $viewerId = (int) $this->viewer()['id'];
        $shareModel = new SocialShareModel();
        $existing = $shareModel
            ->where('post_id', $postId)
            ->where('user_id', $viewerId)
            ->first();

        if ($existing === null) {
            $shareModel->insert([
                'post_id' => $postId,
                'user_id' => $viewerId,
            ]);
        } else {
            $shareModel->update((int) $existing['id'], [
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $shareTotal = (new SocialShareModel())->where('post_id', $postId)->countAllResults();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'          => true,
                'share_total' => $shareTotal,
            ]);
        }

        return $this->redirectToReferrer('posts/' . $postId, 'post-' . $postId)->with('success', 'Post link saved to your shares.');
    }
}
