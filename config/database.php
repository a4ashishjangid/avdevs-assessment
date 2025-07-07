<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'assessment_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }

    // Generic query method
    public function query($sql, $types = '', $params = []) {
        $conn = $this->conn ?: $this->connect();
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        if ($types && $params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Select helper
    public function select($table, $columns = '*', $where = '', $types = '', $params = []) {
        $sql = "SELECT $columns FROM $table";
        if ($where) {
            $sql .= " WHERE $where";
        }
        return $this->query($sql, $types, $params);
    }

    // Insert helper
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $types = str_repeat('s', count($data)); // All strings by default
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $params = array_values($data);
        $conn = $this->conn ?: $this->connect();
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $insert_id = $stmt->insert_id;
        $stmt->close();
        return $insert_id;
    }

    // Update helper
    public function update($table, $data, $where, $types, $params) {
        $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $sql = "UPDATE $table SET $set WHERE $where";
        $allParams = array_merge(array_values($data), $params);
        $allTypes = str_repeat('s', count($data)) . $types;
        $conn = $this->conn ?: $this->connect();
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param($allTypes, ...$allParams);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    // Delete helper
    public function delete($table, $where, $types, $params) {
        $sql = "DELETE FROM $table WHERE $where";
        $conn = $this->conn ?: $this->connect();
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }
}