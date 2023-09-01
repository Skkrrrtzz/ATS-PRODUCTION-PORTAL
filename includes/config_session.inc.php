<?php
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

session_set_cookie_params(
    1800,         // Lifetime of the cookie in seconds (30 minutes)
    '/',          // Path on the domain where the cookie will be available
    'localhost',  // Domain where the cookie will be available
    true,         // Only transmit the cookie over secure (HTTPS) connections
    true          // Make the cookie httponly and inaccessible to JavaScript
);

session_start();

if (!isset($_SESSION["last_regeneration"])) {
    regenerate_session_id();
} else {
    $interval = 60 * 30;
    if (time() - $_SESSION["last_regeneration"] >= $interval) {
        regenerate_session_id();
    }
}

function regenerate_session_id()
{
    session_regenerate_id();
    $_SESSION["last_regeneration"] = time();
}
