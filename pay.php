<?php
session_start();
require_once './database/db_config.php';
require_once './vendor/autoload.php';
require_once 'secret.php';

use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirm_password']));
    $plan = htmlspecialchars(trim($_POST['plan']));
    $location = htmlspecialchars(trim($_POST['location']));

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword) || empty($plan)) {
        $_SESSION['message'] = "All fields are required.";
        $_SESSION['messageType'] = "error";
        header("Location: pay.php");  // Redirect back to the form page
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['messageType'] = "error";
        header("Location: pay.php");
        exit;
    } elseif ($password !== $confirmPassword) {
        $_SESSION['message'] = "Passwords do not match.";
        $_SESSION['messageType'] = "error";
        header("Location: pay.php");
        exit;
    }

    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "Email already exists.";
        $_SESSION['messageType'] = "error";
        header("Location: pay.php");
        exit;
    }

    $stmt->close();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Encode user data securely
    $userData = base64_encode(json_encode([
        'name' => $name,
        'email' => $email,
        'password' => $hashedPassword,
        'location' => $location,
        'plan' => $plan,
    ]));

    Stripe::setApiKey($stripeSecretKey);
    $YOUR_DOMAIN = 'http://localhost/ AmazingHR';

    try {
        $checkout_session = CheckoutSession::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',  // Set currency to GBP (£)
                    'product_data' => ['name' => "Plan Subscription"],
                    'unit_amount' => intval($plan) * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => "$YOUR_DOMAIN/success.php?data=$userData",
            'cancel_url' => "$YOUR_DOMAIN/cancel.html",
        ]);

        header("Location: " . $checkout_session->url);
        exit;
    } catch (Exception $e) {
        $_SESSION['message'] = "Error creating payment session: " . $e->getMessage();
        $_SESSION['messageType'] = "error";
        header("Location: pay.php");
        exit;
    }
}



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
    <title>Pay Now
        </title>
        <link rel="shortcut icon" href="images/logo.png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://kit.fontawesome.com/f0fb58e769.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="style.css">
        <style>
          form input{
            width:300px
          }
        </style>
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
   
    <h3 style="color:#005151;">Sign up to Proceed</h3>
<form action="" method="post">
  <input type="text" name="name" placeholder="Company Name" style="padding: 8px;border: 1px solid rgb(192, 190, 190);border-radius: 5px;margin: 8px;"><br>
  <input type="text" name="location" placeholder="Company Location" style="padding: 8px;border: 1px solid rgb(192, 190, 190);border-radius: 5px;margin: 8px"><br>
    <input type="email" name="email" placeholder="Company Email" style="padding: 8px;border: 1px solid rgb(192, 190, 190);border-radius: 5px;margin: 8px;"><br>
    <input type="password" name="password" placeholder="Password" style="padding: 8px;border: 1px solid rgb(192, 190, 190);border-radius: 5px;margin: 8px"><br>
    <input type="password" name="confirm_password" placeholder="Confirm Password" style="padding: 8px;border: 1px solid rgb(192, 190, 190);border-radius: 5px;margin: 8px"><br>
    <select name="plan" style="padding: 8px;border: 1px solid rgb(192, 190, 190);border-radius: 5px;margin: 8px">
    <option value="">Select Plan</option>
    <option value="99">Basic</option>
    <option value="199">Standard</option>
    <option value="399">Premium</option>
   </select><br>
    <button type="submit" id="contact4" style="width: auto;">Proceed to payment</button>
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