<?php
// email.php — Email sending utilities using PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

function send_email($to, $subject, $message, $headers = []) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPAutoTLS = false;
        $mail->SMTPSecure = SMTP_ENCRYPTION === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        // Enable debugging for troubleshooting
        $mail->SMTPDebug = 0; // Set to 2 for detailed debug output
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer Debug [$level]: $str");
        };

        // Work around local certificate validation problems in XAMPP/PHP
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        // Recipients
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return ['success' => true, 'message' => 'Email sent successfully'];
    } catch (Exception $e) {
        $raw_message = 'Email sending failed: ' . $e->getMessage();
        $user_message = 'Unable to send email. Please check your SMTP settings and try again.';

        if (stripos($e->getMessage(), 'certificate verify failed') !== false || stripos($mail->ErrorInfo, 'certificate verify failed') !== false) {
            $user_message = 'Unable to send email because your system cannot verify the mail server certificate. This often happens on local XAMPP setups. Try the SMTP debug page or install CA certificates.';
        } elseif (stripos($e->getMessage(), 'Could not connect to SMTP host') !== false || stripos($mail->ErrorInfo, 'Could not connect to SMTP host') !== false) {
            $user_message = 'Unable to connect to the mail server. Check SMTP host, port, credentials, and firewall settings.';
        }

        $error_details = [
            'success' => false,
            'message' => $user_message,
            'raw_message' => $raw_message,
            'error_info' => $mail->ErrorInfo,
            'smtp_host' => SMTP_HOST,
            'smtp_port' => SMTP_PORT,
            'from_email' => FROM_EMAIL,
        ];
        error_log("Email sending failed: " . json_encode($error_details));
        return $error_details;
    }
}

function generate_otp() {
    return sprintf('%06d', mt_rand(100000, 999999));
}

function send_otp_email($email, $otp) {
    $subject = 'Your Nur-Sim Verification Code';

    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Nur-Sim Email Verification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #2D8FBF, #5A82A0); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
            .otp-code { font-size: 32px; font-weight: bold; color: #2D8FBF; text-align: center; margin: 20px 0; letter-spacing: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Nur-Sim</h1>
                <p>Nursing Simulation Training Platform</p>
            </div>
            <div class="content">
                <h2>Email Verification</h2>
                <p>Hello,</p>
                <p>Welcome to Nur-Sim! To complete your account verification, please use the following 6-digit code:</p>
                <div class="otp-code">' . $otp . '</div>
                <p>This code will expire in 10 minutes. Please enter it in the verification form to activate your account.</p>
                <p>If you did not request this verification, please ignore this email.</p>
                <p>Best regards,<br>The Nur-Sim Team</p>
            </div>
            <div class="footer">
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ';

    $result = send_email($email, $subject, $message);
    $result['email_preview'] = $message;
    $result['otp_code'] = $otp;
    $result['recipient'] = $email;
    return $result;
}

function send_login_otp_email($email, $otp) {
    $subject = 'Your Nur-Sim Login Code';

    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Nur-Sim Login Verification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #2D8FBF, #5A82A0); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
            .otp-code { font-size: 32px; font-weight: bold; color: #2D8FBF; text-align: center; margin: 20px 0; letter-spacing: 5px; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Nur-Sim</h1>
                <p>Nursing Simulation Training Platform</p>
            </div>
            <div class="content">
                <h2>Login Verification</h2>
                <p>Hello,</p>
                <p>You are attempting to log in to your Nur-Sim account. For security purposes, please use the following 6-digit code to complete your login:</p>
                <div class="otp-code">' . $otp . '</div>
                <p>This code will expire in 10 minutes. Please enter it in the login form to access your account.</p>
                <p>If you did not request this login, please contact our support team immediately.</p>
                <p>Best regards,<br>The Nur-Sim Team</p>
            </div>
            <div class="footer">
                <p>This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>
    </body>
    </html>
    ';

    $result = send_email($email, $subject, $message);
    $result['email_preview'] = $message;
    $result['otp_code'] = $otp;
    $result['recipient'] = $email;
    return $result;
}
?>