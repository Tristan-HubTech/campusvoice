<?php
$announcement = $announcement ?? null;
$isEdit = $isEdit ?? false;
$action = $action ?? site_url('admin/announcements');
?>

<form method="post" action="<?= esc($action) ?>" class="form-grid">
    <label for="title">Title</label>
    <input id="title" name="title" required maxlength="180" value="<?= esc((string) old('title', (string) ($announcement['title'] ?? ''))) ?>">

    <label for="body">Body</label>
    <textarea id="body" name="body" rows="8" required><?= esc((string) old('body', (string) ($announcement['body'] ?? ''))) ?></textarea>

    <label for="audience">Audience</label>
    <select id="audience" name="audience" required>
        <?php $audience = (string) old('audience', (string) ($announcement['audience'] ?? 'all')); ?>
        <?php foreach (['all', 'students', 'admins'] as $option): ?>
            <option value="<?= esc($option) ?>" <?= $audience === $option ? 'selected' : '' ?>><?= esc(ucfirst($option)) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="publish_at">Publish At</label>
    <input id="publish_at" name="publish_at" type="datetime-local" value="<?= esc((string) old('publish_at', isset($announcement['publish_at']) && $announcement['publish_at'] !== null ? date('Y-m-d\TH:i', strtotime((string) $announcement['publish_at'])) : '')) ?>">

    <label for="expires_at">Expires At</label>
    <input id="expires_at" name="expires_at" type="datetime-local" value="<?= esc((string) old('expires_at', isset($announcement['expires_at']) && $announcement['expires_at'] !== null ? date('Y-m-d\TH:i', strtotime((string) $announcement['expires_at'])) : '')) ?>">

    <label for="is_published">Publish Status</label>
    <select id="is_published" name="is_published">
        <?php $isPublished = (string) old('is_published', (string) ($announcement['is_published'] ?? '1')); ?>
        <option value="1" <?= $isPublished === '1' ? 'selected' : '' ?>>Published</option>
        <option value="0" <?= $isPublished === '0' ? 'selected' : '' ?>>Draft</option>
    </select>

    <div class="form-actions">
        <button type="submit"><?= $isEdit ? 'Update Announcement' : 'Create Announcement' ?></button>
        <a href="<?= site_url('admin/announcements') ?>" class="ghost-link">Cancel</a>
    </div>
</form>
