<?php
// Define page meta variables for the header
$page_title = "Lodge Complaint";
$page_desc = "Submit a new grievance to the college administration";

// Include header
require_once "header.php";
require_once "../config.php";

$success_msg = "";
$error_msg = "";
$subject = "";
$description = "";
$category_id = "";

// Fetch categories for dropdown
$categories = [];
$cat_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$cat_result = mysqli_query($conn, $cat_sql);
if($cat_result){
    while($row = mysqli_fetch_assoc($cat_result)){
        $categories[] = $row;
    }
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $category_id = $_POST["category_id"];
    $subject = trim($_POST["subject"]);
    $description = trim($_POST["description"]);
    $user_id = $_SESSION["id"];
    $attachment_name = null;
    
    // Validate inputs
    if(empty($category_id) || empty($subject) || empty($description)){
        $error_msg = "Please fill in all required fields.";
    } else {
        $upload_ok = true;
        
        // Handle file upload if present
        if(isset($_FILES["attachment"]) && $_FILES["attachment"]["error"] == 0){
            $target_dir = "../uploads/";
            
            // Create uploads directory if it doesn't exist
            if(!file_exists($target_dir)){
                mkdir($target_dir, 0777, true);
            }
            
            // Sanitize file name by stripping special characters but keeping timestamp and extension
            $orig_name = preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES["attachment"]["name"]));
            $file_name = time() . "_" . $orig_name;
            $target_file = $target_dir . $file_name;
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            
            // Allow certain file formats
            $allowed_types = array("jpg", "jpeg", "png", "pdf", "doc", "docx");
            if(!in_array($file_type, $allowed_types)){
                $error_msg = "Sorry, only JPG, JPEG, PNG, PDF, DOC, & DOCX files are allowed.";
                $upload_ok = false;
            }
            
            // Check file size (5MB limit)
            if($_FILES["attachment"]["size"] > 5000000){
                $error_msg = "Sorry, your file is too large. Max size is 5MB.";
                $upload_ok = false;
            }
            
            if($upload_ok){
                if(move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)){
                    $attachment_name = $file_name;
                } else {
                    $error_msg = "Sorry, there was an error uploading your file.";
                    $upload_ok = false;
                }
            }
        }
        
        // Insert into database if file check passed
        if($upload_ok){
            $sql = "INSERT INTO complaints (user_id, category_id, subject, description, attachment, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "iisss", $user_id, $category_id, $subject, $description, $attachment_name);
                if(mysqli_stmt_execute($stmt)){
                    $success_msg = "Complaint lodged successfully! You can track its progress in history.";
                    // Reset variables
                    $subject = "";
                    $description = "";
                    $category_id = "";
                } else {
                    $error_msg = "Something went wrong. Please try again later.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

mysqli_close($conn);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Complaint Submission Form</h2>
    </div>
    
    <?php 
    if(!empty($success_msg)){
        echo '<div class="alert alert-success">' . $success_msg . '</div>';
    }
    if(!empty($error_msg)){
        echo '<div class="alert alert-danger">' . $error_msg . '</div>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <!-- Category -->
        <div class="form-group">
            <label for="category_id">Complaint Category <span style="color: var(--danger);">*</span></label>
            <select name="category_id" id="category_id" class="form-control" required>
                <option value="">-- Select Category --</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Subject -->
        <div class="form-group">
            <label for="subject">Subject / Title <span style="color: var(--danger);">*</span></label>
            <input type="text" name="subject" id="subject" class="form-control" value="<?php echo htmlspecialchars($subject); ?>" placeholder="Brief title of the issue" required>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label for="description">Detailed Description <span style="color: var(--danger);">*</span></label>
            <textarea name="description" id="description" class="form-control" placeholder="Provide complete details about the issue..." required><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <!-- Attachment -->
        <div class="form-group">
            <label for="attachment">Upload Attachment <span style="font-weight: normal; font-size: 0.8rem; color: var(--gray-500);">(Optional - Image, PDF, or Word Doc. Max 5MB)</span></label>
            <input type="file" name="attachment" id="attachment" class="form-control" style="padding: 0.5rem;">
        </div>

        <div style="margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">Submit Complaint</button>
            <button type="reset" class="btn btn-secondary">Reset Form</button>
        </div>
    </form>
</div>

<?php 
// Include footer
require_once "footer.php";
?>
