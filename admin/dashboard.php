<?php
// Define page meta variables for the header
$page_title = "Admin Dashboard";
$page_desc = "Quick statistics and recent complaints submitted across the institution";

// Include header
require_once "header.php";
require_once "../config.php";

// Fetch statistics
$total_complaints = 0;
$pending_complaints = 0;
$progress_complaints = 0;
$closed_complaints = 0;
$total_users = 0;
$total_categories = 0;

// Total Complaints
$sql = "SELECT COUNT(*) FROM complaints";
$result = mysqli_query($conn, $sql);
if($result) {
    $row = mysqli_fetch_array($result);
    $total_complaints = $row[0];
}

// Pending Complaints
$sql = "SELECT COUNT(*) FROM complaints WHERE status = 'Pending'";
$result = mysqli_query($conn, $sql);
if($result) {
    $row = mysqli_fetch_array($result);
    $pending_complaints = $row[0];
}

// In Progress Complaints
$sql = "SELECT COUNT(*) FROM complaints WHERE status = 'In Progress'";
$result = mysqli_query($conn, $sql);
if($result) {
    $row = mysqli_fetch_array($result);
    $progress_complaints = $row[0];
}

// Closed Complaints
$sql = "SELECT COUNT(*) FROM complaints WHERE status = 'Closed'";
$result = mysqli_query($conn, $sql);
if($result) {
    $row = mysqli_fetch_array($result);
    $closed_complaints = $row[0];
}

// Total Users
$sql = "SELECT COUNT(*) FROM users";
$result = mysqli_query($conn, $sql);
if($result) {
    $row = mysqli_fetch_array($result);
    $total_users = $row[0];
}

// Total Categories
$sql = "SELECT COUNT(*) FROM categories";
$result = mysqli_query($conn, $sql);
if($result) {
    $row = mysqli_fetch_array($result);
    $total_categories = $row[0];
}

// Fetch recent 5 complaints
$recent_complaints = [];
$sql = "SELECT c.id, c.subject, c.status, c.created_at, cat.name AS category_name, u.fullname AS user_fullname 
        FROM complaints c 
        JOIN categories cat ON c.category_id = cat.id 
        JOIN users u ON c.user_id = u.id
        ORDER BY c.created_at DESC LIMIT 5";

$result = mysqli_query($conn, $sql);
if($result) {
    while($row = mysqli_fetch_assoc($result)){
        $recent_complaints[] = $row;
    }
}

mysqli_close($conn);
?>

<!-- Statistics Panel -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Complaints</h3>
            <div class="stat-number"><?php echo $total_complaints; ?></div>
        </div>
        <div class="stat-icon total">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Pending</h3>
            <div class="stat-number"><?php echo $pending_complaints; ?></div>
        </div>
        <div class="stat-icon pending">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>In Progress</h3>
            <div class="stat-number"><?php echo $progress_complaints; ?></div>
        </div>
        <div class="stat-icon progress">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path></svg>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Resolved</h3>
            <div class="stat-number"><?php echo $closed_complaints; ?></div>
        </div>
        <div class="stat-icon resolved">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
    </div>
</div>

<!-- Secondary Statistics Grid -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); margin-bottom: 2rem;">
    <div class="stat-card" style="border-left: 4px solid var(--secondary);">
        <div class="stat-info">
            <h3>Total Registered Students/Faculty</h3>
            <div class="stat-number" style="font-size: 1.75rem;"><?php echo $total_users; ?></div>
        </div>
        <a href="manage-users.php" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">View Directory</a>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--primary);">
        <div class="stat-info">
            <h3>Complaint Categories</h3>
            <div class="stat-number" style="font-size: 1.75rem;"><?php echo $total_categories; ?></div>
        </div>
        <a href="manage-categories.php" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Manage Categories</a>
    </div>
</div>

<div class="dashboard-row" style="grid-template-columns: 1fr;">
    <!-- Recent Complaints Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Recent Submissions (All Portals)</h2>
            <a href="manage-complaints.php" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">View All Grievances</a>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
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
                            <td colspan="7" class="text-center" style="padding: 2rem; color: var(--gray-400);">No complaints have been lodged in the system yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($recent_complaints as $complaint): ?>
                            <tr>
                                <td>#COMP-<?php echo $complaint['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($complaint['user_fullname']); ?></strong></td>
                                <td><?php echo htmlspecialchars($complaint['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($complaint['subject']); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($complaint['created_at'])); ?></td>
                                <td>
                                    <?php 
                                    $status = $complaint['status'];
                                    $badge_class = 'pending';
                                    if ($status == 'In Progress') $badge_class = 'progress';
                                    if ($status == 'Closed') $badge_class = 'resolved';
                                    
                                    $display_status = ($status == 'Closed') ? 'Resolved' : $status;
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $display_status; ?></span>
                                </td>
                                <td>
                                    <a href="complaint-details.php?id=<?php echo $complaint['id']; ?>" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Review & Respond</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
// Include footer
require_once "footer.php";
?>
