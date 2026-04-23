<?php
$toggleClass = $toggleClass ?? '';
?>
<button type="button" class="theme-toggle <?= esc($toggleClass, 'attr') ?>" data-theme-toggle aria-pressed="false" aria-label="Switch theme" title="Theme">
    <span class="theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true">☀</span>
    <span class="theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true">☽</span>
</button>
