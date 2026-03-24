<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page">
    <div class="portal-welcome">
        <div>
            <h2>My Feedback Submissions</h2>
            <p class="muted">Track statuses and read admin replies on each submission.</p>
        </div>
        <a href="<?= site_url('users/feedback/submit') ?>" class="btn-primary">+ Submit Feedback</a>
    </div>

    <section class="portal-card">
        <?php if (! empty($feedbackList)): ?>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($feedbackList as $item): ?>
                        <tr>
                            <td>#<?= (int) $item['id'] ?></td>
                            <td><?= esc((string) ($item['category_name'] ?? 'N/A')) ?></td>
                            <td><span class="pill type-<?= esc((string) ($item['type'] ?? 'suggestion')) ?>"><?= esc(ucfirst((string) ($item['type'] ?? 'suggestion'))) ?></span></td>
                            <td>
                                <a href="<?= site_url('users/feedback/' . (int) $item['id']) ?>">
                                    <?= esc((string) ($item['subject'] ?? 'Feedback #' . (int) $item['id'])) ?>
                                </a>
                            </td>
                            <td><span class="pill status-<?= esc((string) ($item['status'] ?? 'new')) ?>"><?= esc(ucfirst((string) ($item['status'] ?? 'new'))) ?></span></td>
                            <td><?= esc(date('M d, Y H:i', strtotime((string) ($item['created_at'] ?? 'now')))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="muted empty-hint">No submissions yet. <a href="<?= site_url('users/feedback/submit') ?>">Submit your first feedback.</a></p>
        <?php endif; ?>
    </section>
</div>
<?= $this->endSection() ?>
