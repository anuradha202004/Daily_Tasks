import React from 'react';
import { Routes, Route } from 'react-router-dom';
import { useNotes } from './hooks/useNotes';

// Import our new layout and pages
import MainLayout from './layouts/MainLayout';
import HomePage from './pages/HomePage';
import PinnedPage from './pages/PinnedPage';
import ArchivePage from './pages/ArchivePage';
import NoteEditorPage from './pages/NoteEditorPage';

function App() {
  // We manage the notes state at the very top level
  // so that all pages inside our app have access to the same notes!
  const notesData = useNotes();

  return (
    <div className="app">
      <Routes>
        {/* The wrapper layout for the main app */}
        <Route path="/" element={<MainLayout context={notesData} />}>

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
