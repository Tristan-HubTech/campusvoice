<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page narrow">
    <div class="portal-welcome">
        <div>
            <h2>Share Feedback</h2>
            <p class="muted">Something broken? An idea? Someone doing great work? Let us know.</p>
        </div>
        <a href="<?= site_url('users/feedback') ?>" class="link-more">Back to My Voice</a>
    </div>

    <section class="portal-card">
        <form method="post" action="<?= site_url('users/feedback/submit') ?>" class="portal-form" enctype="multipart/form-data">
            <label for="category_id">What's this about?</label>
            <select id="category_id" name="category_id" required>
                <option value="">Select category</option>
                <?php foreach (($categories ?? []) as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= ((string) old('category_id') === (string) $cat['id']) ? 'selected' : '' ?>>
                        <?= esc((string) $cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="type">Type</label>
            <select id="type" name="type" required>
                <?php
                $oldType = (string) (old('type') ?? 'suggestion');
                foreach (['complaint' => 'Complaint', 'suggestion' => 'Suggestion', 'praise' => 'Praise'] as $value => $label):
                ?>
                    <option value="<?= $value ?>" <?= $oldType === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>

            <label for="message">Your Message</label>
            <textarea id="message" name="message" rows="8" required placeholder="Share the details so we can take action."><?= esc((string) (old('message') ?? '')) ?></textarea>

            <label for="feedback_image">Image <span class="muted">(optional, JPG, PNG, or WebP, max 5 MB)</span></label>
            <div class="feedback-image-field">
                <input id="feedback_image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="feedback-file-input">
            </div>

            <label class="checkbox-row" for="is_anonymous">
                <input id="is_anonymous" name="is_anonymous" type="checkbox" value="1" <?= old('is_anonymous') ? 'checked' : '' ?>>
                Submit anonymously (admins will not see your identity)
            </label>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Share</button>
            </div>
        </form>
    </section>
</div>
<?= $this->endSection() ?>
