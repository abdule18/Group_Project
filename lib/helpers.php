<?php
function redirect($path) {
    header('Location: ' . $path);
    exit;
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
