<?php
/** Contact form handler: validate -> store -> email admin + user. Returns JSON. */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/form_helpers.php';
require_once __DIR__ . '/includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['ok' => false, 'message' => 'Invalid request.']);
}

$name    = clean_val($_POST['name']    ?? '');
$email   = clean_val($_POST['email']   ?? '');
$company = clean_val($_POST['company'] ?? '');
$phone   = clean_val($_POST['phone']   ?? '');
$message = clean_val($_POST['message'] ?? '');

// ---- Server-side validation (mirrors the JS) ----
$errors = [];
if (!valid_name($name))        $errors['name']    = 'Please enter a valid name (letters only).';
if (!valid_email($email))      $errors['email']   = 'Please enter a valid email address.';
if ($company === '')           $errors['company'] = 'Please enter your company.';
if (!valid_phone10($phone))    $errors['phone']   = 'Please enter a valid 10-digit phone number.';
if (mb_strlen($message) < 5)   $errors['message'] = 'Please enter your message.';

if ($errors) {
    json_out(['ok' => false, 'errors' => $errors]);
}

// Prefix the country code for storage + emails (input holds the 10 digits).
$phone = '+91 ' . $phone;

// ---- Store ----
try {
    $stmt = db()->prepare(
        'INSERT INTO contact_submissions (name, email, company, phone, message, ip_address)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$name, $email, $company, $phone, $message, client_ip()]);
} catch (Throwable $e) {
    json_out(['ok' => false, 'message' => 'Could not save your message. Please try again.']);
}

// ---- Emails ----
$cfg = mail_config();
$submitted = date('d M Y, h:i A');

try {
    // Admin notification
    $admin = make_mailer();
    $adminTo = !empty($cfg['contact_admin_email']) ? $cfg['contact_admin_email'] : $cfg['admin_email'];
    $admin->addAddress($adminTo, $cfg['admin_name']);
    $admin->addReplyTo($email, $name);
    $admin->Subject = 'New Contact Enquiry — ' . $name;
    $admin->isHTML(true);
    $admin->Body = render_email(
        'New Contact Enquiry',
        'You have received a new enquiry through the website contact form.',
        [
            'Name'      => $name,
            'Email'     => $email,
            'Company'   => $company,
            'Phone'     => $phone,
            'Message'   => $message,
            'Submitted' => $submitted,
        ]
    );
    $admin->send();

    // User confirmation
    $user = make_mailer();
    $user->addAddress($email, $name);
    $user->Subject = 'We have received your enquiry — Glass Wall Systems';
    $user->isHTML(true);
    $user->Body = render_email(
        'Thank you for contacting us',
        "Hi $name,\n\nThank you for reaching out to Glass Wall Systems. We have received your enquiry and our team will get back to you shortly.",
        ['Your Message' => $message],
        'This is a confirmation that your enquiry has been received. There is no need to reply to this email.'
    );
    $user->send();
} catch (Throwable $e) {
    // Data is saved; email delivery issues shouldn't fail the user.
}

json_out(['ok' => true, 'message' => 'Thank you! Your message has been sent successfully.']);
