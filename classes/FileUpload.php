<?php
class FileUpload {
    private $db;
    private $table = 'files';
    private $upload_dir = 'uploads/';
    private $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'];
    private $max_size = 5242880; // 5MB
    
    public function __construct($db) {
        $this->db = $db;
        // Create upload directory if it doesn't exist
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }
    
    public function upload($file, $user_id) {
        $errors = [];
        
        // Validate file
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $errors[] = "No file selected";
            return ['success' => false, 'errors' => $errors];
        }
        
        $file_size = $file['size'];
        $file_tmp = $file['tmp_name'];
        $file_name = $file['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check file size
        if ($file_size > $this->max_size) {
            $errors[] = "File size exceeds 5MB limit";
        }
        
        // Check file type
        if (!in_array($file_ext, $this->allowed_types)) {
            $errors[] = "File type not allowed";
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Generate unique filename
        $new_filename = uniqid() . '.' . $file_ext;
        $file_path = $this->upload_dir . $new_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Save file info to database
            $data = [
                'user_id' => $user_id,
                'filename' => $new_filename,
                'original_name' => $file_name,
                'file_path' => $file_path
            ];
            try {
                $insert_id = $this->db->insert($this->table, $data);
                if ($insert_id > 0) {
                    return ['success' => true, 'filename' => $new_filename];
                } else {
                    unlink($file_path);
                    $errors[] = "Database error occurred";
                }
            } catch (Exception $e) {
                unlink($file_path);
                $errors[] = "Database error occurred";
            }
        } else {
            $errors[] = "File upload failed";
        }
        
        return ['success' => false, 'errors' => $errors];
    }
    
    public function getUserFiles($user_id) {
        $result = $this->db->select($this->table, '*', 'user_id = ? ORDER BY upload_date DESC', 'i', [$user_id]);
        $files = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $files[] = $row;
            }
        }
        return $files;
    }
}
