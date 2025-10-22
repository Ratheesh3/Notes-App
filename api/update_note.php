<?php
// api/update_note.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if ($id <= 0 || $title === '' || $content === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE notes SET title = :title, content = :content, updated_at = NOW() WHERE id = :id");
    $stmt->execute([':title' => $title, ':content' => $content, ':id' => $id]);

    $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $note = $stmt->fetch();

    echo json_encode(['success' => true, 'note' => $note]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
