<?php
/** Careers form handler: validate (incl. resume) -> store -> email admin (with resume) + user. */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/form_helpers.php';
require_once __DIR__ . '/includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['ok' => false, 'message' => 'Invalid request.']);
}

$first = clean_val($_POST['first_name'] ?? '');
$last  = clean_val($_POST['last_name']  ?? '');
$email = clean_val($_POST['email']      ?? '');
$phone = clean_val($_POST['contact_no'] ?? '');
$msg   = clean_val($_POST['message']    ?? '');
$role  = clean_val($_POST['job_role']   ?? '');
if ($role === '') { $role = 'General Application'; }

$MAX_BYTES   = 3 * 1024 * 1024;                 // 3 MB
$ALLOWED_EXT = ['pdf', 'doc', 'docx'];

$errors = [];
if (!valid_name($first))     $errors['first_name'] = 'Please enter a valid first name (letters only).';
if (!valid_name($last))      $errors['last_name']  = 'Please enter a valid last name (letters only).';
if (!valid_email($email))    $errors['email']      = 'Please enter a valid email address.';
if (!valid_phone10($phone))  $errors['contact_no'] = 'Please enter a valid 10-digit contact number.';

// ---- Resume validation ----
$file = $_FILES['resume'] ?? null;
$ext  = '';
if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
    $errors['resume'] = 'Please upload your resume.';
} elseif ($file['error'] !== UPLOAD_ERR_OK) {
    $errors['resume'] = 'Resume upload failed. Please try again.';
} else {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $ALLOWED_EXT, true)) {
        $errors['resume'] = 'Resume must be a PDF, DOC or DOCX file.';
    } elseif ($file['size'] > $MAX_BYTES) {
        $errors['resume'] = 'Resume is too large (max 3 MB).';
    } elseif ($file['size'] <= 0) {
        $errors['resume'] = 'The uploaded file is empty.';
    }
}

if ($errors) {
    json_out(['ok' => false, 'errors' => $errors]);
}

// ---- Save resume ----
$safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
$safeBase = substr($safeBase, 0, 60);
$stored   = uniqid('resume_', true) . '.' . $ext;
$relPath  = 'uploads/resumes/' . $stored;
$absPath  = __DIR__ . '/' . $relPath;

if (!move_uploaded_file($file['tmp_name'], $absPath)) {
    json_out(['ok' => false, 'message' => 'Could not save your resume. Please try again.']);
}

// ---- Store ----
try {
    $stmt = db()->prepare(
        'INSERT INTO career_applications (job_role, first_name, last_name, email, contact_no, message, resume_path, resume_name, ip_address)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$role, $first, $last, $email, $phone, $msg, $relPath, $file['name'], client_ip()]);
} catch (Throwable $e) {
    json_out(['ok' => false, 'message' => 'Could not save your application. Please try again.']);
}

// ---- Emails ----
$cfg = mail_config();
$fullName  = trim("$first $last");
$submitted = date('d M Y, h:i A');

try {
    // Admin notification (with resume attached)
    $admin = make_mailer();
    $adminTo = !empty($cfg['careers_admin_email']) ? $cfg['careers_admin_email'] : $cfg['admin_email'];
    $admin->addAddress($adminTo, $cfg['admin_name']);
    $admin->addReplyTo($email, $fullName);
    $admin->Subject = 'New Job Application (' . $role . ') — ' . $fullName;
    $admin->isHTML(true);
    $admin->addAttachment($absPath, $file['name']);
    $admin->Body = render_email(
        'New Job Application',
        'A new job application has been submitted through the website careers form. The resume is attached.',
        [
            'Name'      => $fullName,
            'Email'     => $email,
            'Contact'   => $phone,
            'Message'   => ($msg !== '' ? $msg : '—'),
            'Resume'    => $file['name'],
            'Submitted' => $submitted,
        ],
        '',
        ['label' => 'Position Applied For', 'value' => $role]
    );
    $admin->send();

    // User confirmation
    $user = make_mailer();
    $user->addAddress($email, $fullName);
    $user->Subject = 'Your application has been received — Glass Wall Systems';
    $user->isHTML(true);
    $user->Body = render_email(
        'Application Received',
        "Hi $first,\n\nThank you for applying to Glass Wall Systems. We have successfully received your application and resume. Our HR team will review your profile and reach out if there is a suitable match.",
        [],
        'This is an automated confirmation of your application. There is no need to reply to this email.',
        ['label' => 'Position Applied For', 'value' => $role]
    );
    $user->send();
} catch (Throwable $e) {
    // Application is saved; ignore email delivery issues for the user response.
}

json_out(['ok' => true, 'message' => 'Thank you! Your application has been submitted successfully.']);
