<?php
// index.php
require_once __DIR__ . '/db.php';

// fetch notes
$stmt = $pdo->query("SELECT * FROM notes ORDER BY created_at DESC");
$notes = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Notes App</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="container">
    <h1>Notes App</h1>

    <section class="add-note">
      <h2>Add Note</h2>
      <form id="addNoteForm">
        <input type="text" id="noteTitle" name="title" placeholder="Title" required />
        <textarea id="noteContent" name="content" placeholder="Write your note..." rows="4" required></textarea>
        <button type="submit">Add Note</button>
      </form>
    </section>

    <section class="notes-list">
      <h2>Your Notes</h2>
      <div id="notesContainer">
        <?php if (empty($notes)): ?>
          <p class="empty">No notes yet — add one above.</p>
        <?php else: ?>
          <?php foreach ($notes as $note): ?>
            <div class="note" data-id="<?=htmlspecialchars($note['id'])?>">
              <div class="note-head">
                <strong class="note-title"><?=htmlspecialchars($note['title'])?></strong>
                <div class="note-actions">
                  <button class="edit-btn" data-id="<?=htmlspecialchars($note['id'])?>">Edit</button>
                  <button class="delete-btn" data-id="<?=htmlspecialchars($note['id'])?>">Delete</button>
                </div>
              </div>
              <p class="note-content"><?=nl2br(htmlspecialchars($note['content']))?></p>
              <small class="note-meta">Created: <?=htmlspecialchars($note['created_at'])?>
                <?= $note['updated_at'] ? ' • Updated: '.htmlspecialchars($note['updated_at']) : '' ?></small>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </div>

  <!-- Edit Modal (hidden) -->
  <div id="editModal" class="modal hidden">
    <div class="modal-inner">
      <h3>Edit Note</h3>
      <form id="editNoteForm">
        <input type="hidden" id="editNoteId" name="id" />
        <input type="text" id="editNoteTitle" name="title" required />
        <textarea id="editNoteContent" name="content" rows="5" required></textarea>
        <div class="modal-actions">
          <button type="button" id="closeModal">Cancel</button>
          <button type="submit">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script src="js/app.js"></script>
</body>
</html>
