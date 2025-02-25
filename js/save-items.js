// Get the base URL dynamically
const getBaseUrl = () => {
    // Get the current path
    const path = window.location.pathname;
    // Find the position of /Sidestacker/ in the path
    const sidestackerPos = path.indexOf('/Sidestacker/');
    // If found, return everything up to and including /Sidestacker/
    if (sidestackerPos !== -1) {
        return path.substring(0, sidestackerPos + '/Sidestacker/'.length);
    }
    // Fallback to root if not found
    return '/';
};

async function toggleSave(itemType, itemId) {
    try {
        const baseUrl = getBaseUrl();
        const response = await fetch(`${baseUrl}api/toggle_save.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                item_type: itemType,
                item_id: itemId
            })
        });

        const data = await response.json();
        
        if (response.ok) {
            // Update the button appearance
            const button = document.querySelector(`button[data-item-type="${itemType}"][data-item-id="${itemId}"] i`);
            if (data.status === 'saved') {
                button.classList.add('text-blue-600');
            } else {
                button.classList.remove('text-blue-600');
            }
        } else {
            if (response.status === 401) {
                window.location.href = `${baseUrl}login.php`;
            } else {
                console.error('Error:', data.error);
            }
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
