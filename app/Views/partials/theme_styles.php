<?php
$themeCssPath = FCPATH . 'assets/campusvoice-theme.css';
$themeCssVer  = is_file($themeCssPath) ? (string) filemtime($themeCssPath) : '1';
?>
<link rel="stylesheet" href="<?= base_url('assets/campusvoice-theme.css') ?>?v=<?= $themeCssVer ?>">
