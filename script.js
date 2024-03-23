// Fetch user mentions from the backend and update the UI
fetch('fetch_mentions.php')
    .then(response => response.json())
    .then(mentions => {
        // Assuming 'mentions' is an array of user mentions
        const mentionsContainer = document.getElementById('mentions-container');
        mentions.forEach(mention => {
            const mentionElement = document.createElement('div');
            mentionElement.innerHTML = `
                <p><strong>${mention.username}</strong> mentioned you in a caption:</p>
                <p>${mention.caption}</p>
                <p>Media URL: ${mention.media_url}</p>
            `;
            mentionsContainer.appendChild(mentionElement);
        });
    })
    .catch(error => console.error(error));
