<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page narrow">
    <div class="portal-welcome">
        <div>
            <h2>Submit Feedback</h2>
            <p class="muted">Your voice matters. Share a complaint, suggestion, or praise.</p>
        </div>
        <a href="<?= site_url('users/feedback') ?>" class="link-more">Back to My Feedback</a>
    </div>

    <section class="portal-card">
        <form method="post" action="<?= site_url('users/feedback/submit') ?>" class="portal-form">
            <label for="category_id">Category</label>
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

            <label for="subject">Subject</label>
            <input id="subject" name="subject" type="text" required maxlength="180" placeholder="Brief subject"
                value="<?= esc((string) (old('subject') ?? '')) ?>">

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="8" required placeholder="Describe your feedback clearly."><?= esc((string) (old('message') ?? '')) ?></textarea>

            <label class="checkbox-row" for="is_anonymous">
                <input id="is_anonymous" name="is_anonymous" type="checkbox" value="1" <?= old('is_anonymous') ? 'checked' : '' ?>>
                Submit anonymously (admins will not see your identity)
            </label>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Submit Feedback</button>
            </div>
        </form>
    </section>
</div>
<?= $this->endSection() ?>
