<?php
// Define page meta variables for the header
$page_title = "Complaint Details";
$page_desc = "View full information and resolution notes for your grievance";

// Include header
require_once "header.php";
require_once "../config.php";

$user_id = $_SESSION["id"];

// Check if ID parameter is present
if(!isset($_GET["id"]) || empty(trim($_GET["id"]))){
    echo "<div class='alert alert-danger'>Invalid Complaint ID.</div>";
    require_once "footer.php";
    exit;
}

$complaint_id = trim($_GET["id"]);

// Fetch complaint details
$complaint = null;
$sql = "SELECT c.*, cat.name AS category_name 
        FROM complaints c 
        JOIN categories cat ON c.category_id = cat.id 
        WHERE c.id = ? AND c.user_id = ?";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "ii", $complaint_id, $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result) == 1){
            $complaint = mysqli_fetch_assoc($result);
        } else {
            echo "<div class='alert alert-danger'>Complaint not found or you are not authorized to view it.</div>";
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
    <a href="complaint-history.php" class="btn btn-secondary" style="padding: 0.5rem 1rem;">&larr; Back to History</a>
</div>

<div class="card">
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

    <!-- Subject & Description -->
    <div class="complaint-section-title">Subject</div>
    <div style="font-size: 1.1rem; font-weight: 600; color: var(--dark); margin-bottom: 1.5rem;">
        <?php echo htmlspecialchars($complaint['subject']); ?>
    </div>

    <div class="complaint-section-title">Detailed Description</div>
    <div style="background-color: var(--gray-100); padding: 1.5rem; border-radius: var(--border-radius-sm); color: var(--gray-600); white-space: pre-line; line-height: 1.7; font-size: 0.95rem;">
        <?php echo htmlspecialchars($complaint['description']); ?>
    </div>

    <!-- Admin Remarks Section -->
    <?php if($complaint['status'] != 'Pending' || !empty($complaint['remark'])): ?>
        <div class="complaint-section-title">Admin Remarks / Updates</div>
        <div class="remark-box <?php echo ($complaint['status'] == 'Closed') ? 'closed' : ''; ?>">
            <div style="font-weight: 700; font-size: 0.95rem; display: flex; justify-content: space-between; align-items: center;">
                <span>
                    <?php echo ($complaint['status'] == 'Closed') ? 'Final Resolution' : 'Update Remark'; ?>
                </span>
                <span style="font-size: 0.75rem; font-weight: 500; color: var(--gray-500);">
                    Updated on: <?php echo date('d M Y, h:i A', strtotime($complaint['remark_date'])); ?>
                </span>
            </div>
            <div style="margin-top: 8px; color: var(--gray-600); font-size: 0.9rem; line-height: 1.6; white-space: pre-line;">
                <?php echo !empty($complaint['remark']) ? htmlspecialchars($complaint['remark']) : 'No details provided by the administrator yet.'; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php 
// Include footer
require_once "footer.php";
?>
