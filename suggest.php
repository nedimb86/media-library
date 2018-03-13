<?php
include('inc/password.php');
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
require './vendor/phpmailer/src/PHPMailer.php';
require './vendor/phpmailer/src/Exception.php';
require './vendor/phpmailer/src/SMTP.php';


if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $details = trim(filter_input(INPUT_POST, 'details', FILTER_SANITIZE_SPECIAL_CHARS));
    $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING));

    if ($name == "" || $email == "" || $details == ""){
      echo 'Please fill in required fields: Name, Email and Details!';
      exit;
    }

    if($address !== "") {
      echo "Invalid form";
      exit;
    }

    if(!PHPMailer::validateAddress($email)) {
      echo 'Invalid email';
      exit;
    }

    $email_body = "Name $name\n";
    $email_body .= "Email $email\n";
    $email_body .= "Details $details\n";

      $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 2;
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->Username = 'nedimb86@gmail.com';
    $mail->Password = $PASSWORD;
      $mail->setFrom($email, $name);
      $mail->addAddress('nedimb86@gmail.com', 'Nedim Becirovic');
      $mail->addReplyTo($email, $name);
      $mail->Subject = 'Suggestions from ' . $name;
      $mail->Body = $email_body;
      if (!$mail->send()) {
          echo "Mailer Error: " . $mail->ErrorInfo;
          exit;
      }
    header('Location: suggest.php?status=thanks');
}
$pageTitle = 'Suggest a Media Item';
$section = "suggest";
include("inc/header.php");
?>

<div class="section page">
  <div class="wrapper">
    <h1>Suggest a Media Item</h1>
    <?php
    if(isset($_GET['status']) && $_GET['status'] == 'thanks') {
      echo '    <p>Thanks for the email! I&rsquo;ll check out suggestion shortly!</p>';
    } else { ?>

    <p>If you think there is something I&rsquo;m missing, let me know! Complete the form to send me an email.</p>

    <form action="suggest.php" method="post">
      <table>
        <tr>
          <th><label for="name">Name</label></th>
          <td><input type="text" id="name" name="name"></td>
        </tr>
        <tr>
          <th><label for="email">Email</label></th>
          <td><input type="text" id="email" name="email"></td>
        </tr>
        <tr style="display: none">
          <th><label for="address">Address</label></th>
          <td><input type="text" id="address" name="address"><p>Please leave this field blank!</p></td>
        </tr>
        <tr>
          <th><label for="details">Suggest</label></th>
          <td><textarea  id="details" name="details"></textarea></td>
        </tr>
      </table>
      <input type="submit" value="Send">
    </form>
      <?php } ?>
  </div>
</div>

<?php include("inc/footer.php"); ?>