<?php
require_once 'config/database.php';
require_once 'classes/FileUpload.php';
require_once 'includes/session.php';

requireLogin();

$database = new Database();
$fileUpload = new FileUpload($database);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $errors[] = "Invalid CSRF token";
    } else {
        $result = $fileUpload->upload($_FILES['file'], $_SESSION['user_id']);
        
        if ($result['success']) {
            $success = "File uploaded successfully!";
        } else {
            $errors = $result['errors'];
        }
    }
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file_id'])) {
    $delete_file_id = (int)$_POST['delete_file_id'];
    // Fetch file info and ensure it belongs to the current user
    $result = $database->select('files', '*', 'id = ? AND user_id = ?', 'ii', [$delete_file_id, $_SESSION['user_id']]);
    $file = $result ? $result->fetch_assoc() : null;
    if ($file) {
        // Delete from DB
        $database->delete('files', 'id = ? AND user_id = ?', 'ii', [$delete_file_id, $_SESSION['user_id']]);
        // Delete physical file
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }
        $success = 'File deleted successfully.';
    } else {
        $errors[] = 'File not found or permission denied.';
    }
}

$userFiles = $fileUpload->getUserFiles($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - User System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">User System</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Upload File</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="mb-3">
                                <label for="file" class="form-label">Choose File</label>
                                <input type="file" class="form-control" id="file" name="file" required>
                                <div class="form-text">
                                    Allowed types: JPG, PNG, GIF, PDF, DOC, DOCX, TXT (Max 5MB)
                                </div>
                            </div>
                            
                            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Your Files</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($userFiles)): ?>
                            <p>No files uploaded yet.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($userFiles as $file): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($file['original_name']); ?></h6>
                                                <small class="text-muted">
                                                    Uploaded on <?php echo date('M d, Y H:i', strtotime($file['upload_date'])); ?>
                                                </small>
                                            </div>
                                            <div>
                                                <a class="btn btn-sm btn-outline-primary me-2" href="uploads/<?php echo rawurlencode($file['filename']); ?>" target="_blank">View</a>
                                                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this file?');">
                                                    <input type="hidden" name="delete_file_id" value="<?php echo (int)$file['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>