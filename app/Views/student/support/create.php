<?= $this->extend('student/layout') ?>
<?= $this->section('content') ?>
<style>
/* ── Student Support Create ── */
.sup-create-page { max-width: 620px; margin: 0 auto; padding: 2rem 1.25rem 3rem; }

.sup-back-link {
    display: inline-flex; align-items: center; gap: .35rem;
    color: var(--ink-soft); font-size: .82rem; text-decoration: none;
    margin-bottom: 1.75rem; transition: color .15s;
}
.sup-back-link:hover { color: var(--ink); text-decoration: none; }

.sup-create-card {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 20px; padding: 2.25rem 2rem;
    box-shadow: 0 8px 40px rgba(5,15,50,.3);
    backdrop-filter: blur(6px);
    position: relative; overflow: hidden;
}
.sup-create-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, transparent, #3b82f6 30%, #7aaeff 60%, #d4a83a, transparent);
    opacity: .8;
}

.sup-create-title {
    font-size: 1.4rem; font-weight: 700; color: var(--ink);
    margin: 0 0 .3rem; letter-spacing: -.03em;
    font-family: 'Fraunces', Georgia, serif;
}
.sup-create-subtitle { font-size: .83rem; color: var(--ink-soft); margin: 0 0 2rem; }

.sup-field { margin-bottom: 1.35rem; }
.sup-label {
    display: flex; align-items: center; gap: .3rem;
    font-size: .8rem; font-weight: 700; color: var(--ink);
    margin-bottom: .45rem; letter-spacing: .01em;
}
.sup-label-req { color: #ef4444; }

.sup-input, .sup-textarea {
    width: 100%; padding: .68rem .95rem;
    border: 1.5px solid rgba(255,255,255,.12);
    border-radius: 11px;
    background: rgba(255,255,255,.05); color: var(--ink);
    font-size: .88rem; box-sizing: border-box;
    transition: border-color .18s, box-shadow .18s, background .18s;
    outline: none; font-family: inherit;
}
.sup-input::placeholder, .sup-textarea::placeholder { color: var(--ink-soft); opacity: .55; }
.sup-input:focus, .sup-textarea:focus {
    border-color: #5080ff;
    box-shadow: 0 0 0 3px rgba(80,128,255,.15);
    background: rgba(80,128,255,.05);
}
.sup-textarea { resize: vertical; min-height: 152px; line-height: 1.65; }

/* Category chips */
.sup-cat-chips { display: flex; gap: .4rem; flex-wrap: wrap; }
.sup-cat-radio { position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; }
.sup-cat-label {
    display: inline-flex; align-items: center;
    padding: .36rem .9rem;
    border: 1.5px solid rgba(255,255,255,.15);
    border-radius: 999px; cursor: pointer;
    font-size: .78rem; font-weight: 600; color: var(--ink-soft);
    background: rgba(255,255,255,.04);
    transition: border-color .15s, background .15s, color .15s;
    user-select: none;
}
.sup-cat-radio:checked + .sup-cat-label {
    border-color: #5080ff;
    background: rgba(80,128,255,.18);
    color: #a8c8ff;
    box-shadow: 0 0 0 3px rgba(80,128,255,.12);
}
.sup-cat-label:hover { border-color: rgba(80,128,255,.5); color: var(--ink); }

.sup-divider { border: none; border-top: 1px solid rgba(255,255,255,.08); margin: 1.5rem 0; }

.sup-submit-btn {
    width: 100%; padding: .78rem;
    background: linear-gradient(135deg, #1a3a72, #1f4a90);
    color: #fff; border: 1px solid rgba(100,160,255,.25);
    border-radius: 11px;
    font-size: .9rem; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    transition: opacity .15s, transform .12s, box-shadow .15s;
    font-family: inherit;
    box-shadow: 0 4px 14px rgba(20,60,150,.35);
    letter-spacing: .01em;
}
.sup-submit-btn:hover { opacity: .9; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(20,60,150,.45); }
.sup-submit-btn:active { transform: translateY(0); }

/* Light mode */
html:not([data-theme="dark"]) .sup-create-card { background: #fff; border-color: #dde8f8; box-shadow: 0 4px 24px rgba(14,32,80,.08); }
html:not([data-theme="dark"]) .sup-create-card::before { opacity: .5; }
html:not([data-theme="dark"]) .sup-create-title { color: #0d1e42; }
html:not([data-theme="dark"]) .sup-input,
html:not([data-theme="dark"]) .sup-textarea { background: #f8faff; border-color: #c8d8f0; color: #0d1e42; }
html:not([data-theme="dark"]) .sup-input::placeholder,
html:not([data-theme="dark"]) .sup-textarea::placeholder { color: #7888a8; opacity: 1; }
html:not([data-theme="dark"]) .sup-input:focus,
html:not([data-theme="dark"]) .sup-textarea:focus { border-color: #2558c8; background: #fff; box-shadow: 0 0 0 3px rgba(37,88,200,.1); }
html:not([data-theme="dark"]) .sup-cat-label { background: #f4f7ff; border-color: #c8d8f0; color: #4f6ea7; }
html:not([data-theme="dark"]) .sup-cat-radio:checked + .sup-cat-label { border-color: #2558c8; background: #dbeafe; color: #1e3a8a; box-shadow: 0 0 0 3px rgba(37,88,200,.1); }
html:not([data-theme="dark"]) .sup-cat-label:hover { border-color: #2558c8; color: #0d1e42; }
html:not([data-theme="dark"]) .sup-divider { border-color: #dde8f8; }
html:not([data-theme="dark"]) .sup-submit-btn { background: linear-gradient(135deg, #1a3a72, #235296); }
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
                    required maxlength="200" autocomplete="off"
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
                    required minlength="10" autocomplete="off"
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
