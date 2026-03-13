import React, { useState, useMemo } from 'react';
import { Plus, Search, FileText } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { useNotes } from './hooks/useNotes';
import NoteCard from './components/NoteCard';
import NoteModal from './components/NoteModal';

function App() {
  const { notes, addNote, updateNote, deleteNote, togglePin } = useNotes();
  const [search, setSearch] = useState('');
  const [isOpen, setIsOpen] = useState(false);
  const [editNote, setEditNote] = useState(null);

  const filtered = useMemo(() => {
    return notes
      .filter(n =>
        n.title.toLowerCase().includes(search.toLowerCase()) ||
        n.content.toLowerCase().includes(search.toLowerCase())
      )
      .sort((a, b) => {
        if (a.isPinned === b.isPinned) return new Date(b.createdAt) - new Date(a.createdAt);
        return a.isPinned ? -1 : 1;
      });
  }, [notes, search]);

  const onEdit = (n) => {
    setEditNote(n);
    setIsOpen(true);
  };

  const onSave = (data) => {
    editNote ? updateNote(data) : addNote(data);
    setEditNote(null);
  };

  return (
    <div className="app">
      <header className="header">
        <div className="wrap flex">
          <div className="logo">
            <div className="icon-bg"><FileText size={20} /></div>
            <span>Notes App</span>
          </div>

          <div className="search">
            <Search size={16} />
            <input
              placeholder="Search notes..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
          </div>

          <button className="btn btn-blue" onClick={() => { setEditNote(null); setIsOpen(true); }}>
            <Plus size={18} /> New Note
          </button>
        </div>
      </header>

      <main className="wrap">
        {filtered.length > 0 ? (
          <div className="grid">
            <AnimatePresence mode="popLayout">
              {filtered.map(n => (
                <NoteCard key={n.id} note={n} onDelete={deleteNote} onEdit={onEdit} onPin={togglePin} />
              ))}
            </AnimatePresence>
          </div>
        ) : (
          <div className="empty">
            <FileText size={50} style={{ opacity: 0.3, marginBottom: '10px' }} />
            <h2>No notes found</h2>
            <p>Start by creating your first note.</p>
          </div>
        )}
      </main>

      <NoteModal
        isOpen={isOpen}
        onClose={() => { setIsOpen(false); setEditNote(null); }}
        onSave={onSave}
        data={editNote}
      />
    </div>
  );
}

export default App;
