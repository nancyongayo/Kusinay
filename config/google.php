<?php
// Google OAuth 2.0 Configuration
define('GOOGLE_CLIENT_ID',     'Client_ID');
define('GOOGLE_CLIENT_SECRET', 'Client_secret');
define('GOOGLE_REDIRECT_URI',  'http://localhost/KusiNay(Capstone)/oauth2callback.php');

// Session & lockout settings
define('MAX_FAILED_ATTEMPTS', 3);
define('LOCKOUT_MINUTES',     30);
define('OTP_EXPIRY_MINUTES',  3);  // OTP expires in 3 minutes
define('SESSION_TIMEOUT',     18000); // 5 hours idle
