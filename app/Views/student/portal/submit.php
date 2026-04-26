<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<?php $oldType = (string) (old('type') ?? 'suggestion'); ?>
<div class="portal-page narrow">

    <!-- Hero Header -->
    <div class="fb-hero">
        <div class="fb-hero__icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </div>
        <div class="fb-hero__text">
            <h1 class="fb-hero__title">Share Feedback</h1>
            <p class="fb-hero__sub">Something broken? An idea? Someone doing great work? Let us know.</p>
        </div>
        <a href="<?= site_url('users/feedback') ?>" class="fb-hero__back">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            My Voice
        </a>
    </div>

    <?php if (session()->has('error')): ?>
        <div class="fb-alert fb-alert--error"><?= esc(session('error')) ?></div>
    <?php endif ?>
    <?php if (session()->has('success')): ?>
        <div class="fb-alert fb-alert--success"><?= esc(session('success')) ?></div>
    <?php endif ?>

    <section class="fb-card">
        <form method="post" action="<?= site_url('users/feedback/submit') ?>" class="fb-form" enctype="multipart/form-data" novalidate>

            <!-- Category -->
            <div class="fb-field">
                <label class="fb-label" for="category_id">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    What's this about?
                </label>
                <select id="category_id" name="category_id" class="fb-select" required>
                    <option value="">Select a category…</option>
                    <?php foreach (($categories ?? []) as $cat): ?>
                        <option value="<?= (int) $cat['id'] ?>" <?= ((string) old('category_id') === (string) $cat['id']) ? 'selected' : '' ?>>
                            <?= esc((string) $cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Type Pills -->
            <div class="fb-field">
                <label class="fb-label">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Type of Feedback
                </label>
                <div class="fb-type-pills" role="group">
                    <?php foreach (['complaint' => ['Complaint','⚡','fb-pill--complaint'], 'suggestion' => ['Suggestion','💡','fb-pill--suggestion'], 'praise' => ['Praise','⭐','fb-pill--praise']] as $value => [$label, $icon, $mod]): ?>
                    <label class="fb-pill <?= $mod ?> <?= $oldType === $value ? 'fb-pill--active' : '' ?>">
                        <input type="radio" name="type" value="<?= $value ?>" <?= $oldType === $value ? 'checked' : '' ?> hidden>
                        <span class="fb-pill__icon"><?= $icon ?></span>
                        <?= $label ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Message -->
            <div class="fb-field">
                <label class="fb-label" for="message">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="17" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="17" y1="18" x2="3" y2="18"/></svg>
                    Your Message
                </label>
                <textarea id="message" name="message" class="fb-textarea" rows="6" required
                    placeholder="Share the details so we can take action…"><?= esc((string) (old('message') ?? '')) ?></textarea>
                <div class="fb-char-hint"><span id="fb-char-count">0</span> / 2000 characters</div>
            </div>

            <!-- Image Upload -->
            <div class="fb-field">
                <label class="fb-label">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    Attach Image <span class="fb-label-hint">optional · JPG, PNG, WebP · max 5 MB</span>
                </label>
                <label class="fb-dropzone" id="fb-dropzone" for="feedback_image">
                    <div class="fb-dropzone__inner" id="fb-drop-inner">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                        <p class="fb-dropzone__text">Drag & drop or <span class="fb-dropzone__link">browse</span></p>
                        <p class="fb-dropzone__hint" id="fb-file-name">No file selected</p>
                    </div>
                    <input id="feedback_image" name="image" type="file" accept="image/jpeg,image/png,image/webp" style="display:none;">
                </label>
            </div>

            <!-- Anonymous Toggle -->
            <label class="fb-anon-row" for="is_anonymous">
                <div class="fb-anon-info">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <div>
                        <span class="fb-anon-title">Submit anonymously</span>
                        <span class="fb-anon-sub">Admins will not see your identity</span>
                    </div>
                </div>
                <div class="fb-toggle">
                    <input id="is_anonymous" name="is_anonymous" type="checkbox" value="1" <?= old('is_anonymous') ? 'checked' : '' ?> class="fb-toggle__input">
                    <span class="fb-toggle__track"></span>
                </div>
            </label>

            <!-- Submit -->
            <div class="fb-actions">
                <button type="submit" class="fb-submit-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Send Feedback
                </button>
            </div>
        </form>
    </section>
</div>

<script>
(function () {
    // Type pill selection
    document.querySelectorAll('.fb-pill').forEach(function (pill) {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.fb-pill').forEach(function (p) { p.classList.remove('fb-pill--active'); });
            pill.classList.add('fb-pill--active');
        });
    });

    // Character counter
    var textarea = document.getElementById('message');
    var counter  = document.getElementById('fb-char-count');
    if (textarea && counter) {
        counter.textContent = textarea.value.length;
        textarea.addEventListener('input', function () {
            counter.textContent = textarea.value.length;
        });
    }

    // File drop zone
    var dropzone = document.getElementById('fb-dropzone');
    var fileInput = document.getElementById('feedback_image');
    var fileName  = document.getElementById('fb-file-name');

    if (fileInput && fileName) {
        fileInput.addEventListener('change', function () {
            fileName.textContent = fileInput.files[0] ? fileInput.files[0].name : 'No file selected';
            if (dropzone) dropzone.classList.toggle('fb-dropzone--chosen', !!fileInput.files[0]);
        });
    }

    if (dropzone) {
        dropzone.addEventListener('dragover', function (e) { e.preventDefault(); dropzone.classList.add('fb-dropzone--drag'); });
        dropzone.addEventListener('dragleave', function () { dropzone.classList.remove('fb-dropzone--drag'); });
        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropzone.classList.remove('fb-dropzone--drag');
            if (e.dataTransfer.files.length && fileInput) {
                fileInput.files = e.dataTransfer.files;
                fileName.textContent = e.dataTransfer.files[0].name;
                dropzone.classList.add('fb-dropzone--chosen');
            }
        });
    }
})();
</script>
<?= $this->endSection() ?>
