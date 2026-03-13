import { useState, useEffect } from 'react';

export const useNotes = () => {
    const [notes, setNotes] = useState(() => {
        const saved = localStorage.getItem('notes-app-data');
        return saved ? JSON.parse(saved) : [];
    });

    useEffect(() => {
        localStorage.setItem('notes-app-data', JSON.stringify(notes));
    }, [notes]);

    const addNote = (note) => {
        const newNote = {
            ...note,
            id: Date.now().toString(),
            createdAt: new Date().toISOString(),
            isPinned: false,
            color: note.color || 'default',
        };
        setNotes([newNote, ...notes]);
    };

    const updateNote = (updatedNote) => {
        setNotes(notes.map(n => n.id === updatedNote.id ? updatedNote : n));
    };

    const deleteNote = (id) => {
        setNotes(notes.filter(n => n.id !== id));
    };

    const togglePin = (id) => {
        setNotes(notes.map(n =>
            n.id === id ? { ...n, isPinned: !n.isPinned } : n
        ));
    };

    return {
        notes,
        addNote,
        updateNote,
        deleteNote,
        togglePin
    };
};
