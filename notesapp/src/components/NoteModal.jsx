import React, { useState, useEffect } from 'react';
import { X, Check } from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { NOTE_COLORS } from '../utils/colors';

const NoteModal = ({ isOpen, onClose, onSave, data }) => {
    const [title, setTitle] = useState('');
    const [content, setContent] = useState('');
    const [color, setColor] = useState('default');

    useEffect(() => {
        if (data) {
            setTitle(data.title);
            setContent(data.content);
            setColor(data.color);
        } else {
            setTitle('');
            setContent('');
            setColor('default');
        }
    }, [data, isOpen]);

    const onHostSave = (e) => {
        e.preventDefault();
        if (!title.trim() && !content.trim()) return;
        onSave({ ...data, title, content, color });
        onClose();
    };

    return (
        <AnimatePresence>
            {isOpen && (
                <div className="overlay">
                    <div className="absolute inset-0" onClick={onClose} />

                    <motion.div initial={{ scale: 0.9 }} animate={{ scale: 1 }} className="modal">
                        <X className="close" size={20} onClick={onClose} />

                        <form className="form" onSubmit={onHostSave}>
                            <input
                                className="inp inp-title"
                                placeholder="Title"
                                value={title}
                                onChange={(e) => setTitle(e.target.value)}
                                autoFocus
                            />
                            <textarea
                                className="inp inp-text"
                                placeholder="Take a note..."
                                value={content}
                                onChange={(e) => setContent(e.target.value)}
                            />

                            <div className="colors">
                                {NOTE_COLORS.map((c) => (
                                    <button
                                        key={c.id}
                                        type="button"
                                        className={`dot ${color === c.id ? 'active' : ''}`}
                                        style={{ backgroundColor: c.hex }}
                                        onClick={() => setColor(c.id)}
                                    >
                                        {color === c.id && <Check size={14} color="white" />}
                                    </button>
                                ))}
                            </div>

                            <div className="flex" style={{ marginTop: '20px' }}>
                                <span />
                                <button type="submit" className="btn btn-blue">Save Note</button>
                            </div>
                        </form>
                    </motion.div>
                </div>
            )}
        </AnimatePresence>
    );
};

export default NoteModal;
