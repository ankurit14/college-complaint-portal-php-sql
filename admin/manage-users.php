<?php
// Define page meta variables for the header
$page_title = "User Directory";
$page_desc = "Directory of all registered students and faculty members in the portal";

// Include header
require_once "header.php";
require_once "../config.php";

// Fetch all users
$users = [];
$sql = "SELECT id, fullname, email, contact_no, roll_number, user_type, created_at FROM users ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if($result) {
    while($row = mysqli_fetch_assoc($result)){
        $users[] = $row;
    }
}

mysqli_close($conn);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Registered Student & Faculty Members</h2>
    </div>
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Roll Number / ID</th>
                    <th>Email Address</th>
                    <th>Contact No</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 2rem; color: var(--gray-400);">No students or faculty accounts have been registered yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($users as $user): ?>
                        <tr>
                            <td>#USR-<?php echo $user['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($user['fullname']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['roll_number']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['contact_no']); ?></td>
                            <td>
                                <span class="badge <?php echo ($user['user_type'] == 'student') ? 'progress' : 'resolved'; ?>" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                    <?php echo ucfirst($user['user_type']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
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
