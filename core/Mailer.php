<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/google.php';

class Mailer {
    // Gmail SMTP Configuration - Updated approach
    private string $smtpUser = 'nancyongayo24@gmail.com';
    private string $smtpPass = 'zrmx qfex xwwk svrl'; // Original app password - let's try this first

    private function buildMailer(): PHPMailer {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        // Gmail SMTP Settings
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $this->smtpUser;
        $mail->Password   = $this->smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom($this->smtpUser, 'KusiNay System');
        $mail->isHTML(true);
        
        // Debug mode disabled - uncomment below lines if you need to troubleshoot email issues
        // $mail->SMTPDebug = 2; // 0=off, 1=client, 2=client+server
        // $mail->Debugoutput = function($str, $level) { error_log("SMTP Debug level $level: $str"); };
        
        return $mail;
    }

    public function sendVerificationEmail(string $toEmail, string $toName, string $token): bool {
        $link = 'https://kusinayapp.freehosting.dev/verify_success.php?token=' . urlencode($token);
        $mail = $this->buildMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = 'KusiNay – Verify Your Email Address';
        
        // Simple version - use emailTemplate like other working methods
        $mail->Body = $this->emailTemplate(
            'Verify Your Email',
            "Hi <strong>" . htmlspecialchars($toName) . "</strong>,<br><br>
             Thank you for registering with KusiNay. Click the button below to verify your email address and activate your account.",
            $link,
            'Verify My Email'
        );
        
        return $this->send($mail);
    }

    public function sendOTPEmail(string $toEmail, string $toName, string $otp): bool {
        $mail = $this->buildMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = 'KusiNay – Your One-Time Password (OTP)';
        $mail->Body    = $this->emailTemplate(
            'Your Login OTP',
            "Hi <strong>" . htmlspecialchars($toName) . "</strong>,<br><br>
             Use the code below to complete your login. This code expires in <strong>" . OTP_EXPIRY_MINUTES . " minutes</strong>.
             <div style='margin:28px auto;text-align:center'>
               <div style='display:inline-block;background:#3D4A1E;color:#F5EDD6;font-size:2.4rem;font-weight:900;
                           letter-spacing:0.45em;padding:18px 36px;border-radius:12px;
                           border:3px solid #C4722A'>$otp</div>
             </div>
             <p style='text-align:center;color:#888;font-size:.85rem'>
               Do not share this code with anyone. If you did not request this, ignore this email.
             </p>",
            null, null
        );
        return $this->send($mail);
    }

    public function sendPasswordResetEmail(string $toEmail, string $toName, string $token): bool {
        $link = 'https://kusinayapp.freehosting.dev/index.php?action=resetPassword&token=' . urlencode($token);
        $mail = $this->buildMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = 'KusiNay – Password Reset Request';
        $mail->Body    = $this->emailTemplate(
            'Reset Your Password',
            "Hi <strong>" . htmlspecialchars($toName) . "</strong>,<br><br>
             We received a request to reset your password. Click the button below — this link expires in <strong>1 hour</strong>.<br><br>
             If you did not request a password reset, you can safely ignore this email.",
            $link,
            'Reset My Password'
        );
        return $this->send($mail);
    }

    /**
     * Sends a magic-link invite email to a BNS-registered resident.
     * No password is shown — resident clicks the link to set their own password.
     */
    public function sendResidentInviteEmail(string $toEmail, string $toName, string $setupToken): bool {
        $setupUrl = 'https://kusinayapp.freehosting.dev/index.php?action=setupAccount&token=' . urlencode($setupToken);
        $mail = $this->buildMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = 'KusiNay – Set Up Your Account';
        $mail->Body    = $this->emailTemplate(
            'Your KusiNay Account is Ready',
            "Hi <strong>" . htmlspecialchars($toName) . "</strong>,<br><br>
             Your Barangay Nutrition Scholar has registered you in the <strong>KusiNay</strong> Smart Meal Planning &amp; Nutrition system.<br><br>
             Click the button below to set up your password and access your account. This link is valid for <strong>7 days</strong>.<br><br>
             <p style='font-size:.85rem;color:#888;margin-top:16px'>
               If you did not expect this email, you can safely ignore it.
             </p>",
            $setupUrl,
            'Set Up My Account →'
        );
        return $this->send($mail);
    }

