import { useState, useEffect } from 'react';
import CryptoJS from 'crypto-js';

const SECRET_KEY = '12345';
export const useNotes = () => {
    const [notes, setNotes] = useState(() => {
        const saved = localStorage.getItem('notes-app-data');
        if (saved) {
            try {
                const bytes = CryptoJS.AES.decrypt(saved, SECRET_KEY);

                const decryptedData = JSON.parse(bytes.toString(CryptoJS.enc.Utf8));
                return decryptedData;

            } catch (error) {
                console.error("Failed to decrypt data", error);
                return [];
            }
        }
        return [];
    });

    useEffect(() => {

        const encryptedData = CryptoJS.AES.encrypt(JSON.stringify(notes), SECRET_KEY).toString();
        localStorage.setItem('notes-app-data', encryptedData);
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

    const toggleArchive = (id) => {
        setNotes(notes.map(n => n.id === id ? { ...n, isArchived: !n.isArchived, isPinned: false } : n));
    }

    return {
        notes,
        addNote,
        updateNote,
        deleteNote,
        togglePin,
        toggleArchive
    };
};
