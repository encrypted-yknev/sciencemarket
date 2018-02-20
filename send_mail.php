<?php

#ini_set('display_errors', 1);
// Modify the path in the require statement below to refer to the 
// location of your Composer autoload.php file.
require 'composer/vendor/autoload.php';
// Instantiate a new PHPMailer 
$mail = new PHPMailer;

// Tell PHPMailer to use SMTP
$mail->isSMTP();

// Replace sender@example.com with your "From" address. 
// This address must be verified with Amazon SES.
$mail->setFrom('vn1123@gmail.com', 'Venkateshan');

// Replace recipient@example.com with a "To" address. If your account 
// is still in the sandbox, this address must be verified.
// Also note that you can include several addAddress() lines to send
// email to multiple recipients.
$mail->addAddress('venkycse93@gmail.com', 'Venky');

// Replace smtp_username with your Amazon SES SMTP user name.
$mail->Username = 'AKIAJYAA25S64UQ6KBQQ';

// Replace smtp_password with your Amazon SES SMTP password.
$mail->Password = 'Aj/pA0A9zKvJPpKt3FoX+4TJTmkK7jVy0GtOoYFs+OYI';
    
// Specify a configuration set. If you do not want to use a configuration
// set, comment or remove the next line.
#$mail->addCustomHeader('X-SES-CONFIGURATION-SET', 'ConfigSet');
 
// If you're using Amazon SES in a region other than US West (Oregon), 
// replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP  
// endpoint in the appropriate region.
$mail->Host = 'email-smtp.us-east-1.amazonaws.com';

// The subject line of the email
$mail->Subject = 'Password reset';

// The HTML-formatted body of the email
$mail->Body = '<img src="img/svg/logo4.svg" width=17% height=17% /><h1>Sciencemarket</h1>
    <p>You have requested for password reset. Please click on the link below to change your password</br>
    <a href="https://aws.amazon.com/ses">Reset password</a> 
    </p>';

// Tells PHPMailer to use SMTP authentication
$mail->SMTPAuth = true;

// Enable TLS encryption over port 587
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

// Tells PHPMailer to send HTML-formatted email
$mail->isHTML(true);

// The alternative email body; this is only displayed when a recipient
// opens the email in a non-HTML email client. The \r\n represents a 
// line break.
$mail->AltBody = "Sciencemarket\r\n
	You have requested for password reset. Please click on the link below to change your password\n
	<a href=''>Reset password</a>
";

if(!$mail->send()) {
    $message = "<div class='alert alert-danger login-message'>Some error occurred</div>";#.$mail->ErrorInfo." -- ". PHP_EOL;
} else {
    $message = "<div class='alert alert-success login-message'>Password reset link sent to your email</div>";# , PHP_EOL;
}
?>
