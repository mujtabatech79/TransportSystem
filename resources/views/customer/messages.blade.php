<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruckLink - Messages</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
            --info: #17a2b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            overflow: hidden;
            height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, #1a2530 100%);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        
        .sidebar .logo {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar .logo h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .sidebar .logo span {
            color: var(--secondary);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            margin: 8px 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: block;
            text-decoration: none;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(52, 152, 219, 0.15);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Topbar */
        .topbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        /* Chat Container */
        .chat-container {
            flex: 1;
            display: flex;
            overflow: hidden;
            background: #f5f7fb;
        }
        
        /* Conversations Sidebar */
        .conversations-sidebar {
            width: 350px;
            background: white;
            border-right: 1px solid rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .conversations-header {
            padding: 20px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            background: white;
        }
        
        .conversations-header h4 {
            margin: 0;
            font-weight: 600;
        }
        
        .conversations-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
        }
        
        .conversation-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: relative;
        }
        
        .conversation-item:hover {
            background: #f8f9fa;
        }
        
        .conversation-item.active {
            background: linear-gradient(135deg, rgba(52,152,219,0.1), rgba(52,152,219,0.05));
            border-left: 3px solid var(--secondary);
        }
        
        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .conversation-info {
            flex: 1;
            min-width: 0;
        }
        
        .conversation-name {
            font-weight: 600;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .conversation-name span:first-child {
            font-size: 1rem;
        }
        
        .conversation-time {
            font-size: 0.7rem;
            color: #6c757d;
        }
        
        .conversation-last-message {
            font-size: 0.85rem;
            color: #6c757d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .unread-badge {
            background: var(--secondary);
            color: white;
            border-radius: 20px;
            padding: 2px 8px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 10px;
        }
        
        /* Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f5f7fb;
        }
        
        .chat-header {
            padding: 20px 25px;
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
        }
        
        .chat-header-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            margin-right: 15px;
        }
        
        .chat-header-info h5 {
            margin: 0;
            font-weight: 600;
        }
        
        .chat-header-info p {
            margin: 0;
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Messages Area */
        .messages-area {
            flex: 1;
            overflow-y: auto;
            padding: 20px 25px;
            display: flex;
            flex-direction: column;
        }
        
        .message {
            display: flex;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .message.received {
            justify-content: flex-start;
        }
        
        .message.sent {
            justify-content: flex-end;
        }
        
        .message-bubble {
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 20px;
            position: relative;
        }
        
        .message.received .message-bubble {
            background: white;
            color: #333;
            border-bottom-left-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .message.sent .message-bubble {
            background: linear-gradient(135deg, var(--secondary), #2980b9);
            color: white;
            border-bottom-right-radius: 5px;
        }
        
        .message-text {
            font-size: 0.95rem;
            line-height: 1.4;
            word-wrap: break-word;
        }
        
        .message-time {
            font-size: 0.7rem;
            margin-top: 5px;
            opacity: 0.7;
        }
        
        .message.sent .message-time {
            text-align: right;
        }
        
        /* Message Input Area */
        .message-input-area {
            padding: 20px 25px;
            background: white;
            border-top: 1px solid rgba(0,0,0,0.08);
        }
        
        .input-group-custom {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .message-input {
            flex: 1;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            resize: none;
            font-family: inherit;
        }
        
        .message-input:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        }
        
        .send-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), #2980b9);
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(52,152,219,0.4);
        }
        
        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Empty State */
        .empty-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #6c757d;
        }
        
        .empty-chat i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-chat h5 {
            margin-bottom: 10px;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }
            
            .sidebar .logo h3 span,
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar .nav-link {
                text-align: center;
                padding: 15px;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.3rem;
            }
            
            .main-content {
                margin-left: 80px;
            }
            
            .conversations-sidebar {
                width: 280px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h3><i class="fas fa-truck-moving"></i> <span>Truck</span>Link</h3>
            <small class="text-muted">Customer Dashboard</small>
        </div>
        <div class="sidebar-content">
            <nav class="nav flex-column mt-4">
                <a class="nav-link " href="{{route('customer.login')}}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
               <a class="nav-link" href="{{route('all.vehicle')}}">
                    <i class="fas fa-search"></i> <span>All vehicle</span>
                </a>
                <a class="nav-link" href="{{route('find.vehicle')}}">
                    <i class="fas fa-search"></i> <span>Find Vehicles</span>
                </a>
                <a class="nav-link" href="{{route('mybookings')}}">
                    <i class="fas fa-clipboard-list"></i> <span>My Bookings</span>
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-money-bill-wave"></i> <span>Payments</span>
                </a>
                <a class="nav-link" href="{{route('mybookingss')}}">
                    <i class="fas fa-map-marked-alt"></i> <span>Track Shipment</span>
                </a>
                <a class="nav-link notification-badge active" href="{{route('messages.conversations')}}">
                    <i class="fas fa-comments"></i> <span>Messages</span>
                    <span class="badge-count" id="unreadMessageBadge" style="display: none;">0</span>
                </a>
               
                <a class="nav-link" href="{{ route('customer.complaints') }}">
                    <i class="fas fa-exclamation-circle"></i> <span>Complaints</span>
                </a>
               
               <a class="nav-link" href="{{route('customer.analytics')}}"><i class="fas fa-chart-line"></i> <span>Analytics</span></a>
                <a class="nav-link" href="{{route('user.logout')}}">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-semibold"><i class="fas fa-comments me-2 text-primary"></i>Messages</h5>
            </div>
            <div class="user-info d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                        <span class="fw-semibold">{{ session('name', 'Customer') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{route('user.logout')}}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="chat-container">
            <!-- Conversations Sidebar -->
            <div class="conversations-sidebar">
                <div class="conversations-header">
                    <h4><i class="fas fa-comment-dots me-2"></i> Conversations</h4>
                </div>
                <div class="conversations-list" id="conversationsList">
                    @if(count($conversations) > 0)
                        @foreach($conversations as $index => $conv)
                            <div class="conversation-item" data-user-id="{{ $conv['user']->id }}" data-user-name="{{ $conv['user']->name }}" onclick="selectConversation({{ $conv['user']->id }}, '{{ addslashes($conv['user']->name) }}')">
                                <div class="conversation-avatar">
                                    {{ strtoupper(substr($conv['user']->name, 0, 1)) }}
                                </div>
                                <div class="conversation-info">
                                    <div class="conversation-name">
                                        <span>{{ $conv['user']->name }}</span>
                                        <span class="conversation-time">
                                            {{ $conv['last_message'] ? $conv['last_message']->created_at->diffForHumans() : '' }}
                                        </span>
                                    </div>
                                    <div class="conversation-last-message">
                                        {{ $conv['last_message'] ? Str::limit($conv['last_message']->message, 50) : 'No messages yet' }}
                                    </div>
                                </div>
                                @if($conv['unread_count'] > 0)
                                    <div class="unread-badge">{{ $conv['unread_count'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No conversations yet.<br>Start by contacting a vehicle owner.</p>
                            <a href="{{ route('find.vehicle') }}" class="btn btn-primary btn-sm">Find Vehicles</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Chat Area -->
            <div class="chat-area" id="chatArea">
                <div class="empty-chat">
                    <i class="fas fa-comment-dots"></i>
                    <h5>Select a conversation</h5>
                    <p class="text-muted">Choose a conversation from the list to start messaging</p>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentUserId = null;
        let pollingInterval = null;
        
        // Select conversation
        function selectConversation(userId, userName) {
            currentUserId = userId;
            
            // Update active state in sidebar
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('data-user-id') == userId) {
                    item.classList.add('active');
                }
            });
            
            // Load messages
            loadMessages(userId, userName);
            
            // Start polling for new messages
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
            pollingInterval = setInterval(() => {
                if (currentUserId) {
                    refreshMessages(currentUserId);
                }
            }, 5000);
        }
        
        // Load messages
        function loadMessages(userId, userName) {
            $.ajax({
                url: '{{ route("messages.get", "") }}/' + userId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        displayChatArea(response, userName);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading messages:', xhr);
                }
            });
        }
        
        // Refresh messages (for polling)
        function refreshMessages(userId) {
            $.ajax({
                url: '{{ route("messages.get", "") }}/' + userId,
                method: 'GET',
                success: function(response) {
                    if (response.success && currentUserId == userId) {
                        updateMessages(response.messages);
                    }
                }
            });
        }
        
        // Display chat area
        function displayChatArea(data, userName) {
            const chatArea = document.getElementById('chatArea');
            
            let messagesHtml = '';
            data.messages.forEach(msg => {
                messagesHtml += `
                    <div class="message ${msg.is_mine ? 'sent' : 'received'}">
                        <div class="message-bubble">
                            <div class="message-text">${escapeHtml(msg.message)}</div>
                            <div class="message-time">${msg.time} • ${msg.time_ago}</div>
                        </div>
                    </div>
                `;
            });
            
            chatArea.innerHTML = `
                <div class="chat-header">
                    <div class="chat-header-avatar">
                        ${userName.charAt(0).toUpperCase()}
                    </div>
                    <div class="chat-header-info">
                        <h5>${escapeHtml(userName)}</h5>
                        <p><i class="fas fa-circle" style="font-size: 8px; color: #27ae60;"></i> Online</p>
                    </div>
                </div>
                <div class="messages-area" id="messagesArea">
                    ${messagesHtml || '<div class="text-center text-muted p-4">No messages yet. Start the conversation!</div>'}
                </div>
                <div class="message-input-area">
                    <div class="input-group-custom">
                        <textarea class="message-input" id="messageInput" rows="1" placeholder="Type your message..." onkeypress="handleKeyPress(event)"></textarea>
                        <button class="send-btn" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            `;
            
            // Auto-resize textarea
            const textarea = document.getElementById('messageInput');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 100) + 'px';
                });
            }
            
            // Scroll to bottom
            scrollToBottom();
        }
        
        // Update messages (for polling)
        function updateMessages(messages) {
            const messagesArea = document.getElementById('messagesArea');
            if (!messagesArea) return;
            
            const currentMessages = messagesArea.querySelectorAll('.message').length;
            
            if (messages.length > currentMessages) {
                // New messages added
                let newMessagesHtml = '';
                for (let i = currentMessages; i < messages.length; i++) {
                    const msg = messages[i];
                    newMessagesHtml += `
                        <div class="message ${msg.is_mine ? 'sent' : 'received'}">
                            <div class="message-bubble">
                                <div class="message-text">${escapeHtml(msg.message)}</div>
                                <div class="message-time">${msg.time} • ${msg.time_ago}</div>
                            </div>
                        </div>
                    `;
                }
                messagesArea.insertAdjacentHTML('beforeend', newMessagesHtml);
                scrollToBottom();
                
                // Update unread badge in sidebar
                updateUnreadBadge();
            }
        }
        
        // Send message
        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (!message || !currentUserId) return;
            
            // Disable send button temporarily
            const sendBtn = document.querySelector('.send-btn');
            sendBtn.disabled = true;
            
            $.ajax({
                url: '{{ route("messages.send") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    receiver_id: currentUserId,
                    message: message
                },
                success: function(response) {
                    if (response.success) {
                        // Clear input
                        messageInput.value = '';
                        messageInput.style.height = 'auto';
                        
                        // Add message to chat
                        const messagesArea = document.getElementById('messagesArea');
                        const messageHtml = `
                            <div class="message sent">
                                <div class="message-bubble">
                                    <div class="message-text">${escapeHtml(response.message.message)}</div>
                                    <div class="message-time">${response.message.time} • Just now</div>
                                </div>
                            </div>
                        `;
                        messagesArea.insertAdjacentHTML('beforeend', messageHtml);
                        scrollToBottom();
                    }
                    sendBtn.disabled = false;
                },
                error: function(xhr) {
                    console.error('Error sending message:', xhr);
                    sendBtn.disabled = false;
                    toastr.error('Failed to send message. Please try again.');
                }
            });
        }
        
        // Handle enter key press
        function handleKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }
        
        // Scroll to bottom of messages
        function scrollToBottom() {
            const messagesArea = document.getElementById('messagesArea');
            if (messagesArea) {
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }
        }
        
        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Update unread badge in conversation list
        function updateUnreadBadge() {
            // This will be called when new messages are received
            // You can implement this to update the unread count in the sidebar
            location.reload(); // Simple reload to update unread counts
        }
        
        // Clean up polling on page unload
        window.addEventListener('beforeunload', function() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
        });
    </script>
</body>
</html>