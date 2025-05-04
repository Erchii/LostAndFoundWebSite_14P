<?php

// Item model manages database operations for items, including retrieval, creation, updating, deletion, and search.
// It supports filtering by type, status, user, category, and date, as well as pagination and counting results.
// Also handles categories and contact records related to items.


class Item {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAllItems($page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT i.*, c.name as category_name, u.username
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN users u ON i.user_id = u.id
                ORDER BY i.created_at DESC
                LIMIT ?, ?";
                
        return $this->db->select($sql, [$offset, $limit]);
    }
    
    public function getItemsByType($type, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT i.*, c.name as category_name, u.username
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN users u ON i.user_id = u.id
                WHERE i.type = ?
                ORDER BY i.created_at DESC
                LIMIT ?, ?";
                
        return $this->db->select($sql, [$type, $offset, $limit]);
    }
    
    public function getItemsByUser($userId, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT i.*, c.name as category_name
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                WHERE i.user_id = ?
                ORDER BY i.created_at DESC
                LIMIT ?, ?";
                
        return $this->db->select($sql, [$userId, $offset, $limit]);
    }
    
    public function getItemById($id) {
        $sql = "SELECT i.*, c.name as category_name, u.username, u.email, u.phone
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN users u ON i.user_id = u.id
                WHERE i.id = ?";
                
        return $this->db->selectOne($sql, [$id]);
    }
    
    public function createItem($data) {
        return $this->db->insert('items', $data);
    }
    
    public function updateItem($id, $data) {
        return $this->db->update('items', $data, 'id = ?', [$id]);
    }
    
    public function deleteItem($id) {
        return $this->db->delete('items', 'id = ?', [$id]);
    }
    
    public function searchItems($filters, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        $params = [];
        $whereConditions = [];
        
        // Process filters
        if (!empty($filters['type'])) {
            $whereConditions[] = "i.type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['category_id'])) {
            $whereConditions[] = "i.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $whereConditions[] = "i.status = ?";
            $params[] = $filters['status'];
        } else {
            // By default, show only verified and resolved items for public search
            $whereConditions[] = "i.status IN ('verified', 'resolved')";
        }
        
        if (!empty($filters['date_from'])) {
            $whereConditions[] = "i.date_lost_found >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $whereConditions[] = "i.date_lost_found <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['location'])) {
            $whereConditions[] = "i.location LIKE ?";
            $params[] = '%' . $filters['location'] . '%';
        }
        
        if (!empty($filters['keyword'])) {
            $whereConditions[] = "(i.title LIKE ? OR i.description LIKE ?)";
            $params[] = '%' . $filters['keyword'] . '%';
            $params[] = '%' . $filters['keyword'] . '%';
        }
        
        // Build query
        $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
        
        $sql = "SELECT i.*, c.name as category_name, u.username
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN users u ON i.user_id = u.id
                $whereClause
                ORDER BY i.created_at DESC
                LIMIT ?, ?";
                
        $params[] = $offset;
        $params[] = $limit;
        
        return $this->db->select($sql, $params);
    }
    
    public function countAllItems() {
        $sql = "SELECT COUNT(*) as total FROM items";
        $result = $this->db->selectOne($sql);
        
        return $result['total'];
    }
    
    public function countItemsByType($type) {
        $sql = "SELECT COUNT(*) as total FROM items WHERE type = ?";
        $result = $this->db->selectOne($sql, [$type]);
        
        return $result['total'];
    }
    
    public function countItemsByUser($userId) {
        $sql = "SELECT COUNT(*) as total FROM items WHERE user_id = ?";
        $result = $this->db->selectOne($sql, [$userId]);
        
        return $result['total'];
    }
    
    public function countItemsByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM items WHERE status = ?";
        $result = $this->db->selectOne($sql, [$status]);
        
        return $result['total'];
    }
    
    public function countSearchResults($filters) {
        $params = [];
        $whereConditions = [];
        
        // Process filters (same as in searchItems)
        if (!empty($filters['type'])) {
            $whereConditions[] = "i.type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['category_id'])) {
            $whereConditions[] = "i.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $whereConditions[] = "i.status = ?";
            $params[] = $filters['status'];
        } else {
            // By default, show only verified and resolved items for public search
            $whereConditions[] = "i.status IN ('verified', 'resolved')";
        }
        
        if (!empty($filters['date_from'])) {
            $whereConditions[] = "i.date_lost_found >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $whereConditions[] = "i.date_lost_found <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['location'])) {
            $whereConditions[] = "i.location LIKE ?";
            $params[] = '%' . $filters['location'] . '%';
        }
        
        if (!empty($filters['keyword'])) {
            $whereConditions[] = "(i.title LIKE ? OR i.description LIKE ?)";
            $params[] = '%' . $filters['keyword'] . '%';
            $params[] = '%' . $filters['keyword'] . '%';
        }
        
        // Build query
        $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
        
        $sql = "SELECT COUNT(*) as total
                FROM items i
                LEFT JOIN categories c ON i.category_id = c.id
                LEFT JOIN users u ON i.user_id = u.id
                $whereClause";
                
        $result = $this->db->selectOne($sql, $params);
        
        return $result['total'];
    }
    
    public function getCategories() {
        $sql = "SELECT * FROM categories ORDER BY name";
        return $this->db->select($sql);
    }
    
    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        return $this->db->selectOne($sql, [$id]);
    }
    
    public function createContact($data) {
        return $this->db->insert('contacts', $data);
    }
    
    public function getContactsByItem($itemId) {
        $sql = "SELECT c.*, u.username, u.email, u.phone
                FROM contacts c
                JOIN users u ON c.user_id = u.id
                WHERE c.item_id = ?
                ORDER BY c.created_at DESC";
                
        return $this->db->select($sql, [$itemId]);
    }
    
    public function updateContactStatus($id, $status) {
        return $this->db->update('contacts', ['status' => $status], 'id = ?', [$id]);
    }
}