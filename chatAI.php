<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with AI</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .chat-container {
            background-color: #f7f7f7;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin: auto;
            overflow: hidden;
        }

        .chat-header {
            background-color: #075E54;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .chat-messages {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
        }

        .user-message {
            background-color: #DCF8C6;
            border-radius: 10px;
            padding: 8px 12px;
            margin-bottom: 5px;
            max-width: 70%;
            align-self: flex-start;
        }

        .system-message {
            font-style: italic;
            color: #999;
            text-align: center;
            margin: 10px;
        }

        .ai-message {
            background-color: #E1E1E1;
            border-radius: 10px;
            padding: 8px 12px;
            margin-bottom: 5px;
            max-width: 70%;
            align-self: flex-end;
        }

        .chat-input {
            display: flex;
            margin-top: 10px;
            padding: 10px;
            background-color: white;
            border-top: 1px solid #ccc;
        }

        .chat-input input {
            flex-grow: 1;
            border: none;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 14px;
        }

        .chat-input button {
            background-color: #075E54;
            color: white;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            outline: none;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="chat-container">
        <div class="chat-header">
            Chat with AI
        </div>
        <div class="chat-messages" id="chat-box"></div>
        <div class="chat-input">
            <input type="text" id="user-input" placeholder="Type your message...">
            <button id="send-btn"><i class="bi bi-arrow-right"></i></button>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chat-box');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');

        async function sendMessage(message) {
            const response = await fetch('https://api.openai.com/v1/chat/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer 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'
                },
                body: JSON.stringify({
                    model: 'gpt-3.5-turbo',
                    messages: [{
                        role: 'system',
                        content: 'You are a helpful assistant.'
                    }, {
                        role: 'user',
                        content: message
                    }]
                })
            });

            const data = await response.json();
            return data.choices[0].message.content.trim();
        }

        function addMessage(content, role) {
            const messageDiv = document.createElement('div');
            messageDiv.className = role === 'system' ? 'text-gray-500 italic' : 'bg-gray-200 p-2 rounded-md';

            const messageText = document.createTextNode(content);
            messageDiv.appendChild(messageText);

            chatBox.appendChild(messageDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        sendBtn.addEventListener('click', async () => {
            const userMessage = userInput.value;
            if (userMessage) {
                addMessage(userMessage, 'user');
                userInput.value = '';

                const aiMessage = await sendMessage(userMessage);
                addMessage(aiMessage, 'system');
            }
        });

        userInput.addEventListener('keyup', (event) => {
            if (event.key === 'Enter') {
                sendBtn.click();
            }
        });
    </script>
</body>

</html>