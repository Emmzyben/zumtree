<?php
session_start(); // Start the session at the very top of the file
require_once './database/db_config.php';

// Initialize message variables
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $emailOrUsername = trim($_POST['email-username']);
    $password = trim($_POST['password']);

    // Validate input fields
    if (empty($emailOrUsername) || empty($password)) {
        $message = "Email and Password are required.";
        $messageType = "error";
    } else {
        // Check for the user in the database
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("s", $emailOrUsername);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Store user data in session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];

                    header("Location: dashboard.php");
                    exit;
                } else {
                    $message = "Invalid password. Please try again.";
                    $messageType = "error";
                }
            } else {
                $message = "No user found with the provided email.";
                $messageType = "error";
            }

            // Close the statement
            $stmt->close();
        } else {
            $message = "Database query error. Please try again later.";
            $messageType = "error";
        }
    }

    // Store message in the session for display
    $_SESSION['message'] = $message;
    $_SESSION['messageType'] = $messageType;

    // Redirect back to the login page
    header("Location: login.php");
    exit;
}

// Retrieve and unset messages from the session (if any)
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'];
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login
        </title>
        <link rel="shortcut icon" href="images/logo.png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://kit.fontawesome.com/f0fb58e769.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div id="head1">
<img src="images/logo.png" alt="" id="img">
        </div>
        <div id="head2">
            <div class="head2">
              <ul >
                <a href=""></a>
                <li><a href="index.html" >Home</a></li>
                <li><a href="services.html" >Services</a></li>
                <li><a href="about.html">About Us</a></li>
                <li><a href="prices.html" >Prices</a></li>
                 
                <li><a href="book.html" id="button">Contact Us</a></li>
                <li>
                    <div id="google_translate_element">
                    </div>
    </li>
             </ul>
            </div>

        </div>
        
    </header>
    <div id="side">
        <div id="underNav">
<img src="images/logo.png" alt="" id="img">        
        </div>

        <div onclick="openNav()">
         <div class="container" onclick="myFunction(this)" id="sideNav">
             <div class="bar1"></div>
             <div class="bar2"></div>
             <div class="bar3"></div>
           </div>
         </div>
       </div>

       <div id="mySidenav" class="sidenav">
        <div style="text-align: center;margin-bottom: 40px;">
          <img src="images/logo.png" alt="" id="img"> 
        </div>
        <a href="index.html">Home</a>
        <a href="services.html">Services</a>
        <a href="about.html">About Us</a>
        
         <a href="login.php">Sign in</a>
        <a href="book.html">Contact Us</a>
    <div id="google_translate_element">
    </div>
         </div>
       
       
      </div>

<!-- content -->



<div style="display: flex;align-items: center;justify-content: center;">
    <?php
if (!empty($message)) {
    echo '<div id="notificationBar" class="notification-bar notification-' . $messageType . '">';
    echo $message;
    echo '<span class="close-btn" onclick="closeNotification()">&times;</span>';
    echo '</div>';
}
?>
 <div style="height: auto;text-align: center;padding: 30px;border: 1px solid rgb(192, 190, 190);width: 300px;margin: 20px;border-radius: 5px;">
    <h3 style="color:#005151;">Sign in to Dashboard</h3>
<form action="" method="post">
 <input type="email" name="email-username" placeholder="Company Email" style="padding: 8px;border: 1px solid rgb(192, 190, 190);border-radius: 5px;margin: 8px;"><br>
    <input type="password" name="password" placeholder="Password" style="padding: 8px;border: 1px solid rgb(192, 190, 190);border-radius: 5px;margin: 8px"><br>
    <button type="submit" id="contact4" style="width: auto;">Sign in</button>
</form>
</div>   
</div>








<footer>
    <div>
  <div style="display: flex;flex-direction: row;">
        <img src="images/logo.png" alt="" id="imgbtn">
    </div>
<p>At  AmazingHR, we specialize in providing seamless Payroll, HR, and Accounting solutions tailored to meet the unique needs of businesses worldwide.</p>
</div>
  
  <div id="links">
    <h3>Links</h3>
    <a href="index.html">Home</a><br>
    <a href="about.html">About</a><br>
    <a href="services.html">Services</a>
   </div>
   <div id="links">
    <h3>Services</h3>
    <a href="services.html">Payroll Management</a><br>
    <a href="services.html">Human Resource Services</a><br>
    <a href="services.html">Accounting Solutions</a>
    
   </div>
    <div>
   <h3>Contact Us</h3>  
  <p> <i class="fa fa-map-marker" style=" font-size:20px;color:#1a938a;padding-right: 10px"></i>Mailing Address: 117 Constitution Road Chatham ME5 7DW Kent</p>
    <p><i class="fa fa-phone" style="font-size:15px;color:#1a938a;padding-right: 10px;"></i>+44 7398 313362</p>
   <p><i class="fa fa-envelope" style="font-size:15px;color:#1a938a;padding-right: 10px;"></i>Info@crownworldltd.com</p> 
    </div>
   
  
  </footer>
      <button id="scrollToTopBtn" onclick="scrollToTop()">↑</button>
      <div style="padding: 10px;color: #fff;text-align: center;background-color: #060b1e;">
        <p>© 2025  AmazingHR. All rights reserved.</p>
      </div>
      <script src="script.js"></script>
</body>
</html>