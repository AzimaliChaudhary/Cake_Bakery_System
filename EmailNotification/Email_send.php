<?php 

namespace CakeBakery\EmailNotification;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

//xzlr ffui zhkq npsb -> App PAssword


class EmailSender{


    public function EmailSend($email,$first_name,$last_name,$otp){

        try {

            $mail = new PHPMailer(true); // Create a new PHPMailer instance

            //Server settings
            $mail->isSMTP();                                            // Set mailer to use SMTP
            $mail->Host       = 'smtp.gmail.com';                   // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                                 // Enable SMTP authentication
            $mail->Username   = 'azchaudhary08@gmail.com';             // SMTP username
            $mail->Password   = 'xzlrffuizhkqnpsb';                       // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = 587;                                  // TCP port to connect to
            // $mail->SMTPDebug = 2;
        
            //Recipients
            $mail->setFrom('azchaudhary08@gmail.com', 'Mailer');
            $mail->addAddress($email, $first_name." ".$last_name); // Add a recipient
        
            // Content
            $mail->isHTML(true);                                      // Set email format to HTML
            $mail->Subject = 'OTP Verification';
        

            $name = $first_name." ".$last_name;

            // Fetch the template
            ob_start();
            include 'otp_email_template.php'; // Include the template with placeholders
            $template = ob_get_clean(); // Capture and store the evaluated content

            // Replace placeholders with session variables
            $template = str_replace('{{date}}', date('d M, Y'), $template);
            $template = str_replace('{{otp}}', $otp, $template);
            $template = str_replace('{{name}}', $name, $template);
            

            
           
            $mail->Body    = $template;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
            
        
            $mail->send();
            echo 'Email has been sent';
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

}


?>        