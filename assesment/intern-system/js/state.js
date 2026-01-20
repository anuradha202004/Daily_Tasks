const AppState = {
    interns: [],
    tasks: [],
    logs: [],
    currentView: 'dashboard',
    filters: { status: '', skill: '' },
    nextInternId: 1,
    nextTaskId: 1,
    reassignmentData: null  // Store reassignment context
};

// LocalStorage Management
const StorageManager = {
    STORAGE_KEY: 'internHubData',
    
    // Save all app data to localStorage
    saveToStorage() {
        try {
            const dataToSave = {
                interns: AppState.interns,
                tasks: AppState.tasks,
                logs: AppState.logs,
                nextInternId: AppState.nextInternId,
                nextTaskId: AppState.nextTaskId,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem(this.STORAGE_KEY, JSON.stringify(dataToSave));
            console.log('✓ Data saved to localStorage');
            return true;
        } catch (error) {
            console.error('Error saving to localStorage:', error);
            return false;
        }
    },
    
    // Load all app data from localStorage
    loadFromStorage() {
        try {
            const savedData = localStorage.getItem(this.STORAGE_KEY);
            
            if (!savedData) {
                console.log('No saved data found in localStorage');
                return false;
            }
            
            const parsed = JSON.parse(savedData);
            AppState.interns = parsed.interns || [];
            AppState.tasks = parsed.tasks || [];
            AppState.logs = parsed.logs || [];
            AppState.nextInternId = parsed.nextInternId || 1;
            AppState.nextTaskId = parsed.nextTaskId || 1;
            
            console.log('✓ Data loaded from localStorage');
            console.log(`Loaded: ${AppState.interns.length} interns, ${AppState.tasks.length} tasks`);
            return true;
        } catch (error) {
            console.error('Error loading from localStorage:', error);
            return false;
        }
    },
    
    // Clear all stored data
    clearStorage() {
        try {
            localStorage.removeItem(this.STORAGE_KEY);
            AppState.interns = [];
            AppState.tasks = [];
            AppState.logs = [];
            AppState.nextInternId = 1;
            AppState.nextTaskId = 1;
            console.log('✓ localStorage cleared');
            return true;
        } catch (error) {
            console.error('Error clearing localStorage:', error);
            return false;
        }
    },
    
    // Export data as JSON (for backup)
    exportData() {
        try {
            const data = {
                interns: AppState.interns,
                tasks: AppState.tasks,
                logs: AppState.logs,
                exportedAt: new Date().toISOString()
            };
            const json = JSON.stringify(data, null, 2);
            const blob = new Blob([json], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `internhub-backup-${new Date().toISOString().split('T')[0]}.json`;
            link.click();
            return true;
        } catch (error) {
            console.error('Error exporting data:', error);
            return false;
        }
    }
};