<?= $this->extend('student/layout') ?>

<?= $this->section('content') ?>
<div class="portal-page narrow">
    <div class="portal-welcome" style="background: var(--glass-bg); color: var(--ink); box-shadow: 0 4px 24px rgba(47,107,255,0.06);">
        <div style="display: flex; align-items: center; gap: 16px;">
            <span style="font-size: 2.2rem; background: #e6f0f8; border-radius: 16px; padding: 8px 14px; display: flex; align-items: center; justify-content: center;">
                <svg width="32" height="32" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#2f6bff"/><path d="M12 7v5" stroke="#fff" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="16" r="1.2" fill="#fff"/></svg>
            </span>
            <div>
                <h2 style="margin:0; color:var(--primary);">Campus Announcements</h2>
                <p class="muted" style="color:var(--ink-soft);">Latest updates from the administration.</p>
            </div>
        </div>
    </div>

    <section style="background: var(--surface); box-shadow: 0 6px 32px rgba(47,107,255,0.08); border-radius: 18px; padding: 28px 18px 22px 18px; border: none; margin-top: 18px;">
        <?php if (! empty($announcements)): ?>
            <ul class="announce-list-full">
                <?php foreach ($announcements as $announcement): ?>
                    <li style="background: #fff; border-radius: 16px; border: 1.5px solid #d6e3ef; box-shadow: 0 4px 24px rgba(47,107,255,0.06); padding: 22px 26px 16px 26px; margin-bottom: 10px; position:relative; list-style:none; border-left: 5px solid #2f6bff;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                            <span style="font-size:1.15rem; color:#2f6bff; display:flex; align-items:center;">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#2f6bff"/><path d="M12 7v5" stroke="#fff" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="16" r="1.2" fill="#fff"/></svg>
                            </span>
                            <h3 style="margin:0; color:#174a7c; font-size:1.18rem; font-weight:800; letter-spacing:0.01em;">Announcement</h3>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p style="margin:0; color:#17324a; font-size:1.07rem; line-height:1.6; font-weight:500;">
                                <?= nl2br(esc((string) ($announcement['body'] ?? ''))) ?>
                            </p>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; margin-top:8px;">
                            <span style="font-size:0.97rem; color:#4a6a84; display:flex; align-items:center; gap:4px;">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M7 8V6a5 5 0 0 1 10 0v2" stroke="#2f6bff" stroke-width="1.5"/><rect x="4" y="8" width="16" height="12" rx="3" stroke="#2f6bff" stroke-width="1.5"/><circle cx="12" cy="14" r="2.5" stroke="#2f6bff" stroke-width="1.5"/></svg>
                                Published: <span><?= esc(date('M d, Y H:i', strtotime((string) ($announcement['publish_at'] ?? $announcement['created_at'] ?? 'now')))) ?></span>
                            </span>
                        </div>
                    </li>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted empty-hint">No announcements found.</p>
        <?php endif; ?>
    </section>
</div>
<?= $this->endSection() ?>
