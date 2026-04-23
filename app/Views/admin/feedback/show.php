<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="panel-grid single-column-mobile">
    <section class="panel">
        <div class="panel-head">
            <h2>Feedback #<?= isset($feedback['row_num']) ? (int)$feedback['row_num'] : (int)$feedback['id'] ?></h2>
            <a href="<?= site_url('admin/feedback') ?>">Back to queue</a>
        </div>

        <div class="detail-grid">
            <div>
                <h4>Type</h4>
                <p><span class="pill type-<?= esc((string) $feedback['type']) ?>"><?= esc(ucfirst((string) $feedback['type'])) ?></span></p>
            </div>
            <div>
                <h4>Status</h4>
                <p><span class="pill status-<?= esc((string) $feedback['status']) ?>"><?= esc(ucfirst((string) $feedback['status'])) ?></span></p>
            </div>
            <div>
                <h4>Category</h4>
                <p><?= esc((string) ($feedback['category_name'] ?? 'N/A')) ?></p>
            </div>
            <div>
                <h4>Submitted</h4>
                <p><?= esc((string) date('M d, Y H:i', strtotime((string) ($feedback['created_at'] ?? 'now')))) ?></p>
            </div>
        </div>

        <h4>Subject</h4>
        <p><?= esc((string) ($feedback['subject'] ?? 'No subject')) ?></p>

        <?php
        $aimg = (string) ($feedback['image_path'] ?? '');
        if ($aimg !== ''):
            $aurl = \App\Libraries\FeedbackImageStorage::publicUrl($aimg);
        ?>
        <h4>Attachment</h4>
        <div class="admin-feedback-image">
            <a href="<?= esc($aurl) ?>" target="_blank" rel="noopener noreferrer">
                <img src="<?= esc($aurl) ?>" alt="Student upload" loading="lazy" decoding="async">
            </a>
        </div>
        <?php endif; ?>

        <h4>Message</h4>
        <article class="message-box"><?= nl2br(esc((string) $feedback['message'])) ?></article>

        <h4>Student</h4>
        <p>
            <?php if ((int) ($feedback['is_anonymous'] ?? 0) === 1): ?>
                Anonymous submission
            <?php else: ?>
                <?= esc(trim(((string) ($feedback['first_name'] ?? '')) . ' ' . ((string) ($feedback['last_name'] ?? '')))) ?>
                (<?= esc((string) ($feedback['email'] ?? 'N/A')) ?>)
            <?php endif; ?>
        </p>

        <form method="post" action="<?= site_url('admin/feedback/' . (int) $feedback['id'] . '/status') ?>" class="form-grid">
            <h3>Update Status</h3>
            <select name="status" required>
                <?php foreach (['new', 'reviewed', 'resolved'] as $status): ?>
                    <option value="<?= esc($status) ?>" <?= ((string) $feedback['status'] === $status) ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="admin_notes" rows="3" placeholder="Optional note for your team"><?= esc((string) ($feedback['admin_notes'] ?? '')) ?></textarea>
            <button type="submit">Save Status</button>
        </form>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Admin Replies</h2>
        </div>

        <?php if (! empty($replies)): ?>
            <div class="reply-thread">
                <?php foreach ($replies as $reply): ?>
                    <article class="reply-item">
                        <header>
                            <strong><?= esc(trim(((string) ($reply['first_name'] ?? '')) . ' ' . ((string) ($reply['last_name'] ?? '')))) ?></strong>
                            <small><?= esc((string) date('M d, Y H:i', strtotime((string) ($reply['created_at'] ?? 'now')))) ?></small>
                        </header>
                        <p><?= nl2br(esc((string) $reply['message'])) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="muted">No replies yet.</p>
        <?php endif; ?>

        <form method="post" action="<?= site_url('admin/feedback/' . (int) $feedback['id'] . '/reply') ?>" class="form-grid">
            <h3>Add Reply</h3>
            <textarea name="message" rows="4" required placeholder="Type your response for this concern"></textarea>
            <button type="submit">Post Reply</button>
        </form>
    </section>
</div>
<?= $this->endSection() ?>
