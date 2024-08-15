<?php
class Post {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function getPost($post_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        return $stmt->fetch();
    }

    public function getComments($post_id) {
        $stmt = $this->pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at ASC");
        $stmt->execute([$post_id]);
        return $stmt->fetchAll();
    }

    public function addComment($post_id, $user_id, $content) {
        $stmt = $this->pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        return $stmt->execute([$post_id, $user_id, $content]);
    }
    
    public function addPost($title, $content, $user_id, $board_id, $file_name = null) {
        $stmt = $this->pdo->prepare("INSERT INTO posts (title, content, user_id, board_id, file_name) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$title, $content, $user_id, $board_id, $file_name]);
    }
    
    public function getPostFile($post_id) {
        $stmt = $this->pdo->prepare("SELECT file_name FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        return $stmt->fetchColumn();
    }
}
