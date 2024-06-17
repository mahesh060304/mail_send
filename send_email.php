<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function getEmailTemplate($templateName,$data) {
    $templatePath = __DIR__ . "/mail_templates/{$templateName}.html";
    if (!file_exists($templatePath)) {
        throw new Exception("Template not found: $templateName");
    }
    $templateContent = file_get_contents($templatePath);

    foreach ($data as $key => $value) {
        $templateContent = str_replace("{{{$key}}}", $value, $templateContent);
    }

    return $templateContent;
}

function sendEmail($to, $cc, $bcc, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        if (!empty($to)) {
            foreach ($to as $recipient) {
                if (!empty($recipient)) {
                    $mail->addAddress($recipient);
                }
            }
        }

        if (!empty($cc)) {
            foreach ($cc as $recipient) {
                if (!empty($recipient)) {
                    $mail->addCC($recipient);
                }
            }
        }

        if (!empty($bcc)) {
            foreach ($bcc as $recipient) {
                if (!empty($recipient)) {
                    $mail->addBCC($recipient);
                }
            }
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $to = $_POST['to'];
        $cc = $_POST['cc'];
        $bcc = $_POST['bcc'];
        $template = $_POST['template'];
        if($template=="bootcamp"){
            $subject ="Welcome to GUVI Bootcamp";
            $data = [
                'name' => "",
                'event_date' => ""
            ];
        }else if($template=="course_approve"){
            $subject ="Course Approved";
        }else if($template=="forget"){
            $subject ="Forgot Password";
            $data = [
                'name' => "",
                'hash' =>""
            ];
        }else if($template=="job_alert"){
            $subject ="Welcome to GUVI ";
        }else if($template=="landing"){
            $subject ="Thank you for signing up for GUVI Android Course ";
            $data = [
                'hash' =>""
            ];
        }else if($template=="mentor_email"){
            $subject ="Welcome to GUVI Mentorship Program";
            $data = [
                'name' => "",
                'hash' =>""
            ];
        }else if($template=="mentor_video"){
            $subject ="Welcome to GUVI Mentorship Program";
            $data = [
                'name' => "",
                'hash' =>""
            ];
        }else if($template=="mentor"){
            $subject ="Mentee Contact Details";
            $data = [
                'name' => "",
                'email' => $to,
                'phone' => ""
            ];
        }else if($template=="new_mentor_register"){
            $subject ="Thank you for your association with GUVI.";
            $data=[
                'name' => "",
                'hash' => "",
                'role' => ""
            ];
        }else if($template == "refer"){
            $subject = "Welcome to GUVI";
            $data = [
                'hash' => "",
            ];
        }else if($template=="reject"){
            $subject ="Course Rejected";
        }else if($template=="task_approve"){
            $subject ="Task approve";
            $data=[
                'menteename' => "",
                'mentorname' => "",
                'menteeemail' => "",
                'mentoremail' => ""
            ];
        }else if($template=="task_approved"){
            $subject ="Task approved";
            $data=[
                'menteename' => ""
            ];
        }else if($template=="user_feedback"){
            $subject ="User feedback";   
            $data = [
                'uname' => "Mahesh",
                'feedback' =>"Good",
                'page'=>"1",
                'type_of_feedback'=> "Education",
            ];
        }else if($template=="video"){
            $subject ="Welcome to GUVI"; 
            $data = [
                'name' => "",
                'hash' =>""
            ];
        }

        $body = getEmailTemplate($template,$data);
        sendEmail($to, $cc, $bcc, $subject, $body);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

