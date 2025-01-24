<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
include('EmailNotification/Email_send.php');
use CakeBakery\EmailNotification\EmailSender;

// Initialize EmailSender
$send_email = new EmailSender();

// Get email from session
$email = $_SESSION['email'];

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Combine OTP inputs
    $otp = $_POST['otp1'] . $_POST['otp2'] . $_POST['otp3'] . $_POST['otp4'];

    // Check OTP in database
    $sql = "SELECT otp_varify, user_verify FROM tbluser WHERE Email = '$email'";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        if ($row['user_verify'] == 1) {
            echo "<script>alert('User is already verified.'); window.location.href = 'login.php';</script>";
        } else {
            if ($row['otp_varify'] == $otp) {
                // Update the user verification status if OTP matches
                $update_sql = "UPDATE tbluser SET user_verify = 1 WHERE Email = '$email'";
                $update_result = mysqli_query($con, $update_sql);
                if ($update_result) {
                    echo "<script>alert('OTP verified! User is now verified.'); window.location.href = 'login.php';</script>";
                } else {
                    echo "<script>alert('Failed to update verification status.');</script>";
                }
            } else {
                echo "<script>alert('Invalid OTP. Please try again.');</script>";
            }
        }
    } else {
        echo "<script>alert('User not found.');</script>";
    }
}

// Handle OTP resend
// Handle OTP resend
if (isset($_POST['resend_otp']) && $_POST['resend_otp'] == 'true') {
    // Generate a new OTP
    $otp = rand(1000, 9999);

    // Update the OTP in the database
    $sql = "UPDATE tbluser SET otp_varify = '$otp' WHERE Email = '$email'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        // Send OTP to user's email
        $fname = ''; // Fetch user's first name if necessary
        $lname = ''; // Fetch user's last name if necessary
        $send_email->EmailSend($email, $fname, $lname, $otp); // Send OTP email

        // Respond with success message
        echo json_encode(["status" => "success", "message" => "OTP has been sent to your email address."]);
    } else {
        // Respond with failure message
        echo json_encode(["status" => "error", "message" => "Failed to resend OTP. Please try again later."]);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cake Bakery System || OTP Verification</title>
    <link rel="stylesheet" href="css/otp_style.css" />
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
    <?php include_once('includes/header.php'); ?>

    <section class="banner_area">
        <div class="container">
            <div class="banner_text">
                <h3>OTP Verification</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="registration.php">OTP Verification</a></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="otp_section">
        <div class="container1">
            <header>
                <i class="bx bxs-check-shield"></i>
            </header>
            <h4>Enter OTP Code</h4>
            <form action="" method="POST">
                <div class="input-field">
                    <input type="number" name="otp1" id="otp1" maxlength="1" oninput="moveNext(this, 'otp2')" />
                    <input type="number" name="otp2" id="otp2" maxlength="1" oninput="moveNext(this, 'otp3')" />
                    <input type="number" name="otp3" id="otp3" maxlength="1" oninput="moveNext(this, 'otp4')" />
                    <input type="number" name="otp4" id="otp4" maxlength="1" oninput="moveNext(this, '')" />
                </div>
                <button class="btn btn_verify">Verify OTP</button>

                <!-- Resend OTP Section -->
                <div class="form-group col-md-12 resend_otp">
                    <strong class="">Don't receive OTP?</strong>
                    <a href="javascript:void(0)" onclick="resend_otp()" id="resendBtn"><i class="ft-user" name="btn_resend"></i> Resend</a>
                </div>
            </form>
        </div>
    </section>

    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <script>
        function resend_otp() {
    var email = "<?php echo $_SESSION['email']; ?>";
    
    // Disable the resend button immediately and start the cooldown
    var resendButton = document.getElementById('resendBtn');
    resendButton.innerHTML = 'Please wait 10 seconds...';  // Change button text
    resendButton.style.pointerEvents = 'none';  // Disable button click
    resendButton.classList.add('disabled');  // Optional: Add a disabled class to style

    // Start the 10-second cooldown
    var timeLeft = 10;
    var timer = setInterval(function() {
        timeLeft--;
        resendButton.innerHTML = 'Resend (' + timeLeft + ')';
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            resendButton.innerHTML = 'Resend';  // Reset the button text
            resendButton.style.pointerEvents = 'auto';  // Enable button click again
            resendButton.classList.remove('disabled');  // Remove the disabled class
        }
    }, 1000);  // Update every second

    // AJAX call to resend OTP
    $.ajax({
        url: "",  // The same page to handle OTP resend
        type: "POST",
        data: { resend_otp: 'true', email: email },
        success: function(data) {
            var response = JSON.parse(data);
            if (response.status === "success") {
                alert(response.message);  // Show the success message
            } else {
                alert(response.message);  // Show the failure message
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
            alert("An error occurred while resending OTP. Please try again later.");
        }
    });
}


        function moveNext(current, nextFieldID) {
            if (current.value.length >= 1) {
                if (nextFieldID) {
                    document.getElementById(nextFieldID).disabled = false;
                    document.getElementById(nextFieldID).focus();
                }
            }
        }

        // Optional: disable fields initially except for the first one
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll(".input-field input");
            inputs.forEach((input, index) => {
                input.disabled = index !== 0;
            });
        });
    </script>

    <?php include_once('includes/footer.php'); ?>

</body>
</html>
