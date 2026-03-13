import React, { useState, useEffect } from 'react';
import { ArrowLeft, Check } from 'lucide-react';
import { useOutletContext, useParams, useNavigate } from 'react-router-dom';
import { NOTE_COLORS } from '../utils/colors';

const NoteEditorPage = () => {
    // We get all the functions and data down from the Outlet context (App.js)
    const { notes, addNote, updateNote } = useOutletContext();

    // useParams gets the URL parameter (e.g. /note/123 -> id is '123')
    const { id } = useParams();
    const navigate = useNavigate();

    const [title, setTitle] = useState('');
    const [content, setContent] = useState('');
    const [color, setColor] = useState('default');
    const [existingNote, setExistingNote] = useState(null);

    useEffect(() => {
        // If there's an ID in the URL, we are EDITING an existing note
        if (id) {
            const foundNote = notes.find(n => n.id === id);
            if (foundNote) {
                setExistingNote(foundNote);
                setTitle(foundNote.title);
                setContent(foundNote.content);
                setColor(foundNote.color || 'default');
            } else {
                // Not found? go back to home!
                navigate('/');
            }
        }
    }, [id, notes, navigate]);

    const handleSave = (e) => {
        e.preventDefault();

        // Don't save if it's completely empty
        if (!title.trim() && !content.trim()) {
            navigate(-1); // Go back one page in history
            return;
        }

        if (existingNote) {
            // Update the existing note
            updateNote({ ...existingNote, title, content, color });
        } else {
            // Create a brand new note
            addNote({ title, content, color });
        }

        // Go back to the previous page!
        navigate(-1);
    };

    return (
        <div className="page-container">
            <header className="page-header" style={{ marginBottom: '1rem' }}>
                <button className="btn icon-btn" onClick={() => navigate(-1)}>
                    <ArrowLeft size={24} /> Back
                </button>
            </header>

            <div className="page-content" style={{ maxWidth: '800px', margin: '0 auto' }}>
                <form className="form editor-form" onSubmit={handleSave}>
                    <input
                        className="inp editor-title"
                        placeholder="Note Title"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        autoFocus
                    />

                    <textarea
                        className="inp editor-textarea"
                        placeholder="What's on your mind today?"
                        value={content}
                        onChange={(e) => setContent(e.target.value)}
                    />

                    <div className="editor-bottom-bar">
                        <div className="colors" style={{ display: 'flex', gap: '8px' }}>
                            {NOTE_COLORS.map((c) => (
                                <button
                                    key={c.id}
                                    type="button"
                                    className={`color-dot ${color === c.id ? 'active' : ''}`}
                                    style={{ backgroundColor: c.hex }}
                                    onClick={() => setColor(c.id)}
                                >
                                    {color === c.id && <Check size={16} color="white" />}
                                </button>
                            ))}
                        </div>

                        <button type="submit" className="btn btn-blue" style={{ fontSize: '1rem', padding: '12px 24px' }}>
                            {existingNote ? 'Update Note' : 'Create Note'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default NoteEditorPage;
