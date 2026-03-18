<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<section class="panel">
    <div class="panel-head">
        <h2>Edit Announcement</h2>
        <a href="<?= site_url('admin/announcements') ?>">Back</a>
    </div>

    <?= view('admin/announcements/_form', [
        'action' => site_url('admin/announcements/' . (int) $announcement['id']),
        'announcement' => $announcement,
        'isEdit' => true,
    ]) ?>
</section>
<?= $this->endSection() ?>
