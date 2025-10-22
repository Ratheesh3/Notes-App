// assets/app.js
document.addEventListener('DOMContentLoaded', () => {
  const addForm = document.getElementById('addNoteForm');
  const notesContainer = document.getElementById('notesContainer');
  const editModal = document.getElementById('editModal');
  const editForm = document.getElementById('editNoteForm');

  // Helpers
  function createNoteHtml(note) {
    const wrapper = document.createElement('div');
    wrapper.className = 'note';
    wrapper.dataset.id = note.id;
    wrapper.innerHTML = `
      <div class="note-head">
        <strong class="note-title">${escapeHtml(note.title)}</strong>
        <div class="note-actions">
          <button class="edit-btn" data-id="${note.id}">Edit</button>
          <button class="delete-btn" data-id="${note.id}">Delete</button>
        </div>
      </div>
      <p class="note-content">${nl2br(escapeHtml(note.content))}</p>
      <small class="note-meta">Created: ${note.created_at}${note.updated_at ? ' • Updated: '+note.updated_at : ''}</small>
    `;
    return wrapper;
  }

  function escapeHtml(s) {
    return (s+'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  function nl2br(s) {
    return s.replace(/\n/g, '<br>');
  }

  // Add note
  addForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(addForm);
    const res = await fetch('api/add_note.php', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();
    if (data.success) {
      // Prepend new note
      const noteEl = createNoteHtml(data.note);
      const empty = notesContainer.querySelector('.empty');
      if (empty) empty.remove();
      notesContainer.prepend(noteEl);
      addForm.reset();
    } else {
      alert(data.error || 'Failed to add note');
    }
  });

  // Delegate clicks for edit / delete
  notesContainer.addEventListener('click', async (e) => {
    const editBtn = e.target.closest('.edit-btn');
    const delBtn = e.target.closest('.delete-btn');

    if (editBtn) {
      const id = editBtn.dataset.id;
      // populate modal with existing values from DOM
      const noteDiv = document.querySelector(`.note[data-id="${id}"]`);
      const title = noteDiv.querySelector('.note-title').textContent;
      const content = noteDiv.querySelector('.note-content').innerText;
      document.getElementById('editNoteId').value = id;
      document.getElementById('editNoteTitle').value = title;
      document.getElementById('editNoteContent').value = content;
      openModal();
      return;
    }

    if (delBtn) {
      const id = delBtn.dataset.id;
      if (!confirm('Delete this note?')) return;
      const formData = new FormData();
      formData.append('id', id);
      const res = await fetch('api/delete_note.php', { method: 'POST', body: formData });
      const data = await res.json();
      if (data.success) {
        const node = document.querySelector(`.note[data-id="${id}"]`);
        if (node) node.remove();
        if (notesContainer.children.length === 0) {
          notesContainer.innerHTML = '<p class="empty">No notes yet — add one above.</p>';
        }
      } else {
        alert(data.error || 'Failed to delete');
      }
    }
  });

  // Edit form submit
  editForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(editForm);
    const res = await fetch('api/update_note.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
      // update DOM
      const note = data.note;
      const node = document.querySelector(`.note[data-id="${note.id}"]`);
      if (node) {
        node.querySelector('.note-title').textContent = note.title;
        node.querySelector('.note-content').innerHTML = nl2br(escapeHtml(note.content));
        const meta = node.querySelector('.note-meta');
        meta.textContent = `Created: ${note.created_at}${note.updated_at ? ' • Updated: '+note.updated_at : ''}`;
      }
      closeModal();
    } else {
      alert(data.error || 'Failed to update');
    }
  });

  // Modal helpers
  function openModal() {
    editModal.classList.remove('hidden');
  }
  function closeModal() {
    editModal.classList.add('hidden');
    editForm.reset();
  }

  document.getElementById('closeModal').addEventListener('click', closeModal);

  // close modal on outside click
  editModal.addEventListener('click', (e) => {
    if (e.target === editModal) closeModal();
  });
});
