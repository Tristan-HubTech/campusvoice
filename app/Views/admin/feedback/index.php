<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<section class="panel">
    <div class="panel-head stack-mobile">
        <h2>Feedback Queue</h2>
        <form method="get" action="<?= site_url('admin/feedback') ?>" class="filter-row">
            <input type="text" name="q" placeholder="Search subject, message, category" value="<?= esc((string) ($filters['q'] ?? '')) ?>">

            <select name="status">
                <option value="">All Statuses</option>
                <?php foreach (['new', 'reviewed', 'resolved'] as $status): ?>
                    <option value="<?= esc($status) ?>" <?= (($filters['status'] ?? '') === $status) ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="type">
                <option value="">All Types</option>
                <?php foreach (['complaint', 'suggestion', 'praise'] as $type): ?>
                    <option value="<?= esc($type) ?>" <?= (($filters['type'] ?? '') === $type) ? 'selected' : '' ?>><?= esc(ucfirst($type)) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="category_id">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= ((string) ($filters['category_id'] ?? '') === (string) $category['id']) ? 'selected' : '' ?>>
                        <?= esc((string) $category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Apply</button>
        </form>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Category</th>
                <th>Subject</th>
                <th>Author</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php if (! empty($feedbackList)): ?>
                <?php $rowNum = 1; foreach ($feedbackList as $item): ?>
                    <tr>
                        <td>#<?= $rowNum++ ?></td>
                        <td><span class="pill type-<?= esc((string) $item['type']) ?>"><?= esc(ucfirst((string) $item['type'])) ?></span></td>
                        <td><?= esc((string) ($item['category_name'] ?? 'N/A')) ?></td>
                        <td><?= esc((string) ($item['subject'] ?: mb_strimwidth((string) $item['message'], 0, 50, '...'))) ?></td>
                        <td>
                            <?php if ((int) ($item['is_anonymous'] ?? 0) === 1): ?>
                                Anonymous
                            <?php else: ?>
                                <?= esc(trim(((string) ($item['first_name'] ?? '')) . ' ' . ((string) ($item['last_name'] ?? '')))) ?>
                            <?php endif; ?>
                        </td>
                        <td><span class="pill status-<?= esc((string) $item['status']) ?>"><?= esc(ucfirst((string) $item['status'])) ?></span></td>
                        <td><?= esc((string) date('M d, Y H:i', strtotime((string) ($item['created_at'] ?? 'now')))) ?></td>
                        <td><a class="text-link" href="<?= site_url('admin/feedback/' . (int) $item['id']) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No feedback records found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
