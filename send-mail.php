<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name    = strip_tags(trim($_POST["name"]));
    $email   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone   = strip_tags(trim($_POST["phone"]));
    $subject = strip_tags(trim($_POST["subject"]));
    $message = trim($_POST["message"]);

    if ( empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($phone) || empty($subject) || empty($message)) {
        $_SESSION['mail_status'] = "❌ Please fill all fields correctly.";
        header("Location: contact.php");
        exit;
    }

    $admin_content = "New contact form submission:\n\n";
    $admin_content .= "Name: $name\nEmail: $email\nPhone: $phone\nCourse: $subject\n\nMessage:\n$message\n";

    $client_content = "Dear $name,\n\nThank you for contacting Gian Institute.\n\n";
    $client_content .= "You selected the course: $subject\n\nWe will get back to you shortly.\n\nBest regards,\nGian Institute";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tanyabatra@gmail.com'; // your email
        $mail->Password   = 'app password';     // your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // ➤ Send to admin
        $mail->setFrom('tanyabatra@gmail.com', 'Gian Institute Contact Form');
        $mail->addAddress('tanyabatra@gmail.com', 'Gian Admin');
        $mail->Subject = "New contact form submission: $subject";
        $mail->Body    = $admin_content;
        $mail->isHTML(false);
        $mail->send();

        // ➤ Send to user
        $mail->clearAddresses();
        $mail->setFrom('tanyabatra949@gmail.com', 'Gian Institute');
        $mail->addAddress($email, $name);
        $mail->Subject = "Thank you for contacting Gian Institute";
        $mail->Body    = $client_content;
        $mail->isHTML(false);
        $mail->send();

        $_SESSION['mail_status'] = "✅ Your message has been sent successfully!";
    } catch (Exception $e) {
        $_SESSION['mail_status'] = "❌ Mail error: {$mail->ErrorInfo}";
    }

    header("Location: contact.php");
    exit;
} else {
    $_SESSION['mail_status'] = "❌ Invalid request.";
    header("Location: contact.php");
    exit;
}
