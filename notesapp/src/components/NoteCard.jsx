import React from 'react';
import { Pin } from 'lucide-react';
import { motion } from 'framer-motion';
import { NOTE_COLORS } from '../utils/colors';

const NoteCard = ({ note, onDelete, onEdit, onPin }) => {
    const color = NOTE_COLORS.find(c => c.id === note.color) || NOTE_COLORS[0];

    return (
        <motion.div
            layout
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            className="card"
            style={{ borderTop: `4px solid ${color.hex}` }}
        >
            <button className={`pin ${note.isPinned ? 'active' : ''}`} onClick={() => onPin(note.id)}>
                <Pin size={16} fill={note.isPinned ? 'currentColor' : 'none'} />
            </button>

            <h3 className="card-title">{note.title || 'Untitled'}</h3>
            <p className="card-text">{note.content}</p>

            <div className="card-footer">
                <div className="card-btns">
                    <button className="action-text" onClick={() => onEdit(note)}>Edit</button>
                    <button className="action-text" style={{ color: '#ef4444' }} onClick={() => onDelete(note.id)}>Delete</button>
                </div>
            </div>
        </motion.div>
    );
};

export default NoteCard;
