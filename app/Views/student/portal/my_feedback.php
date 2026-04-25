<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page narrow">
    <div class="portal-welcome">
        <div>
            <h2>What You've Shared</h2>
            <p class="muted">Track statuses and read admin replies on each submission.</p>
        </div>
        <a href="<?= site_url('users/feedback/submit') ?>" class="btn-primary">+ Share Feedback</a>
    </div>

    <section class="portal-card">
        <?php if (! empty($feedbackList)): ?>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Ref #</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Replies</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $rowNum = 0; ?>
                    <?php foreach ($feedbackList as $item): ?>
                        <?php $rowNum++; ?>
                        <tr>
                            <td>#<?= $rowNum ?></td>
                            <td>
                                <?php
                                $ip = (string) ($item['image_path'] ?? '');
                                if ($ip !== ''):
                                    $iurl = \App\Libraries\FeedbackImageStorage::publicUrl($ip);
                                ?>
                                    <a href="<?= esc($iurl) ?>" target="_blank" rel="noopener noreferrer" title="View image">
                                        <img src="<?= esc($iurl) ?>" alt="" class="data-table__thumb" loading="lazy">
                                    </a>
                                <?php else: ?>
                                    <span class="data-table__thumb--empty" title="No image">—</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="fbk-badge">#FBK-<?= str_pad((string) (int) $item['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                            <td><?= esc((string) ($item['category_name'] ?? 'N/A')) ?></td>
                            <td><span class="pill type-<?= esc((string) ($item['type'] ?? 'suggestion')) ?>"><?= esc(ucfirst((string) ($item['type'] ?? 'suggestion'))) ?></span></td>
                            <td><span class="pill status-<?= esc((string) ($item['status'] ?? 'new')) ?>"><?= esc(ucfirst((string) ($item['status'] ?? 'new'))) ?></span></td>
                            <td><?= esc(date('M d, Y H:i', strtotime((string) ($item['created_at'] ?? 'now')))) ?></td>
                            <td>
                                <?php $rc = (int) ($item['reply_count'] ?? 0); ?>
                                <?php if ($rc > 0): ?>
                                    <a href="<?= site_url('users/feedback/' . (int) $item['id']) ?>" class="pill status-reviewed" style="font-size:0.72rem; text-decoration:none;">
                                        <?= $rc ?> <?= $rc === 1 ? 'reply' : 'replies' ?>
                                    </a>
                                <?php else: ?>
                                    <span class="muted" style="font-size:0.85rem;">0</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="<?= site_url('users/feedback/' . (int) $item['id']) ?>" class="btn-view-sm">View</a>
                                    <button type="button" class="btn-delete-sm" data-delete-url="<?= site_url('users/feedback/' . (int) $item['id'] . '/delete') ?>">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="muted empty-hint">Nothing shared yet. <a href="<?= site_url('users/feedback/submit') ?>">Got something on your mind?</a></p>
        <?php endif; ?>
    </section>
</div>
<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-card">
        <div class="modal-icon">🗑️</div>
        <h3 class="modal-title">Delete Feedback</h3>
        <p class="modal-text">Are you sure you want to delete this submission? This action cannot be undone.</p>
        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn--cancel" id="modalCancel">Cancel</button>
            <form method="post" id="deleteForm" style="margin:0;">
                <button type="submit" class="modal-btn modal-btn--confirm">Yes, Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
(function(){
    const overlay = document.getElementById('deleteModal');
    const form    = document.getElementById('deleteForm');
    const cancel  = document.getElementById('modalCancel');

    document.querySelectorAll('.btn-delete-sm[data-delete-url]').forEach(function(btn){
        btn.addEventListener('click', function(){
            form.action = btn.getAttribute('data-delete-url');
            overlay.classList.add('is-visible');
        });
    });

    cancel.addEventListener('click', function(){
        overlay.classList.remove('is-visible');
    });

    overlay.addEventListener('click', function(e){
        if(e.target === overlay) overlay.classList.remove('is-visible');
    });

    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') overlay.classList.remove('is-visible');
    });
})();
</script>
<?= $this->endSection() ?>
