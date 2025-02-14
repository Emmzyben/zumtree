<?php
session_start(); // Ensure session is started
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once './database/db_config.php';

// Fetch user details
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $message = "User not found.";
    $messageType = "error";
}
$stmt->close();

// Initialize message variables
$message = "";
$messageType = "";

// Handle CSV upload
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['staffs_csv'])) {
    $file = $_FILES['staffs_csv'];

    // Check if file is valid
    if ($file['error'] === UPLOAD_ERR_OK && pathinfo($file['name'], PATHINFO_EXTENSION) === 'csv') {
        $uploadDir = './uploads/';
        $filePath = $uploadDir . basename($file['name']);

        // Move the uploaded file to the server
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Update the database with the file path
            $updateQuery = "UPDATE users SET file = ? WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("si", $filePath, $userId);

            if ($stmt->execute()) {
                $message = "CSV file successfully uploaded!";
                $messageType = "success";
            } else {
                $message = "Error updating user data: " . $stmt->error;
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Failed to upload file.";
            $messageType = "error";
        }
    } else {
        $message = "Invalid file type or upload error. Please upload a valid CSV file.";
        $messageType = "error";
    }
}

// Close the database connection
$conn->close();

// Retrieve and unset session messages (if any)
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
    <title>Dashboard
        </title>
        <link rel="shortcut icon" href="images/logo.png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="https://kit.fontawesome.com/f0fb58e769.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="style.css">
        <style>
            form input{
                padding: 8px;border: 1px solid rgb(192, 190, 190);border-radius: 5px;margin-bottom: 8px;
                margin-top: 5px;width: 300px;
            }
            .card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            max-width: 500px;
            margin: 20px auto;
            text-align: left;
            background-color: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .card h3 {
            color: #005151;
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
                <a href=""></a>
                <a href=""></a>
                <a href=""></a>
                <li><a href="">Welcome <?php echo htmlspecialchars($user['name']); ?>!</a></li>
                <li><a href="logout.php" >Log out</a></li>
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
        <a href="">Log out</a>
    <div id="google_translate_element">
    </div>
         </div>
       
       
      </div>

<!-- content -->





<div style="height: auto;;padding: 30px;text-align: center;">
<?php
if (!empty($message)) {
    echo '<div id="notificationBar" class="notification-bar notification-' . $messageType . '">';
    echo $message;
    echo '<span class="close-btn" onclick="closeNotification()">&times;</span>';
    echo '</div>';
}
?>
    <h3 style="text-align: center; color: #005151;">My Company Details</h3>

<div class="card">
    <h3>User Details</h3>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($user['location']); ?></p>
    <?php if (!empty($user['file'])): ?>
        <p><strong>Uploaded File:</strong> <a href="<?php echo htmlspecialchars($user['file']); ?>" download>Download CSV</a></p>
    <?php endif; ?>
</div>

<h5 style="text-align: center; color: #005151;">Upload a CSV File Containing Company Staffs and Salaries</h5>

<form action="" method="POST" enctype="multipart/form-data" style="text-align: center;">
    <input type="file" name="staffs_csv" accept=".csv" required><br><br>
    <button type="submit" style="background-color: #005151; color: white; padding: 10px 20px; border: none; cursor: pointer;">
        Upload CSV
    </button>
</form>

<br>
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