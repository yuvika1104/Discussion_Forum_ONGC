document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('threadSearch');
    if (!searchInput) {
        console.error('Search input with ID "threadSearch" not found.');
        return;
    }

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const threads = document.querySelectorAll('.thread-link');

        if (threads.length === 0) {
            console.warn('No elements with class "thread-link" found.');
        }

        let visibleThreadCount = 0;
        threads.forEach(thread => {
            const title = thread.getAttribute('data-title')?.toLowerCase() || '';
            const username = thread.getAttribute('data-username')?.toLowerCase() || '';
            
            if (title.includes(searchTerm) || username.includes(searchTerm)) {
                thread.style.display = 'block';
                visibleThreadCount++;
            } else {
                thread.style.display = 'none';
            }
        });

        const noThreadsMessage = document.querySelector('.no-threads');
        if (noThreadsMessage) {
            noThreadsMessage.style.display = visibleThreadCount === 0 ? 'block' : 'none';
        } else if (visibleThreadCount === 0) {
            console.warn('No threads message element with class "no-threads" found.');
        }
    });
});