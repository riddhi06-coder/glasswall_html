<?php
/**
 * SMTP + recipient configuration — EXAMPLE / TEMPLATE.
 * Copy to config/mail.php and fill in the real values (config/mail.php is git-ignored).
 */
return [
    'host'        => 'smtp.yourhost.com',
    'username'    => 'noreply@yourdomain.com',
    'password'    => 'YOUR_SMTP_PASSWORD',
    'port'        => 465,
    'encryption'  => 'smtps',                 // 'smtps' (465) or 'tls' (587)
    'from_email'  => 'noreply@yourdomain.com',
    'from_name'   => 'Glass Wall Systems',
    'admin_name'  => 'Glass Wall Systems',

    // Inbox per form (leave a per-form value empty to fall back to admin_email).
    'admin_email'         => 'admin@yourdomain.com',   // default / fallback
    'contact_admin_email' => 'contact@yourdomain.com', // Contact form
    'careers_admin_email' => 'careers@yourdomain.com', // Careers form
];

