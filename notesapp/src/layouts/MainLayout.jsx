import React from 'react';
import { NavLink, Outlet } from 'react-router-dom';
import { FileText, Archive, Pin, LogOut } from 'lucide-react';

import { useOutletContext } from 'react-router-dom';

// Use the `context` prop passed from App.jsx
const MainLayout = ({ context }) => {

    const userString = localStorage.getItem('currentUser');
    const user = userString ? JSON.parse(userString) : null;

    const handleLogout = () => {
        localStorage.removeItem('currentUser');
        window.location.href = '/login';
    };

    return (
        <div className='layout'>
            <aside className='sidebar'>
                <div className='logo' style={{ marginBottom: '2rem', padding: '0 1rem' }}>
                    <div className='icon-bg'><FileText size={20} /></div>
                    <span>Notes App</span>
                </div>
                <nav className='nav-menu'>
                    <NavLink to="/" className="nav-item" end>
                        <FileText size={18} />AllNotes
                    </NavLink>
                    <NavLink to="/pinned" className="nav-item">
                        <Pin size={18} />Pinned
                    </NavLink>
                    <NavLink to="/archived" className="nav-item">
                        <Archive size={18} />Archive
                    </NavLink>
                </nav>

                {/* --- NEW: User Profile & Logout section at the bottom --- */}
                {user && (
                    <div style={{ marginTop: 'auto', paddingTop: '1rem', borderTop: '1px solid var(--border)' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '15px' }}>
                            <img src={user.picture} alt="profile" style={{ width: '36px', height: '36px', borderRadius: '50%' }} />
                            <div style={{ overflow: 'hidden' }}>
                                <p style={{ fontSize: '0.85rem', fontWeight: 'bold', whiteSpace: 'nowrap', textOverflow: 'ellipsis', color: 'var(--text)' }}>
                                    {user.name}
                                </p>
                            </div>
                        </div>
                        <button
                            onClick={handleLogout}
                            style={{
                                width: '100%', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '8px',
                                padding: '10px', background: 'var(--bg)', color: '#ef4444',
                                border: 'none', borderRadius: '8px', cursor: 'pointer', fontWeight: '600'
                            }}
                        >
                            <LogOut size={16} /> Sign Out
                        </button>
                    </div>
                )}
            </aside>
            <main className='main-content'>
                {/* We pass the notesData DOWN to all the sub-pages via Outlet */}
                {/* We pass the context PROPS DOWN to all the sub-pages via Outlet */}
                <Outlet context={context} />
            </main>
        </div>

    );
};

export default MainLayout;