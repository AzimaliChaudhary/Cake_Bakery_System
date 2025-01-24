<?php 
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if($_SERVER["REQUEST_METHOD"] == "POST")
{

  $email = $_POST['email'];
  $sql = "SELECT user_verify FROM tbluser WHERE Email = '$email'";

  $result = mysqli_query($con, $sql);

  if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_array($result);

      if ($row['user_verify'] == 1) {
          ?> 
            <script>
              alert('User is already verified.');
              window.location.href = "login.php";
            </script>

          <?php
         
      } else {
             $new_otp=rand(1000,9999);
             $update_otp="update tbluser set otp_varify=$new_otp where Email='$email'";

             $update_result=mysqli_query($con,$update_otp);

             if($update_result)
             {
              ?> 
              <script>
                alert('Otp successfully resended.');
                window.location.href = "login.php";
              </script>
  
            <?php
             }else {
              echo "<script>alert('Error while resending Otp.');</script>";
          }
      }
  }
}

?> 


