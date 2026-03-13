import React, { useState, useMemo } from 'react';
import { Plus, Search, FileText } from 'lucide-react';
import { AnimatePresence } from 'framer-motion';
import { useOutletContext, useNavigate } from 'react-router-dom';
import NoteCard from '../components/NoteCard';

const HomePage = () => {
    const { notes, deleteNote, togglePin, toggleArchive } = useOutletContext();
    const navigate = useNavigate();

    const [search, setSearch] = useState('');

    const filtered = useMemo(() => {
        return notes
            // Important: We ONLY want to show unarchived notes on the Home Page
            .filter(n => !n.isArchived)
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
        // Navigate directly to the editor page with this note's ID
        navigate(`/note/${n.id}`);
    };

    return (
        <div className="page-container">
            <header className="page-header">
                <h2>All Notes</h2>
                <div className="search">
                    <Search size={16} />
                    <input
                        placeholder="Search notes..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                    />
                </div>
                <button className="btn btn-blue" onClick={() => navigate('/create')}>
                    <Plus size={18} /> New Note
                </button>
            </header>

            <div className="page-content">
                {filtered.length > 0 ? (
                    <div className="grid">
                        <AnimatePresence mode="popLayout">
                            {filtered.map(n => (
                                <NoteCard
                                    key={n.id}
                                    note={n}
                                    onDelete={deleteNote}
                                    onEdit={onEdit}
                                    onPin={togglePin}
                                    onArchive={toggleArchive}
                                />
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
            </div>

        </div>
    );
};

export default HomePage;
