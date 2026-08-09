<?php
// Define page meta variables for the header
$page_title = "My Profile";
$page_desc = "View registration details and update your security settings";

// Include header
require_once "header.php";
require_once "../config.php";

$user_id = $_SESSION["id"];
$success_msg = "";
$error_msg = "";

// Fetch current details
$fullname = $email = $contact_no = $roll_number = $user_type = "";
$sql = "SELECT fullname, email, contact_no, roll_number, user_type FROM users WHERE id = ?";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_bind_result($stmt, $fullname, $email, $contact_no, $roll_number, $user_type);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Processing Profile Update
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])){
    $new_fullname = trim($_POST["fullname"]);
    $new_contact = trim($_POST["contact_no"]);
    
    if(empty($new_fullname) || empty($new_contact)){
        $error_msg = "Please fill in all profile fields.";
    } else {
        $sql = "UPDATE users SET fullname = ?, contact_no = ? WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ssi", $new_fullname, $new_contact, $user_id);
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Profile updated successfully!";
                $fullname = $new_fullname;
                $contact_no = $new_contact;
                $_SESSION["fullname"] = $new_fullname; // Update session value
            } else {
                $error_msg = "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Processing Password Update
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_password"])){
    $curr_password = $_POST["curr_password"];
    $new_password = $_POST["new_password"];
    $conf_password = $_POST["conf_password"];
    
    if(empty($curr_password) || empty($new_password) || empty($conf_password)){
        $error_msg = "Please fill in all password fields.";
    } elseif($new_password != $conf_password){
        $error_msg = "New password and confirmation password do not match.";
    } elseif(strlen($new_password) < 6){
        $error_msg = "New password must have at least 6 characters.";
    } else {
        // Verify current password first
        $db_password = "";
        $pass_sql = "SELECT password FROM users WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $pass_sql)){
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_bind_result($stmt, $db_password);
                mysqli_stmt_fetch($stmt);
            }
            mysqli_stmt_close($stmt);
        }
        
        if(password_verify($curr_password, $db_password)){
            // Update password
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $upd_sql = "UPDATE users SET password = ? WHERE id = ?";
            if($stmt = mysqli_prepare($conn, $upd_sql)){
                mysqli_stmt_bind_param($stmt, "si", $new_hash, $user_id);
                if(mysqli_stmt_execute($stmt)){
                    $success_msg = "Password updated successfully!";
                } else {
                    $error_msg = "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $error_msg = "Invalid current password.";
        }
    }
}

mysqli_close($conn);
?>

<?php 
if(!empty($success_msg)){
    echo '<div class="alert alert-success">' . $success_msg . '</div>';
}
if(!empty($error_msg)){
    echo '<div class="alert alert-danger">' . $error_msg . '</div>';
}
?>

<div class="dashboard-row">
    <!-- Profile Info Form -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title">Profile Information</h2>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Read Only Fields -->
            <div class="form-group">
                <label>Roll Number / Employee ID</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($roll_number); ?>" disabled style="background-color: var(--gray-100); color: var(--gray-500); cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($email); ?>" disabled style="background-color: var(--gray-100); color: var(--gray-500); cursor: not-allowed;">
            </div>
            <div class="form-group">
                <label>Account Type</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars(ucfirst($user_type)); ?>" disabled style="background-color: var(--gray-100); color: var(--gray-500); cursor: not-allowed; text-transform: capitalize;">
            </div>
            
            <hr style="border: 0; border-top: 1px solid var(--gray-200); margin: 1.5rem 0;">
            
            <!-- Editable Fields -->
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" name="fullname" id="fullname" class="form-control" value="<?php echo htmlspecialchars($fullname); ?>" required>
            </div>
            <div class="form-group">
                <label for="contact_no">Contact Number</label>
                <input type="text" name="contact_no" id="contact_no" class="form-control" value="<?php echo htmlspecialchars($contact_no); ?>" required>
            </div>
            
            <button type="submit" name="update_profile" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Save Profile Updates</button>
        </form>
    </div>

    <!-- Password Update Form -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title">Update Password</h2>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="curr_password">Current Password</label>
                <input type="password" name="curr_password" id="curr_password" class="form-control" placeholder="Enter current password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Minimum 6 characters" required>
            </div>
            <div class="form-group">
                <label for="conf_password">Confirm New Password</label>
                <input type="password" name="conf_password" id="conf_password" class="form-control" placeholder="Verify new password" required>
            </div>
            
            <button type="submit" name="update_password" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Update Security Password</button>
        </form>
    </div>
</div>

<?php 
// Include footer
require_once "footer.php";
?>
