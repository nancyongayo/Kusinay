<?php
// Google OAuth 2.0 Configuration
define('GOOGLE_CLIENT_ID',     'CLIENT_ID' );
define('GOOGLE_CLIENT_SECRET', 'CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI',  'https://kusinayapp.freehosting.dev/oauth2callback.php');

// Session & lockout settings
define('MAX_FAILED_ATTEMPTS', 3);
define('LOCKOUT_MINUTES',     30);
define('OTP_EXPIRY_MINUTES',  3);  // OTP expires in 3 minutes
define('SESSION_TIMEOUT',     18000); // 5 hours idle
