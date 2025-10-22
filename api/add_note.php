<?php
// api/add_note.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if ($title === '' || $content === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Title and content are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO notes (title, content) VALUES (:title, :content)");
    $stmt->execute([':title' => $title, ':content' => $content]);
    $id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("SELECT * FROM notes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $note = $stmt->fetch();

    echo json_encode(['success' => true, 'note' => $note]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
