<?php
// Initialize session
session_start();

// If user is already logged in, redirect to student dashboard
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: student/dashboard.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$fullname = $email = $password = $contact_no = $roll_number = $user_type = "";
$fullname_err = $email_err = $password_err = $contact_no_err = $roll_number_err = "";
$success_msg = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate fullname
    if(empty(trim($_POST["fullname"]))){
        $fullname_err = "Please enter your full name.";
    } else {
        $fullname = trim($_POST["fullname"]);
    }
    
    // Validate roll_number
    if(empty(trim($_POST["roll_number"]))){
        $roll_number_err = "Please enter your Roll Number / Employee ID.";
    } else {
        // Check if roll number already exists
        $sql = "SELECT id FROM users WHERE roll_number = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_roll);
            $param_roll = trim($_POST["roll_number"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $roll_number_err = "This Roll Number / ID is already registered.";
                } else {
                    $roll_number = trim($_POST["roll_number"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email.";
    } else {
        // Check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already registered.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate contact_no
    if(empty(trim($_POST["contact_no"]))){
        $contact_no_err = "Please enter your contact number.";
    } else {
        $contact_no = trim($_POST["contact_no"]);
    }

    // Get user type
    $user_type = $_POST["user_type"];
    
    // Check input errors before inserting in database
    if(empty($fullname_err) && empty($email_err) && empty($password_err) && empty($contact_no_err) && empty($roll_number_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (fullname, email, password, contact_no, roll_number, user_type) VALUES (?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_fullname, $param_email, $param_password, $param_contact, $param_roll, $param_type);
            
            // Set parameters
            $param_fullname = $fullname;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_contact = $contact_no;
            $param_roll = $roll_number;
            $param_type = $user_type;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Registration successful! You can now log in.";
                // Clear fields
                $fullname = $email = $password = $contact_no = $roll_number = $user_type = "";
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - College Complaint Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Create Account</h1>
                <p>Register as a student or faculty member</p>
            </div>
            
            <?php 
            if(!empty($success_msg)){
                echo '<div class="alert alert-success">' . $success_msg . ' <a href="login.php">Login here</a></div>';
            }
            ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <!-- User Type Selector -->
                <div class="form-group">
                    <label for="user_type">Register As</label>
                    <select name="user_type" id="user_type" class="form-control">
                        <option value="student" <?php echo ($user_type == 'student') ? 'selected' : ''; ?>>Student</option>
                        <option value="faculty" <?php echo ($user_type == 'faculty') ? 'selected' : ''; ?>>Faculty</option>
                    </select>
                </div>

                <!-- Full Name -->
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" name="fullname" id="fullname" class="form-control <?php echo (!empty($fullname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fullname; ?>" placeholder="John Doe">
                    <span style="color: var(--danger); font-size: 0.8rem;"><?php echo $fullname_err; ?></span>
                </div>

                <!-- Roll Number / Employee ID -->
                <div class="form-group">
                    <label for="roll_number">Roll Number / Employee ID</label>
                    <input type="text" name="roll_number" id="roll_number" class="form-control <?php echo (!empty($roll_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $roll_number; ?>" placeholder="e.g. BT19CSE042">
                    <span style="color: var(--danger); font-size: 0.8rem;"><?php echo $roll_number_err; ?></span>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" placeholder="john.doe@college.edu">
                    <span style="color: var(--danger); font-size: 0.8rem;"><?php echo $email_err; ?></span>
                </div>

                <!-- Contact Number -->
                <div class="form-group">
                    <label for="contact_no">Contact Number</label>
                    <input type="text" name="contact_no" id="contact_no" class="form-control <?php echo (!empty($contact_no_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $contact_no; ?>" placeholder="e.g. 9876543210">
                    <span style="color: var(--danger); font-size: 0.8rem;"><?php echo $contact_no_err; ?></span>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" placeholder="Minimum 6 characters">
                    <span style="color: var(--danger); font-size: 0.8rem;"><?php echo $password_err; ?></span>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Register</button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
                <p style="margin-top: 0.5rem;"><a href="index.php">&larr; Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>
