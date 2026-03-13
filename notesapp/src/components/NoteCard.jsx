import React from 'react';
import { Pin } from 'lucide-react';
import { motion } from 'framer-motion';
import { NOTE_COLORS } from '../utils/colors';

const NoteCard = ({ note, onDelete, onEdit, onPin, onArchive }) => {
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

            {/* Wrap the content and make it clickable to Open the note */}
            <div
                onClick={() => onEdit(note)}
                style={{ cursor: 'pointer', flex: 1, display: 'flex', flexDirection: 'column' }}
                title="Click to open note"
            >
                <h3 className="card-title">{note.title || 'Untitled'}</h3>
                <p className="card-text">{note.content}</p>
            </div>

            <div className="card-footer" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <span style={{ fontSize: '0.75rem', color: 'var(--gray)' }}>
                    {new Date(note.createdAt).toLocaleDateString()}
                </span>
                <div className="card-btns">
                    {/* Only show Archive/Unarchive if the function is available (it might not be passed down in all views) */}
                    {onArchive && (
                        <button className="action-text" style={{ color: 'var(--gray)' }} onClick={() => onArchive(note.id)}>
                            {note.isArchived ? 'Unarchive' : 'Archive'}
                        </button>
                    )}
                    <button className="action-text" onClick={() => onEdit(note)}>Edit</button>
                    <button className="action-text" style={{ color: '#ef4444' }} onClick={() => onDelete(note.id)}>Delete</button>
                </div>
            </div>
        </motion.div>
    );
};

export default NoteCard;
