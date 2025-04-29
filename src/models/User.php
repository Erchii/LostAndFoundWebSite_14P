<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getUsersByRole($role) {
        $sql = "SELECT id, username, email, first_name, last_name, phone, role, created_at
                FROM users
                WHERE role = ?
                ORDER BY created_at DESC";
                
        return $this->db->select($sql, [$role]);
    }
    
    public function getAllUsers($page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT id, username, email, first_name, last_name, phone, role, created_at
                FROM users
                ORDER BY created_at DESC
                LIMIT ?, ?";
                
        return $this->db->select($sql, [$offset, $limit]);
    }
    
    public function getUserById($id) {
        $sql = "SELECT id, username, email, first_name, last_name, phone, role, created_at
                FROM users
                WHERE id = ?";
                
        return $this->db->selectOne($sql, [$id]);
    }
    
    public function getUserByEmail($email) {
        $sql = "SELECT *
                FROM users
                WHERE email = ?";
                
        return $this->db->selectOne($sql, [$email]);
    }
    
    public function getUserByUsername($username) {
        $sql = "SELECT *
                FROM users
                WHERE username = ?";
                
        return $this->db->selectOne($sql, [$username]);
    }
    
    public function createUser($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        
        return $this->db->insert('users', $data);
    }
    
    public function updateUser($id, $data) {
        // If password is being updated, hash it
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            // Don't update password if empty
            unset($data['password']);
        }
        
        return $this->db->update('users', $data, 'id = ?', [$id]);
    }
    
    public function deleteUser($id) {
        return $this->db->delete('users', 'id = ?', [$id]);
    }
    
    public function verifyPassword($user, $password) {
        return password_verify($password, $user['password']);
    }
    
    public function countAllUsers() {
        $sql = "SELECT COUNT(*) as total FROM users";
        $result = $this->db->selectOne($sql);
        
        return $result['total'];
    }
    
    public function countUsersByRole($role) {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = ?";
        $result = $this->db->selectOne($sql, [$role]);
        
        return $result['total'];
    }
    
    public function searchUsers($keyword, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT id, username, email, first_name, last_name, phone, role, created_at
                FROM users
                WHERE username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?
                ORDER BY created_at DESC
                LIMIT ?, ?";
                
        $keyword = '%' . $keyword . '%';
        
        return $this->db->select($sql, [$keyword, $keyword, $keyword, $keyword, $offset, $limit]);
    }
    
    public function countSearchResults($keyword) {
        $sql = "SELECT COUNT(*) as total
                FROM users
                WHERE username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?";
                
        $keyword = '%' . $keyword . '%';
        
        $result = $this->db->selectOne($sql, [$keyword, $keyword, $keyword, $keyword]);
        
        return $result['total'];
    }
}