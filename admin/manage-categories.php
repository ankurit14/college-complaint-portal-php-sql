<?php
// Define page meta variables for the header
$page_title = "Manage Categories";
$page_desc = "Add, edit, or remove complaint category classifications";

// Include header
require_once "header.php";
require_once "../config.php";

$success_msg = "";
$error_msg = "";

$edit_mode = false;
$edit_id = "";
$edit_name = "";
$edit_description = "";

// 1. Handle Delete Action
if(isset($_GET["delete"]) && !empty(trim($_GET["delete"]))){
    $delete_id = trim($_GET["delete"]);
    
    // Check if complaints are associated with this category
    $check_sql = "SELECT COUNT(*) FROM complaints WHERE category_id = ?";
    $complaints_exist = 0;
    if($stmt = mysqli_prepare($conn, $check_sql)){
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_bind_result($stmt, $complaints_exist);
            mysqli_stmt_fetch($stmt);
        }
        mysqli_stmt_close($stmt);
    }
    
    if ($complaints_exist > 0) {
        $error_msg = "Cannot delete category: There are {$complaints_exist} active complaints cataloged under this category.";
    } else {
        $sql = "DELETE FROM categories WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $delete_id);
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Category deleted successfully!";
            } else {
                $error_msg = "Oops! Something went wrong while deleting. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// 2. Handle Create or Edit Submissions
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Edit Save
    if(isset($_POST["save_edit"])){
        $edit_id = $_POST["category_id"];
        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        
        if(empty($name)){
            $error_msg = "Category name is required.";
        } else {
            $sql = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $edit_id);
                if(mysqli_stmt_execute($stmt)){
                    $success_msg = "Category updated successfully!";
                } else {
                    $error_msg = "Category name must be unique. Choose another name.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
    
    // Create New
    if(isset($_POST["add_new"])){
        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        
        if(empty($name)){
            $error_msg = "Category name is required.";
        } else {
            $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
            if($stmt = mysqli_prepare($conn, $sql)){
                mysqli_stmt_bind_param($stmt, "ss", $name, $description);
                if(mysqli_stmt_execute($stmt)){
                    $success_msg = "New category added successfully!";
                } else {
                    $error_msg = "Category name already exists.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// 3. Check for Edit Trigger to Load Form Values
if(isset($_GET["edit"]) && !empty(trim($_GET["edit"]))){
    $edit_id = trim($_GET["edit"]);
    $sql = "SELECT id, name, description FROM categories WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_assoc($result);
                $edit_name = $row["name"];
                $edit_description = $row["description"];
                $edit_mode = true;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// 4. Fetch All Categories
$categories = [];
$sql = "SELECT * FROM categories ORDER BY name ASC";
$result = mysqli_query($conn, $sql);
if($result) {
    while($row = mysqli_fetch_assoc($result)){
        $categories[] = $row;
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
    <!-- Category List Card -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title">Existing Categories</h2>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($categories)): ?>
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 2rem; color: var(--gray-400);">No categories defined yet. Use the form to add some.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($categories as $cat): ?>
                            <tr>
                                <td><?php echo $cat['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                                <td style="max-width: 250px; font-size: 0.85rem; color: var(--gray-500);"><?php echo htmlspecialchars($cat['description']); ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="manage-categories.php?edit=<?php echo $cat['id']; ?>" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">Edit</a>
                                        <a href="manage-categories.php?delete=<?php echo $cat['id']; ?>" class="btn btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Category Entry/Edit Form -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title"><?php echo $edit_mode ? 'Edit Category' : 'Add New Category'; ?></h2>
        </div>
        <form action="manage-categories.php" method="post">
            <?php if($edit_mode): ?>
                <input type="hidden" name="category_id" value="<?php echo $edit_id; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">Category Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($edit_mode ? $edit_name : ''); ?>" placeholder="e.g. Hostel & Mess" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description / Scope</label>
                <textarea name="description" id="description" class="form-control" placeholder="Define what type of complaints fall under this classification..."><?php echo htmlspecialchars($edit_mode ? $edit_description : ''); ?></textarea>
            </div>
            
            <?php if($edit_mode): ?>
                <button type="submit" name="save_edit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Save Changes</button>
                <a href="manage-categories.php" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem; display: block; text-align: center;">Cancel Edit</a>
            <?php else: ?>
                <button type="submit" name="add_new" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Create Category</button>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php 
// Include footer
require_once "footer.php";
?>