    /**
     * Sends login credentials to a resident registered by a BNS Staff member.
     * Kept for backward compatibility — new flow uses sendResidentInviteEmail.
     */
    public function sendResidentCredentialsEmail(string $toEmail, string $toName, string $tempPassword): bool {
        $loginUrl = 'https://kusinayapp.freehosting.dev/index.php?action=login';
        $mail = $this->buildMailer();
        $mail->addAddress($toEmail, $toName);
        $mail->Subject = 'KusiNay – Your Account Credentials';
        $mail->Body    = $this->emailTemplate(
            'Your KusiNay Account is Ready',
            "Hi <strong>" . htmlspecialchars($toName) . "</strong>,<br><br>
             Your Barangay Nutrition Scholar has registered you in the KusiNay system.
             Use the credentials below to log in for the first time.<br><br>
             <table style='width:100%;border-collapse:collapse;font-size:.95rem;margin:16px 0'>
               <tr>
                 <td style='padding:6px 0;color:#888;width:40%'>Email address:</td>
                 <td style='padding:6px 0;font-weight:600;color:#3D4A1E'>" . htmlspecialchars($toEmail) . "</td>
               </tr>
             </table>
             <div style='margin:20px auto;text-align:center'>
               <div style='font-size:.78rem;color:#888;margin-bottom:8px;text-transform:uppercase;letter-spacing:.06em'>Temporary Password</div>
               <div style='display:inline-block;background:#3D4A1E;color:#F5EDD6;font-size:1.6rem;font-weight:900;
                           letter-spacing:0.25em;padding:14px 28px;border-radius:12px;
                           border:3px solid #C4722A'>" . htmlspecialchars($tempPassword) . "</div>
             </div>
             <p style='font-size:.88rem;color:#555;margin-top:16px'>
               <strong>Important:</strong> You will be asked to change this password the first time you log in.
               Please keep your new password private and do not share it with anyone.
             </p>",
            $loginUrl,
            'Log In to KusiNay'
        );
        return $this->send($mail);
    }

    private function send(PHPMailer $mail): bool {
        try {
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log detailed error for debugging
            error_log('Mailer Error: ' . $e->getMessage());
            error_log('SMTP Debug: ' . $mail->ErrorInfo);
            return false;
        }
    }

    private function emailTemplate(string $title, string $body, ?string $btnLink, ?string $btnText): string {
        $btn = $btnLink ? "
            <div style='text-align:center;margin-top:28px'>
                <a href='{$btnLink}' target='_blank' rel='noopener'
                   style='background:#6B7A3A;color:#F5EDD6;padding:14px 32px;border-radius:10px;
                          text-decoration:none;font-weight:700;font-size:1rem;display:inline-block'>
                    {$btnText} →
                </a>
            </div>
            <div style='text-align:center;margin-top:20px;background:#f8f9fa;padding:15px;border-radius:8px'>
                <p style='margin:0 0 8px;font-weight:600;color:#333;font-size:.9rem'>
                    📋 Copy this link to verify manually:
                </p>
                <div style='background:#fff;border:1px solid #ddd;border-radius:6px;padding:10px;
                            font-family:monospace;font-size:.8rem;word-break:break-all;color:#333'>
                    {$btnLink}
                </div>
                <p style='margin:8px 0 0;font-size:.75rem;color:#666'>
                    💡 Tip: Right-click the link above and select &quot;Copy&quot; to use it in a new browser tab
                </p>
            </div>" : '';

        return "
        <!DOCTYPE html>
        <html>
        <body style='margin:0;padding:0;background:#ede0c0;font-family:Segoe UI,sans-serif'>
        <div style='max-width:540px;margin:32px auto;background:#fffcf5;border-radius:16px;
                    overflow:hidden;box-shadow:0 8px 32px rgba(61,74,30,0.12)'>

            <!-- Header -->
            <div style='background:#6B7A3A;padding:28px 32px;text-align:center'>
                <span style='color:#F5EDD6;font-size:1.6rem;font-weight:900'>🍲 KusiNay</span>
                <div style='color:rgba(245,237,214,0.75);font-size:.78rem;margin-top:4px;letter-spacing:.06em;text-transform:uppercase'>
                    Smart Meal Planning &amp; Nutrition
                </div>
            </div>

            <!-- Body -->
            <div style='padding:36px 32px;color:#3d3d2e'>
                <h2 style='color:#6B7A3A;margin:0 0 16px;font-size:1.3rem'>{$title}</h2>
                <div style='font-size:.95rem;line-height:1.7'>{$body}</div>
                {$btn}
            </div>

            <!-- Footer -->
            <div style='background:#6B7A3A;padding:16px 32px;text-align:center'>
                <p style='color:rgba(245,237,214,0.7);font-size:.78rem;margin:0'>
                    © " . date('Y') . " KusiNay — Nourishing families with care.<br>
                    This is an automated message, please do not reply.
                </p>
            </div>
        </div>
        </body>
        </html>";
    }
}