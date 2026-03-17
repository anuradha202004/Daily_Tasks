import React from 'react';
import { Routes, Route } from 'react-router-dom';
import { useNotes } from './hooks/useNotes';

// Import our new layout and pages
import MainLayout from './layouts/MainLayout';
import HomePage from './pages/HomePage';
import PinnedPage from './pages/PinnedPage';
import ArchivePage from './pages/ArchivePage';
import NoteEditorPage from './pages/NoteEditorPage';
import LoginPage from './pages/LoginPage';
import { Navigate } from 'react-router-dom';

function App() {
  // Safe check if someone is logged in!
  let user = null;
  try {
    const currentUserString = localStorage.getItem('currentUser');
    if (currentUserString) {
      user = JSON.parse(currentUserString);
    }
  } catch (e) {
    console.error("Failed to parse user", e);
  }

  // We manage the notes state at the very top level
  // so that all pages inside our app have access to the same notes!
  const notesData = useNotes();

  return (
    <div className="app">
      <Routes>
        {/* PUBLIC ROUTE: The Login Page */}
        <Route path="/login" element={user ? <Navigate to="/" /> : <LoginPage />} />

        {/* PROTECTED ROUTES: Only show MainLayout if 'user' exists, else redirect to Login */}
        <Route path="/" element={user ? <MainLayout context={notesData} /> : <Navigate to="/login" />}>

          {/* Outlet sub-pages */}
          <Route element={<HomePage />} index />
          <Route path="pinned" element={<PinnedPage />} />
          <Route path="archived" element={<ArchivePage />} />

          {/* New specific pages to handle Creating and Editing */}
          <Route path="create" element={<NoteEditorPage />} />
          <Route path="note/:id" element={<NoteEditorPage />} />

        </Route>
      </Routes>
    </div>
  );
}

export default App;
