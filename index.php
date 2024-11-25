
<!DOCTYPE html>
<html>
<head>
    <title>Chat Bot</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/90664d89df.js" crossorigin="anonymous"></script>
</head>
<body>

<?php
include 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);


?>
<div class="contaier">
    <div class="header">
        <h1>Chat Bot</h1>
    </div>
<div class="chat-messages">
<div class="chat-input-container">
    <input type="text" id="message-input" placeholder="Type your message...">
    <button id="send-button" disabled>Send</button>
</div>
</div>
</div>

<script>


async function sendMessage() {
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();
    const sendButton = document.getElementById('send-button');
    // Validate empty message
    if (!message) return;
    // Disable input and button while processing
    messageInput.disabled = true;
    sendButton.disabled = true;
    try {
        // Add user message to chat
        addMessageToChat('user', message);
        // Clear input and show loading
        messageInput.value = '';
        showLoadingIndicator();

        const response = await Promise.race([
            fetch('chatbot_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message }),
            }),
            new Promise((_, reject) =>
                setTimeout(() => reject(new Error('Request timed out')), 30000)
            )
        ]);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        if (data.error) {
            throw new Error(data.error);
        } else {
            addMessageToChat('bot', data.response);
        }
    } catch (error) {
        console.error('Chat error:', error);
        addMessageToChat('bot', 'Sorry, there was an error processing your request. Please try again later.');
    } finally {
        // Clean up: remove loading indicator and re-enable inputs
        removeLoadingIndicator();
        messageInput.disabled = false;
        sendButton.disabled = false;
        messageInput.focus();
    }
}

function addMessageToChat(sender, message) {
    const chatMessages = document.querySelector('.chat-messages');
    const messageDiv = document.createElement('div');
    const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    // Create message container
    messageDiv.className = `message ${sender}-message`;
    // Create message content
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    contentDiv.textContent = message;
    // Create timestamp
    const timeDiv = document.createElement('div');
    timeDiv.className = 'message-timestamp';
    timeDiv.textContent = timestamp;
    // Assemble message
    messageDiv.appendChild(contentDiv);
    messageDiv.appendChild(timeDiv);
    // Add to chat and scroll
    chatMessages.appendChild(messageDiv);
    scrollToBottom();
    // Add animation class
    setTimeout(() => messageDiv.classList.add('show'), 100);
}

function scrollToBottom() {
    const chatMessages = document.querySelector('.chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function showLoadingIndicator() {
    const chatMessages = document.querySelector('.chat-messages');
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'typing-indicator';
    loadingDiv.id = 'loading-message';

    // Create three dot elements
    for (let i = 0; i < 3; i++) {
        const dot = document.createElement('div');
        dot.className = 'dot';
        loadingDiv.appendChild(dot);
    }

    chatMessages.appendChild(loadingDiv);
    scrollToBottom();
}

function removeLoadingIndicator() {
    const loadingMessage = document.getElementById('loading-message');
    if (loadingMessage) {
        loadingMessage.remove();
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');

    // Send message on enter key
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Send message on button click
    sendButton.addEventListener('click', sendMessage);

    // Input handling
    messageInput.addEventListener('input', () => {
        sendButton.disabled = !messageInput.value.trim();
    });
});


</script>
