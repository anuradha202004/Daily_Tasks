// Header functionality - Search and User Menu

/**
 * Perform search when user submits search form
 */
function performSearch() {
    const searchQuery = document.getElementById('searchInput').value.trim();
    if (searchQuery) {
        window.location.href = 'search-results.php?q=' + encodeURIComponent(searchQuery);
    }
}

/**
 * Handle search on Enter key press
 */
function handleSearchKeypress(event) {
    if (event.key === 'Enter') {
        performSearch();
    }
}

/**
 * Toggle user dropdown menu
 */
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

/**
 * Close user dropdown when clicking outside
 */
document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    if (userMenu && !userMenu.contains(event.target)) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown) {
            dropdown.classList.remove('show');
        }
    }
});
