<?php
/**
 * Mail helper — configures PHPMailer (SMTP) and renders a branded HTML email.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

function mail_config(): array
{
    return require __DIR__ . '/../config/mail.php';
}

/**
 * Build a ready-to-use PHPMailer instance (SMTP + logo embedded as "gwslogo").
 */
function make_mailer(): PHPMailer
{
    $cfg = mail_config();
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = $cfg['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $cfg['username'];
    $mail->Password   = $cfg['password'];
    $mail->SMTPSecure = $cfg['encryption'] === 'tls'
        ? PHPMailer::ENCRYPTION_STARTTLS
        : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = (int) $cfg['port'];
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($cfg['from_email'], $cfg['from_name']);

    // Embed the company logo so it renders inline in the email.
    $logo = __DIR__ . '/../assets/images/gws.png';
    if (is_file($logo)) {
        $mail->addEmbeddedImage($logo, 'gwslogo', 'gws-logo.png');
    }

    return $mail;
}

/**
 * Render a professional HTML email.
 *
 * @param string $heading   Big heading under the logo bar.
 * @param string $intro     Intro paragraph (plain text; may include line breaks).
 * @param array  $rows      Ordered [label => value] detail rows (values shown as text).
 * @param string $note      Optional closing note paragraph.
 */
function render_email(string $heading, string $intro, array $rows = [], string $note = '', ?array $highlight = null): string
{
    $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };

    // Optional highlighted callout (e.g. the job role).
    $highlightHtml = '';
    if ($highlight && !empty($highlight['value'])) {
        $highlightHtml = '
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0 6px;">
          <tr><td style="background:#eef4ff;border:1px solid #d4e3ff;border-left:4px solid #0a4bb3;border-radius:8px;padding:14px 18px;">
            <span style="display:block;font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">' . $esc($highlight['label'] ?? 'Details') . '</span>
            <span style="display:block;margin-top:4px;font-size:19px;color:#0a2540;font-weight:700;">' . $esc($highlight['value']) . '</span>
          </td></tr>
        </table>';
    }

    $rowsHtml = '';
    foreach ($rows as $label => $value) {
        if ($value === null || $value === '') { $value = '—'; }
        $rowsHtml .= '
            <tr>
              <td style="padding:12px 16px;border-bottom:1px solid #eef0f4;font-size:13px;color:#6b7280;font-weight:600;width:40%;vertical-align:top;">' . $esc($label) . '</td>
              <td style="padding:12px 16px;border-bottom:1px solid #eef0f4;font-size:14px;color:#111827;vertical-align:top;">' . nl2br($esc($value)) . '</td>
            </tr>';
    }
    $detailTable = $rowsHtml === '' ? '' : '
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eef0f4;border-radius:10px;overflow:hidden;margin:8px 0 4px;">
          ' . $rowsHtml . '
        </table>';

    $noteHtml = $note === '' ? '' : '<p style="margin:20px 0 0;font-size:14px;line-height:1.6;color:#4b5563;">' . nl2br($esc($note)) . '</p>';

    return '<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f6f9;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:28px 12px;">
    <tr><td align="center">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 6px 24px rgba(16,24,40,.08);font-family:Arial,Helvetica,sans-serif;">

        <!-- Logo bar -->
        <tr>
          <td style="background:#0a2540;padding:22px 28px;text-align:center;">
            <img src="cid:gwslogo" alt="Glass Wall Systems" height="42" style="height:42px;display:inline-block;">
          </td>
        </tr>

        <!-- Accent strip -->
        <tr><td style="height:4px;background:#0a4bb3;line-height:4px;font-size:0;">&nbsp;</td></tr>

        <!-- Body -->
        <tr>
          <td style="padding:32px 28px 28px;">
            <h1 style="margin:0 0 14px;font-size:20px;line-height:1.3;color:#0a2540;">' . $esc($heading) . '</h1>
            <p style="margin:0 0 6px;font-size:14px;line-height:1.6;color:#4b5563;">' . nl2br($esc($intro)) . '</p>
            ' . $highlightHtml . '
            ' . $detailTable . '
            ' . $noteHtml . '
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#f8fafc;border-top:1px solid #eef0f4;padding:22px 28px;">
            <p style="margin:0 0 6px;font-size:13px;color:#0a2540;font-weight:700;">Glass Wall Systems</p>
            <p style="margin:0;font-size:12px;line-height:1.6;color:#8a94a6;">
              503-504, 5th Floor, A Wing, Marathon Futurex, Mafatlal Mills Compound,<br>
              N.M. Joshi Marg, Lower Parel (E), Mumbai 400 013<br>
              Email: info@glasswallsystems.in &nbsp;|&nbsp; Phone: +91 22 6103 3456
            </p>
            <p style="margin:14px 0 0;font-size:11px;color:#b6bdc8;">This is an automated message from the Glass Wall Systems website.</p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body></html>';
}
