document.addEventListener('DOMContentLoaded', function () {
    // Your server endpoint to receive webhook notifications
    const serverEndpoint = 'http://localhost:3000';

    // Function to handle incoming notifications
    function handleNotification(data) {
        // Extract data from the received notification
        const urlMedia = data.url_media;
        const caption = data.caption;
        const username = data.username;

        // Display the notification
        const notificationElement = document.createElement('div');
        notificationElement.innerHTML = `<p><strong>${username}</strong> mentioned you in a caption:</p>
                                         <p>${caption}</p>
                                         <p>Media URL: ${urlMedia}</p>`;
        document.getElementById('notifications').appendChild(notificationElement);
    }

    // Simulate receiving a notification (replace with your actual logic)
    function simulateNotification() {
        const simulatedData = {
            url_media: 'https://example.com/image.jpg',
            caption: 'Check out this amazing photo!',
            username: 'example_user'
        };

        handleNotification(simulatedData);
    }

    // Call the simulateNotification function when the page loads
    simulateNotification();
});

const appId = '944463346552351';
const appSecret = '5054166e9895b4e93359d385965e126e';
const appAccessToken = `${appId}|${appSecret}`;
const instagramAccountId = '7346615261587451645'; // Retrieve this from the /me/accounts endpoint

const subscribeParams = {
    object: 'user',
    fields: 'mention',
    callback_url: serverEndpoint,
    verify_token: 'iNfrG6DMX=srwedXO8Sqg2FUefiw=YBc0m77oPKyMzL4ijHWZW3Q/kKL6vpLjmdO5p/EDAKc-bWE5TW1NI8V07agXtfWZizin7aUR5YEdxv9CMjWpKwWl9jOb!Dh-iOABQlE0lSlMfgawl9hiHxTl3wNB=isT/0sanoJQEg1mg3T2UJPS0YFETM53nZH=A8aq4!Tg9w?7GPAZBDcxPnmM/7oHPxt?dqR4hjJgLeA9wi01RzMOM/LBHoEDhrs2-b2',
    access_token: userLongLivedToken, // Long-lived user access token with manage_pages permission
};

fetch(`https://graph.facebook.com/v12.0/${instagramAccountId}/subscriptions`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(subscribeParams),
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error(error));
