<?php
function sanitize_text($s) {
    $s = trim($s ?? '');
    // Strip dangerous tags
    return strip_tags($s);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
