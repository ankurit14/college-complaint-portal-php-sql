<?php
// Define page meta variables for the header
$page_title = "Review Grievance";
$page_desc = "Examine student complaint details and submit status updates";

// Include header
require_once "header.php";
require_once "../config.php";

$success_msg = "";
$error_msg = "";

// Check if ID parameter is present
if(!isset($_GET["id"]) || empty(trim($_GET["id"]))){
    echo "<div class='alert alert-danger'>Invalid Complaint ID.</div>";
    require_once "footer.php";
    exit;
}

$complaint_id = trim($_GET["id"]);

// Process status and remark updates
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_status"])){
    $new_status = $_POST["status"];
    $remark = trim($_POST["remark"]);
    
    if(empty($new_status)){
        $error_msg = "Please select a valid status.";
    } else {
        $sql = "UPDATE complaints SET status = ?, remark = ?, remark_date = CURRENT_TIMESTAMP WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ssi", $new_status, $remark, $complaint_id);
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Complaint status and remarks updated successfully!";
            } else {
                $error_msg = "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Fetch complaint details with student info
$complaint = null;
$sql = "SELECT c.*, cat.name AS category_name, u.fullname AS user_fullname, u.email AS user_email, u.contact_no AS user_contact, u.roll_number AS user_roll, u.user_type AS user_role
        FROM complaints c 
        JOIN categories cat ON c.category_id = cat.id 
        JOIN users u ON c.user_id = u.id
        WHERE c.id = ?";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $complaint_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result) == 1){
            $complaint = mysqli_fetch_assoc($result);
        } else {
            echo "<div class='alert alert-danger'>Complaint not found.</div>";
            require_once "footer.php";
            exit;
        }
    } else {
        echo "<div class='alert alert-danger'>Oops! Something went wrong. Please try again later.</div>";
        require_once "footer.php";
        exit;
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<div style="margin-bottom: 1.5rem;">
    <a href="manage-complaints.php" class="btn btn-secondary" style="padding: 0.5rem 1rem;">&larr; Back to Complaints List</a>
</div>

<?php 
if(!empty($success_msg)){
    echo '<div class="alert alert-success">' . $success_msg . '</div>';
}
if(!empty($error_msg)){
    echo '<div class="alert alert-danger">' . $error_msg . '</div>';
}
?>

<div class="dashboard-row">
    <!-- Complaint Info Panel -->
    <div class="card" style="margin-bottom: 0;">
        <div class="complaint-header-info">
            <div>
                <h2 style="font-size: 1.4rem; font-weight: 700;">#COMP-<?php echo $complaint['id']; ?></h2>
                <div style="color: var(--gray-500); font-size: 0.85rem; margin-top: 4px;">
                    Category: <strong><?php echo htmlspecialchars($complaint['category_name']); ?></strong>
                </div>
            </div>
            <div>
                <?php 
                $status = $complaint['status'];
                $badge_class = 'pending';
                if ($status == 'In Progress') $badge_class = 'progress';
                if ($status == 'Closed') $badge_class = 'resolved';
                
                $display_status = ($status == 'Closed') ? 'Resolved' : $status;
                ?>
                <span class="badge <?php echo $badge_class; ?>" style="font-size: 0.85rem; padding: 0.4rem 1rem;"><?php echo $display_status; ?></span>
            </div>
        </div>

        <!-- Student details card block -->
        <div style="background-color: var(--primary-light); padding: 1rem; border-radius: var(--border-radius-sm); border: 1px solid rgba(79, 70, 229, 0.1); margin-bottom: 1.5rem;">
            <h4 style="color: var(--primary); margin-bottom: 0.5rem; font-weight: 700;">Submitted By Details</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.85rem;">
                <div><strong>Name:</strong> <?php echo htmlspecialchars($complaint['user_fullname']); ?> (<?php echo ucfirst($complaint['user_role']); ?>)</div>
                <div><strong>Roll Number/ID:</strong> <?php echo htmlspecialchars($complaint['user_roll']); ?></div>
                <div><strong>Email:</strong> <?php echo htmlspecialchars($complaint['user_email']); ?></div>
                <div><strong>Contact No:</strong> <?php echo htmlspecialchars($complaint['user_contact']); ?></div>
            </div>
        </div>

        <!-- Metadata Grid -->
        <div class="meta-info-grid">
            <div class="meta-item">
                <span class="meta-label">Date Lodged:</span>
                <div class="meta-value"><?php echo date('d M Y, h:i A', strtotime($complaint['created_at'])); ?></div>
            </div>
            <div class="meta-item">
                <span class="meta-label">Last Updated:</span>
                <div class="meta-value"><?php echo date('d M Y, h:i A', strtotime($complaint['updated_at'])); ?></div>
            </div>
            <div class="meta-item">
                <span class="meta-label">Attachment:</span>
                <div class="meta-value">
                    <?php if($complaint['attachment']): ?>
                        <a href="../uploads/<?php echo urlencode($complaint['attachment']); ?>" target="_blank" class="attachment-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
                            View File
                        </a>
                    <?php else: ?>
                        <span style="color: var(--gray-400);">No attachment uploaded</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="complaint-section-title">Subject</div>
        <div style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 1.5rem;">
            <?php echo htmlspecialchars($complaint['subject']); ?>
        </div>

        <div class="complaint-section-title">Detailed Description</div>
        <div style="background-color: var(--gray-100); padding: 1.5rem; border-radius: var(--border-radius-sm); color: var(--gray-600); white-space: pre-line; line-height: 1.7; font-size: 0.95rem; margin-bottom: 1.5rem;">
            <?php echo htmlspecialchars($complaint['description']); ?>
        </div>

        <?php if(!empty($complaint['remark'])): ?>
            <div class="complaint-section-title">Current Resolution / Remarks</div>
            <div class="remark-box <?php echo ($complaint['status'] == 'Closed') ? 'closed' : ''; ?>">
                <div style="font-weight: 700; font-size: 0.95rem; display: flex; justify-content: space-between; align-items: center;">
                    <span>Last Saved Remark</span>
                    <span style="font-size: 0.75rem; font-weight: 500; color: var(--gray-500);">
                        Updated on: <?php echo date('d M Y, h:i A', strtotime($complaint['remark_date'])); ?>
                    </span>
                </div>
                <div style="margin-top: 8px; color: var(--gray-600); font-size: 0.9rem; line-height: 1.6; white-space: pre-line;">
                    <?php echo htmlspecialchars($complaint['remark']); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Response Panel -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title">Take Action</h2>
        </div>
        <form action="complaint-details.php?id=<?php echo $complaint['id']; ?>" method="post">
            <div class="form-group">
                <label for="status">Change Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Pending" <?php echo ($complaint['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="In Progress" <?php echo ($complaint['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="Closed" <?php echo ($complaint['status'] == 'Closed') ? 'selected' : ''; ?>>Closed (Resolved)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="remark">Resolution Remarks / Feedback</label>
                <textarea name="remark" id="remark" class="form-control" placeholder="Provide notes on the progress or instructions on how this issue has been resolved..." required><?php echo htmlspecialchars($complaint['remark'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" name="update_status" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Update Complaint Status</button>
        </form>
        
        <div style="background-color: var(--warning-light); padding: 1rem; border-radius: var(--border-radius-sm); border: 1px solid rgba(245, 158, 11, 0.2); margin-top: 1.5rem; font-size: 0.8rem; color: var(--gray-600);">
            <strong>Notice:</strong> Submitting updates instantly updates the status and notifies the student/faculty dashboard. Closing a complaint means the dispute/grievance is officially marked as Resolved.
        </div>
    </div>
</div>

<?php 
// Include footer
require_once "footer.php";
?>
