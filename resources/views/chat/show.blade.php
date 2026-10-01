@extends('layouts.app')

@section('content')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <div class="chat-container">
                <h2>Chat with {{ $recipient->name }}</h2>
                <div class="chat-box" id="chat-box">
                    @foreach($messages as $message)
                        <div class="message {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                            <strong>{{ $message->sender_id === auth()->id() ? 'You' : $message->sender->name }}</strong>
                            <p>{{ $message->content }}</p>
                            <small>{{ $message->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                    @endforeach
                </div>
                <form action="{{ route('chat.send') }}" method="POST">
                    @csrf
                    <input type="hidden" name="recipient_id" value="{{ $supplierId }}">
                    <input type="text" name="message" placeholder="Type your message..." required>
                    <button type="submit">Send</button>
                </form>
            </div>

            <style>
                .chat-container {
                    padding: 20px;
                    background: #F5F5F5; /* Warm Beige background */
                    border-radius: 12px;
                    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                    max-width: 600px;
                    margin: 40px auto;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                }

                .chat-box {
                    height: 400px;
                    overflow-y: auto;
                    border: none;
                    margin-bottom: 20px;
                    padding: 20px;
                    background: #fff;
                    border-radius: 12px;
                    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
                }

                .message {
                    margin-bottom: 15px;
                    padding: 12px 18px;
                    border-radius: 18px;
                    position: relative;
                    max-width: 80%;
                    word-wrap: break-word;
                    font-size: 16px;
                }

                .sent {
                    background: #1A2A6C; /* Deep Blue for sent messages */
                    color: white;
                    text-align: right;
                    margin-left: auto;
                    border-top-left-radius: 0;
                    border-bottom-left-radius: 18px;
                }

                .received {
                    background: #D1D1D1; /* Neutral Gray for received messages */
                    color: #2E2E2E; /* Dark Charcoal text for received messages */
                    text-align: left;
                    margin-right: auto;
                    border-top-right-radius: 0;
                    border-bottom-right-radius: 18px;
                }

                .sent strong, .received strong {
                    display: block;
                    font-size: 0.85rem;
                    margin-bottom: 5px;
                    color: #D9AF4B; /* Gold for names */
                    font-weight: 600;
                }

                .sent small, .received small {
                    display: block;
                    font-size: 0.75rem;
                    opacity: 0.7;
                    color: #888;
                }

                form {
                    display: flex;
                    margin-top: 20px;
                    padding: 8px 0;
                }

                input[type="text"] {
                    flex: 1;
                    padding: 14px;
                    border: 1px solid #D1D1D1; /* Neutral gray border */
                    border-radius: 25px;
                    margin-right: 15px;
                    font-size: 16px;
                    outline: none;
                    transition: all 0.3s;
                }

                input[type="text"]:focus {
                    border-color: #1A2A6C; /* Deep Blue border on focus */
                    box-shadow: 0 0 5px rgba(26, 42, 108, 0.3);
                }

                button {
                    padding: 14px 20px;
                    border: none;
                    border-radius: 25px;
                    background: #D9AF4B; /* Gold background */
                    color: white;
                    cursor: pointer;
                    transition: background 0.3s;
                    font-size: 16px;
                }

                button:hover {
                    background: #C79D3A; /* Slightly darker gold on hover */
                }

                button:active {
                    background: #B88B29; /* Darker gold on active */
                }
            </style>

            <script>
                // Scroll to the bottom of the chat box on page load and when new message is added
                window.onload = function() {
                    var chatBox = document.getElementById('chat-box');
                    chatBox.scrollTop = chatBox.scrollHeight;
                };
            </script>
        </div>
    </div>
</div>

@endsection
