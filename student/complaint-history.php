<?php
// Define page meta variables for the header
$page_title = "Complaint History";
$page_desc = "View and track all complaints you have submitted";

// Include header
require_once "header.php";
require_once "../config.php";

$user_id = $_SESSION["id"];

// Get status filter if set
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';

// Construct query based on status filter
$complaints = [];
if ($status_filter != 'All') {
    $sql = "SELECT c.id, c.subject, c.status, c.created_at, cat.name AS category_name 
            FROM complaints c 
            JOIN categories cat ON c.category_id = cat.id 
            WHERE c.user_id = ? AND c.status = ?
            ORDER BY c.created_at DESC";
} else {
    $sql = "SELECT c.id, c.subject, c.status, c.created_at, cat.name AS category_name 
            FROM complaints c 
            JOIN categories cat ON c.category_id = cat.id 
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC";
}

if($stmt = mysqli_prepare($conn, $sql)){
    if ($status_filter != 'All') {
        mysqli_stmt_bind_param($stmt, "is", $user_id, $status_filter);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
    }
    
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)){
            $complaints[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!-- Filter Bar -->
<div class="card" style="padding: 1rem; margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="font-weight: 600; color: var(--gray-600);">Filter by Status:</div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="complaint-history.php?status=All" class="btn <?php echo ($status_filter == 'All') ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 0.4rem 1rem; font-size: 0.85rem;">All</a>
            <a href="complaint-history.php?status=Pending" class="btn <?php echo ($status_filter == 'Pending') ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 0.4rem 1rem; font-size: 0.85rem;">Pending</a>
            <a href="complaint-history.php?status=In Progress" class="btn <?php echo ($status_filter == 'In Progress') ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 0.4rem 1rem; font-size: 0.85rem;">In Progress</a>
            <a href="complaint-history.php?status=Closed" class="btn <?php echo ($status_filter == 'Closed') ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 0.4rem 1rem; font-size: 0.85rem;">Resolved</a>
        </div>
    </div>
</div>

<!-- History Table -->
<div class="card">
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
                <?php if(empty($complaints)): ?>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 3rem; color: var(--gray-400);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem; color: var(--gray-300);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <p>No <?php echo ($status_filter != 'All') ? strtolower($status_filter) . ' ' : ''; ?>complaints found.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($complaints as $complaint): ?>
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
                                
                                $display_status = ($status == 'Closed') ? 'Resolved' : $status;
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo $display_status; ?></span>
                            </td>
                            <td>
                                <a href="complaint-details.php?id=<?php echo $complaint['id']; ?>" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
// Include footer
require_once "footer.php";
?>
