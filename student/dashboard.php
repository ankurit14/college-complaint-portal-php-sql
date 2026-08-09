<?php
// Define page meta variables for the header
$page_title = "Dashboard";
$page_desc = "Overview of your lodged complaints and their statuses";

// Include header
require_once "header.php";
require_once "../config.php";

$user_id = $_SESSION["id"];

// Fetch statistics
$total_count = $pending_count = $progress_count = $closed_count = 0;

// Total
$sql = "SELECT COUNT(*) FROM complaints WHERE user_id = ?";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_bind_result($stmt, $total_count);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Pending
$sql = "SELECT COUNT(*) FROM complaints WHERE user_id = ? AND status = 'Pending'";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_bind_result($stmt, $pending_count);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);
}

// In Progress
$sql = "SELECT COUNT(*) FROM complaints WHERE user_id = ? AND status = 'In Progress'";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_bind_result($stmt, $progress_count);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Closed
$sql = "SELECT COUNT(*) FROM complaints WHERE user_id = ? AND status = 'Closed'";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_bind_result($stmt, $closed_count);
        mysqli_stmt_fetch($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Fetch recent complaints
$recent_complaints = [];
$sql = "SELECT c.id, c.subject, c.status, c.created_at, cat.name AS category_name 
        FROM complaints c 
        JOIN categories cat ON c.category_id = cat.id 
        WHERE c.user_id = ? 
        ORDER BY c.created_at DESC LIMIT 5";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)){
            $recent_complaints[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!-- Statistics Panel -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Lodged</h3>
            <div class="stat-number"><?php echo $total_count; ?></div>
        </div>
        <div class="stat-icon total">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Pending</h3>
            <div class="stat-number"><?php echo $pending_count; ?></div>
        </div>
        <div class="stat-icon pending">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>In Progress</h3>
            <div class="stat-number"><?php echo $progress_count; ?></div>
        </div>
        <div class="stat-icon progress">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Resolved</h3>
            <div class="stat-number"><?php echo $closed_count; ?></div>
        </div>
        <div class="stat-icon resolved">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
    </div>
</div>

<div class="dashboard-row">
    <!-- Recent Complaints Table -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title">Recent Complaints</h2>
            <a href="complaint-history.php" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">View All</a>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Date Lodged</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($recent_complaints)): ?>
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 2rem; color: var(--gray-400);">No complaints lodged yet. <a href="lodge-complaint.php">Lodge your first complaint now.</a></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($recent_complaints as $complaint): ?>
                            <tr>
                                <td>#COMP-<?php echo $complaint['id']; ?></td>
                                <td><?php echo htmlspecialchars($complaint['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($complaint['created_at'])); ?></td>
                                <td>
                                    <?php 
                                    $status = $complaint['status'];
                                    $badge_class = 'pending';
                                    if ($status == 'In Progress') $badge_class = 'progress';
                                    if ($status == 'Closed') $badge_class = 'resolved';
                                    
                                    // Make user friendly closed label as "Resolved" in UI
                                    $display_status = ($status == 'Closed') ? 'Resolved' : $status;
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $display_status; ?></span>
                                </td>
                                <td>
                                    <a href="complaint-details.php?id=<?php echo $complaint['id']; ?>" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title">Quick Actions</h2>
        </div>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <a href="lodge-complaint.php" class="btn btn-primary" style="justify-content: flex-start; padding: 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Lodge New Complaint
            </a>
            <a href="profile.php" class="btn btn-secondary" style="justify-content: flex-start; padding: 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Update Profile Settings
            </a>
            
            <div style="background-color: var(--primary-light); padding: 1rem; border-radius: var(--border-radius-sm); border: 1px solid rgba(79, 70, 229, 0.1); margin-top: 1rem;">
                <h4 style="color: var(--primary); margin-bottom: 0.5rem; font-weight: 700;">Need Help?</h4>
                <p style="font-size: 0.8rem; color: var(--gray-600);">If you have any emergency issues or queries, contact student support administration at <strong>support@college.edu</strong>.</p>
            </div>
        </div>
    </div>
</div>

<?php 
// Include footer
require_once "footer.php";
?>
