import React, { useState, useMemo } from 'react';
import { Search, Pin } from 'lucide-react';
import { AnimatePresence } from 'framer-motion';
import { useOutletContext, useNavigate } from 'react-router-dom';
import NoteCard from '../components/NoteCard';

const PinnedPage = () => {
    // We get the same functions from the layout wrapper
    const { notes, deleteNote, togglePin, toggleArchive } = useOutletContext();
    const navigate = useNavigate();

    const [search, setSearch] = useState('');

    const filtered = useMemo(() => {
        return notes
            // Important: We ONLY want to show PINNED notes that are NOT archived
            .filter(n => n.isPinned && !n.isArchived)
            .filter(n =>
                n.title.toLowerCase().includes(search.toLowerCase()) ||
                n.content.toLowerCase().includes(search.toLowerCase())
            )
            .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
    }, [notes, search]);

    const onEdit = (n) => {
        navigate(`/note/${n.id}`);
    };

    return (
        <div className="page-container">
            <header className="page-header">
                <h2>Pinned Notes</h2>
                <div className="search">
                    <Search size={16} />
                    <input
                        placeholder="Search pinned..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                    />
                </div>
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
                        <Pin size={50} style={{ opacity: 0.3, marginBottom: '10px' }} />
                        <h2>No pinned notes</h2>
                        <p>Pin important notes for easy access.</p>
                    </div>
                )}
            </div>

        </div>
    );
};

export default PinnedPage;
