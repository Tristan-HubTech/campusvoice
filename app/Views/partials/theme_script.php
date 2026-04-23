<?php
$themeJsPath = FCPATH . 'assets/campusvoice-theme.js';
$themeJsVer  = is_file($themeJsPath) ? (string) filemtime($themeJsPath) : '1';
?>
<script src="<?= base_url('assets/campusvoice-theme.js') ?>?v=<?= $themeJsVer ?>" defer></script>
