<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<section class="panel">
    <div class="panel-head">
        <h2>Announcements</h2>
        <a class="btn-link" href="<?= site_url('admin/announcements/create') ?>">Create New</a>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
            <tr>
                <th>Title</th>
                <th>Audience</th>
                <th class="status-col">Status</th>
                <th>Publish</th>
                <th>Expires</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (! empty($announcements)): ?>
                <?php foreach ($announcements as $item): ?>
                    <tr>
                        <td>
                            <strong><?= esc((string) $item['title']) ?></strong>
                            <div class="muted"><?= esc(mb_strimwidth((string) $item['body'], 0, 80, '...')) ?></div>
                        </td>
                        <td><?= esc(ucfirst((string) $item['audience'])) ?></td>
                        <td>
                            <?php if ((int) ($item['is_published'] ?? 0) === 1): ?>
                                <span class="pill status-active">Published</span>
                            <?php else: ?>
                                <span class="pill status-inactive">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $item['publish_at'] ? esc((string) date('M d, Y H:i', strtotime((string) $item['publish_at']))) : '-' ?></td>
                        <td><?= $item['expires_at'] ? esc((string) date('M d, Y H:i', strtotime((string) $item['expires_at']))) : '-' ?></td>
                        <td>
                            <div class="tbl-actions">
                                <a class="text-link" href="<?= site_url('admin/announcements/' . (int) $item['id'] . '/edit') ?>">Edit</a>
                                <form method="post" action="<?= site_url('admin/announcements/' . (int) $item['id'] . '/delete') ?>" class="inline-form" onsubmit="return confirm('Delete this announcement?');">
                                    <button class="text-btn danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No announcements available yet.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
