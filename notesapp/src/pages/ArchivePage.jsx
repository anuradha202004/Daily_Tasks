import React, { useState, useMemo } from 'react';
import { Search, Archive } from 'lucide-react';
import { AnimatePresence } from 'framer-motion';
import { useOutletContext, useNavigate } from 'react-router-dom';
import NoteCard from '../components/NoteCard';

const ArchivePage = () => {
    // We get the same functions from the layout wrapper
    const { notes, deleteNote, togglePin, toggleArchive } = useOutletContext();
    const navigate = useNavigate();

    const [search, setSearch] = useState('');

    const filtered = useMemo(() => {
        return notes
            // Important: We ONLY want to show ARCHIVED notes on this page
            .filter(n => n.isArchived)
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
                <h2>Archived Notes</h2>
                <div className="search">
                    <Search size={16} />
                    <input
                        placeholder="Search archived..."
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
                        <Archive size={50} style={{ opacity: 0.3, marginBottom: '10px' }} />
                        <h2>Archive is empty</h2>
                        <p>When you archive notes, they will appear here.</p>
                    </div>
                )}
            </div>

        </div>
    );
};

export default ArchivePage;
