<?php
class User {
    private $db;
    private $table = 'users';
    
    public $id;
    public $username;
    public $email;
    public $password;
    public $created_at;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function register() {
        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        $data = [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $hashed_password
        ];
        try {
            $insert_id = $this->db->insert($this->table, $data);
            return $insert_id > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function login($username, $password) {
        $result = $this->db->select($this->table, 'id, username, email, password', 'username = ? OR email = ?', 'ss', [$username, $username]);
        $user = $result ? $result->fetch_assoc() : false;
        if($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
    public function emailExists($email) {
        $result = $this->db->select($this->table, 'id', 'email = ?', 's', [$email]);
        return $result && $result->num_rows > 0;
    }
    
    public function usernameExists($username) {
        $result = $this->db->select($this->table, 'id', 'username = ?', 's', [$username]);
        return $result && $result->num_rows > 0;
    }
}