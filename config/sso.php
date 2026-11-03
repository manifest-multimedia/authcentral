<?php

$collegeCallback = rtrim(env('CIS_URL', 'https://college.pnmtc.edu.gh'), '/').'/auth/callback';

return [
    // Preserve the existing configured College integration; additional clients require an explicit allowlist.
    'allowed_callbacks' => array_values(array_filter(array_map('trim', explode(',', env('SSO_ALLOWED_CALLBACKS', $collegeCallback))))),
    'college_callback' => env('COLLEGE_CALLBACK_URL', $collegeCallback),
];
