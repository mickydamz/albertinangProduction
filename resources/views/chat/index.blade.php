@extends('layouts.app')

@section('content')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <div class="chat-container">
                <h2>All Conversations</h2>

                <!-- Show warning message below -->
                <div class="alert alert-warning mb-3" role="alert">
                    <strong>Warning!</strong> Messaging and performing any transactions with distributors should only be done on this platform to avoid being banned. Please ensure you are using the Albertina secure communication channels only.
                </div>

                <!-- Check if there are no conversations -->
                @if($conversations->isEmpty())
                    <p>No chats yet.</p>
                @else
                    <!-- Conversations List -->
                    <div class="conversation-list">
                        @foreach($conversations as $supplierId => $messages)
                            @php
                                $supplier = $messages->first()->sender_id === Auth::id() ? $messages->first()->recipient : $messages->first()->sender;
                            @endphp
                            <a href="{{ route('chat.show', $supplierId) }}" class="conversation-item">
                                <div class="conversation-info">
                                    <strong>{{ $supplier->name }}</strong>
                                    <p>{{ $messages->first()->content }}</p>
                                    <small>{{ $messages->first()->created_at->format('Y-m-d H:i') }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <style>
                /* Chat container styling */
                .chat-container {
                    padding: 30px;
                    background: #F1F2F6; /* Soft off-white with a cool tone */
                    border-radius: 16px;
                    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07); /* Light shadow for depth */
                    max-width: 700px;
                    margin: 40px auto;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
                    color: #333; /* Neutral text */
                }

                h2 {
                    font-size: 1.8rem;
                    color: #333;
                    margin-bottom: 20px;
                    font-weight: 600;
                    text-align: center;
                }

                /* Conversations List Styling */
                .conversation-list {
                    list-style: none;
                    padding: 0;
                    margin-top: 20px;
                }

                .conversation-item {
                    display: flex;
                    align-items: center;
                    padding: 18px;
                    text-decoration: none;
                    border: 1px solid #E4E4E4; /* Subtle border */
                    border-radius: 12px;
                    margin-bottom: 15px;
                    transition: background-color 0.3s ease, color 0.3s ease;
                    background: #fff;
                }

                /* Hover effect with a soft color shift */
                .conversation-item:hover {
                    background-color:rgb(225, 225, 230); /* Light blue hover effect */
                    color:rgb(229, 232, 238); /* Apple blue text */
                }

                .conversation-info {
                    margin-left: 15px;
                    flex: 1;
                }

                .conversation-info strong {
                    display: block;
                    font-size: 1.15rem;
                    color: #1A1A1A; /* Slightly darker text for the name */
                    font-weight: 500;
                    margin-bottom: 5px;
                }

                .conversation-info p {
                    margin: 0;
                    color: #666; /* Soft gray for message preview */
                    font-size: 0.95rem;
                    opacity: 0.75;
                }

                .conversation-info small {
                    color: #999; /* Light gray text for timestamps */
                    font-size: 0.8rem;
                }

                /* Custom Styling for Alert */
                .alert-warning {
                    background-color:rgb(110, 16, 31); /* Very light cream for alert */
                    color:rgb(250, 247, 242); /* Warm brown for text */
                    border: 1px solid rgb(156, 116, 55); /* Soft yellow border */
                border-radius: 10px;
                    padding: 20px;
                    font-size: 1.1rem;
                    margin-bottom: 30px;
                    box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1); /* Soft shadow */
                    animation: fadeInAlert 1s ease-in-out;
                }

                .alert-warning strong {
                    font-weight: 700;
                    color: #D48E00; /* A warm golden yellow for emphasis */
                }

                /* Smooth fade-in effect for alert */
                @keyframes fadeInAlert {
                    0% {
                        opacity: 0;
                        transform: translateY(-10px);
                    }
                    100% {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

            </style>
        </div>
    </div>
</div>

@endsection
