import React from 'react';
import { NavLink, Outlet } from 'react-router-dom';
import { FileText, Archive, Pin } from 'lucide-react';

import { useOutletContext } from 'react-router-dom';

// Use the `context` prop passed from App.jsx
const MainLayout = ({ context }) => {

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