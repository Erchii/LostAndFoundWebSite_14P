<?php
class Notification {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function countAllNotifications($userId) {
        $sql = "SELECT COUNT(*) as total
                FROM notifications
                WHERE user_id = ?";
                
        $result = $this->db->selectOne($sql, [$userId]);
        
        return $result['total'];
    }
    
    public function getAllNotifications($userId, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT n.*, i.title as item_title
                FROM notifications n
                LEFT JOIN items i ON n.item_id = i.id
                WHERE n.user_id = ?
                ORDER BY n.created_at DESC
                LIMIT ?, ?";
                
        return $this->db->select($sql, [$userId, $offset, $limit]);
    }
    
    public function getUnreadNotificationsCount($userId) {
        $sql = "SELECT COUNT(*) as total
                FROM notifications
                WHERE user_id = ? AND is_read = 0";
                
        $result = $this->db->selectOne($sql, [$userId]);
        
        return $result['total'];
    }
    
    public function markAsRead($id, $userId) {
        return $this->db->update('notifications', ['is_read' => 1], 'id = ? AND user_id = ?', [$id, $userId]);
    }
    
    public function markAllAsRead($userId) {
        return $this->db->update('notifications', ['is_read' => 1], 'user_id = ?', [$userId]);
    }
    
    public function createNotification($data) {
        return $this->db->insert('notifications', $data);
    }
    
    public function deleteNotification($id) {
        return $this->db->delete('notifications', 'id = ?', [$id]);
    }
    
    public function deleteAllNotifications($userId) {
        return $this->db->delete('notifications', 'user_id = ?', [$userId]);
    }
    
    public function createItemStatusNotification($itemId, $status) {
        // Get item details
        $sql = "SELECT title, user_id FROM items WHERE id = ?";
        $item = $this->db->selectOne($sql, [$itemId]);
        
        if (!$item) {
            return false;
        }
        
        $title = '';
        $message = '';
        
        switch ($status) {
            case 'verified':
                $title = 'Item Verified';
                $message = 'Your item "' . $item['title'] . '" has been verified by an admin and is now visible to other users.';
                break;
            case 'rejected':
                $title = 'Item Rejected';
                $message = 'Your item "' . $item['title'] . '" has been rejected by an admin. Please check the admin notes for more information.';
                break;
            case 'resolved':
                $title = 'Item Resolved';
                $message = 'Your item "' . $item['title'] . '" has been marked as resolved by an admin.';
                break;
            default:
                return false;
        }
        
        $notificationData = [
            'user_id' => $item['user_id'],
            'item_id' => $itemId,
            'title' => $title,
            'message' => $message,
            'is_read' => 0
        ];
        
        return $this->createNotification($notificationData);
    }
    
    public function createContactNotification($contactId) {
        // Get contact details
        $sql = "SELECT c.item_id, c.user_id, c.message, i.title, i.user_id as item_owner_id
                FROM contacts c
                JOIN items i ON c.item_id = i.id
                WHERE c.id = ?";
                
        $contact = $this->db->selectOne($sql, [$contactId]);
        
        if (!$contact) {
            return false;
        }
        
        // Create notification for item owner
        $notificationData = [
            'user_id' => $contact['item_owner_id'],
            'item_id' => $contact['item_id'],
            'title' => 'New Contact Request',
            'message' => 'Someone has contacted you about your item "' . $contact['title'] . '". Check your item details to respond.',
            'is_read' => 0
        ];
        
        return $this->createNotification($notificationData);
    }
    
    public function createContactStatusNotification($contactId, $status) {
        // Get contact details
        $sql = "SELECT c.item_id, c.user_id, i.title
                FROM contacts c
                JOIN items i ON c.item_id = i.id
                WHERE c.id = ?";
                
        $contact = $this->db->selectOne($sql, [$contactId]);
        
        if (!$contact) {
            return false;
        }
        
        $title = '';
        $message = '';
        
        switch ($status) {
            case 'approved':
                $title = 'Contact Request Approved';
                $message = 'Your contact request for "' . $contact['title'] . '" has been approved. The item owner will contact you directly.';
                break;
            case 'rejected':
                $title = 'Contact Request Rejected';
                $message = 'Your contact request for "' . $contact['title'] . '" has been rejected by the item owner.';
                break;
            default:
                return false;
        }
        
        $notificationData = [
            'user_id' => $contact['user_id'],
            'item_id' => $contact['item_id'],
            'title' => $title,
            'message' => $message,
            'is_read' => 0
        ];
        
        return $this->createNotification($notificationData);
    }
}