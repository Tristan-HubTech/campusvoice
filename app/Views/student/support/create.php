<?= $this->extend('student/layout') ?>
<?= $this->section('content') ?>
<style>
/* ── Student Support Create ── */
.sup-create-page { max-width: 640px; margin: 0 auto; padding: 2rem 1.25rem; }

.sup-back-link {
    display: inline-flex; align-items: center; gap: .35rem;
    color: var(--ink-soft); font-size: .82rem; text-decoration: none;
    margin-bottom: 1.5rem; transition: color .15s;
}
.sup-back-link:hover { color: var(--ink); text-decoration: none; }

.sup-create-card {
    background: var(--surface); border: 1px solid var(--line);
    border-radius: 16px; padding: 2rem;
    box-shadow: 0 2px 12px rgba(10,27,66,.06);
}
.sup-create-title {
    font-size: 1.35rem; font-weight: 700; color: var(--ink);
    margin: 0 0 .35rem; letter-spacing: -.025em;
}
.sup-create-subtitle { font-size: .84rem; color: var(--ink-soft); margin: 0 0 1.75rem; }

.sup-field { margin-bottom: 1.25rem; }
.sup-label {
    display: flex; align-items: center; gap: .3rem;
    font-size: .81rem; font-weight: 600; color: var(--ink);
    margin-bottom: .42rem;
}
.sup-label-req { color: #ef4444; font-weight: 700; }

.sup-input, .sup-textarea {
    width: 100%; padding: .65rem .9rem;
    border: 1.5px solid var(--line);
    border-radius: 10px;
    background: var(--bg); color: var(--ink);
    font-size: .88rem; box-sizing: border-box;
    transition: border-color .15s, box-shadow .15s;
    outline: none; font-family: inherit;
}
.sup-input::placeholder, .sup-textarea::placeholder { color: var(--ink-soft); opacity: .7; }
.sup-input:focus, .sup-textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(10,27,66,.1);
    background: var(--surface);
}
.sup-textarea { resize: vertical; min-height: 148px; line-height: 1.65; }

/* Category chips */
.sup-cat-chips { display: flex; gap: .45rem; flex-wrap: wrap; }
.sup-cat-radio { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
.sup-cat-label {
    display: inline-flex; align-items: center;
    padding: .38rem .9rem;
    border: 1.5px solid var(--line);
    border-radius: 999px; cursor: pointer;
    font-size: .79rem; font-weight: 600; color: var(--ink-soft);
    background: var(--bg);
    transition: border-color .15s, background .15s, color .15s;
    user-select: none;
}
.sup-cat-radio:checked + .sup-cat-label {
    border-color: var(--primary);
    background: rgba(10,27,66,.07);
    color: var(--ink);
}
.sup-cat-label:hover { border-color: var(--primary); color: var(--ink); }

.sup-divider { border: none; border-top: 1px solid var(--line); margin: 1.5rem 0; }

.sup-submit-btn {
    width: 100%; padding: .72rem;
    background: var(--primary); color: #fff;
    border: none; border-radius: 10px;
    font-size: .9rem; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: .45rem;
    transition: opacity .15s, transform .12s;
    font-family: inherit;
}
.sup-submit-btn:hover { opacity: .88; transform: translateY(-1px); }
.sup-submit-btn:active { transform: translateY(0); opacity: .82; }

/* ── Dark mode overrides ── */
html[data-theme="dark"] .sup-back-link { color: #8aaad0; }
html[data-theme="dark"] .sup-back-link:hover { color: #e0eaff; }
html[data-theme="dark"] .sup-create-card { background: #131b2e; border-color: #1e2d4a; box-shadow: 0 4px 24px rgba(0,0,0,.3); }
html[data-theme="dark"] .sup-create-title { color: #e0eaff; }
html[data-theme="dark"] .sup-create-subtitle { color: #8aaad0; }
html[data-theme="dark"] .sup-label { color: #c8d8f0; }
html[data-theme="dark"] .sup-input,
html[data-theme="dark"] .sup-textarea {
    background: #0f1a30; border-color: #1e2d4a;
    color: #e0eaff;
}
html[data-theme="dark"] .sup-input::placeholder,
html[data-theme="dark"] .sup-textarea::placeholder { color: #4a5f80; opacity: 1; }
html[data-theme="dark"] .sup-input:focus,
html[data-theme="dark"] .sup-textarea:focus {
    border-color: #5080ff; background: #131b2e;
    box-shadow: 0 0 0 3px rgba(80,128,255,.15);
}
html[data-theme="dark"] .sup-cat-label {
    background: #0f1a30; border-color: #2a3d60; color: #8aaad0;
}
html[data-theme="dark"] .sup-cat-radio:checked + .sup-cat-label {
    border-color: #5080ff; background: rgba(80,128,255,.15); color: #c0d5ff;
}
html[data-theme="dark"] .sup-cat-label:hover { border-color: #5080ff; color: #c0d5ff; }
html[data-theme="dark"] .sup-divider { border-color: #1e2d4a; }
html[data-theme="dark"] .sup-submit-btn { background: #1e3a72; }
html[data-theme="dark"] .sup-submit-btn:hover { background: #243f7d; }
</style>

<div class="sup-create-page">
    <a href="<?= site_url('users/support') ?>" class="sup-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Back to tickets
    </a>

    <div class="sup-create-card">
        <h1 class="sup-create-title">Submit a Support Ticket</h1>
        <p class="sup-create-subtitle">We typically respond within 24 hours on business days.</p>

        <form action="<?= site_url('users/support') ?>" method="post">
            <?= csrf_field() ?>

            <div class="sup-field">
                <label class="sup-label">Subject <span class="sup-label-req">*</span></label>
                <input type="text" name="subject" class="sup-input"
                    value="<?= esc(old('subject', '')) ?>"
                    required maxlength="200"
                    placeholder="Brief description of your issue">
            </div>

            <div class="sup-field">
                <label class="sup-label">Category <span class="sup-label-req">*</span></label>
                <div class="sup-cat-chips">
                    <?php $oldCat = old('category', 'general');
                    foreach (['general' => 'General', 'technical' => 'Technical', 'account' => 'Account', 'other' => 'Other'] as $val => $lbl): ?>
                    <input type="radio" name="category" id="cat_<?= $val ?>"
                        value="<?= $val ?>" class="sup-cat-radio"
                        <?= $oldCat === $val ? 'checked' : '' ?>>
                    <label for="cat_<?= $val ?>" class="sup-cat-label"><?= $lbl ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="sup-field">
                <label class="sup-label">Message <span class="sup-label-req">*</span></label>
                <textarea name="message" class="sup-textarea"
                    required minlength="10"
                    placeholder="Describe your issue in detail — the more context, the faster we can help."><?= esc(old('message', '')) ?></textarea>
            </div>

            <hr class="sup-divider">

            <button type="submit" class="sup-submit-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Submit Ticket
            </button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
