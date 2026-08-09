<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Complaint Portal - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-card landing-container">
            <div class="auth-header">
                <h1>College Complaint Portal</h1>
                <p>Welcome! Please select your portal to proceed.</p>
            </div>
            
            <div class="portal-selector">
                <!-- Student / Faculty Card -->
                <div class="portal-option" onclick="location.href='login.php'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <h3>Student & Faculty</h3>
                    <p>Lodge new complaints, track existing issues, and update your profile.</p>
                    <div class="d-flex gap-2">
                        <a href="login.php" class="btn btn-primary" style="padding: 0.5rem 1rem;">Login</a>
                        <a href="register.php" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Register</a>
                    </div>
                </div>

                <!-- Admin Card -->
                <div class="portal-option" onclick="location.href='admin/login.php'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <h3>Administrator</h3>
                    <p>Review complaints, update status, add resolution remarks, and manage categories.</p>
                    <a href="admin/login.php" class="btn btn-primary" style="padding: 0.5rem 1rem; width: 100%;">Admin Login</a>
                </div>
            </div>
            
            <div class="auth-footer">
                <p>&copy; <?php echo date('Y'); ?> College Complaint Portal. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
