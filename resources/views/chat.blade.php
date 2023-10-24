<!DOCTYPE html>
<?php
/**
 * @var User $user
 * @var User[] $users
 * @var ChatsModel[] $chats
 */

use App\Models\User;
use App\Models\ChatsModel;

?>
<html lang="ar">
<head>
    <title>Chat App - Laravel</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;1,100;1,200;1,300;1,400&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', serif;
        }

        .form-control:focus {
            box-shadow: none;
        }
    </style>
</head>

<body>

<div class="container p-3 rounded">
    <h1 class="text-center fw-bold text-dark">Chat App - Laravel & React PHP</h1>
    <div class="row mt-4">
        <div class="col-lg-4 bg-light border p-3">
            <span class="fw-bold h4">Users List</span>
            <hr>

            @foreach($users as $userModel)
                <a data-user="{{$userModel->id}}" class="h5 d-block text-decoration-none bg-white p-3 mb-2 border rounded d-flex align-items-center"
                   href="/chat/{{$userModel->id}}">{{$userModel->name}}</a>
            @endforeach
        </div>
        <div class="col-lg-8">
            <div class="bg-white border">
                <div class="bg-light p-3 h4 d-flex justify-content-between align-items-center" style="gap: 10px;">
                    <span class="fw-bold"><?= $user->name ?></span>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <div class="bg-success rounded-circle" style="width: 10px; height: 10px;"></div>
                        <span style="font-size: 14px">Online</span>
                    </div>
                </div>

                <div id="templates" style="display: none">
                    <div data-sender="message" class="d-flex justify-content-start mb-3">
                        <div class="px-4 py-2 text-light mx-2 bg-success d-inline-block rounded message"
                             style="font-size: 20px; width: 40%">
                        </div>
                    </div>

                    <div data-reciever="message" class="d-flex justify-content-end mb-3">
                        <div class="px-4 py-2 text-light mx-2 bg-primary d-inline-block rounded message"
                             style="font-size: 20px; width: 40%">
                        </div>
                    </div>
                </div>

                <div id="messages-container" class="p-4" style="height: 60vh; overflow-y: auto">
                    @foreach($chats as $chat)
                        @if ($chat->sender_id == auth()->id())
                            <div data-sender="message" class="d-flex justify-content-start mb-3">
                                <div class="px-4 py-2 text-light mx-2 bg-success d-inline-block rounded message"
                                     style="font-size: 20px; width: 40%">
                                    {{$chat->message}}
                                </div>
                            </div>
                        @else
                            <div data-reciever="message" class="d-flex justify-content-end mb-3">
                                <div class="px-4 py-2 text-light mx-2 bg-primary d-inline-block rounded message"
                                     style="font-size: 20px; width: 40%">
                                    {{$chat->message}}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="d-flex">
                    <input id="chat-msg" type="text" class="form-control rounded-0 border-0 bg-light p-3"
                           placeholder="Enter Your Message">
                    <button class="btn btn-primary" id="send-btn">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const conn = new WebSocket('ws://localhost:8090?receiver_id={{auth()->id()}}&sender_id={{$user->id}}');
    conn.onopen = function (e) {
        console.log("Connection established!");
    };

    conn.onmessage = function (e) {
        const receiverContainer = $("#templates [data-reciever]").clone();
        const message = JSON.parse(e.data);
        receiverContainer.find('.message').html(message.message);
        $("#messages-container").append(receiverContainer.show());
    };

    $('#send-btn').click(function () {
        const messageInput = $('#chat-msg');
        const message = messageInput.val();
        if (!message) return;
        const senderContainer = $("#templates [data-sender]").clone();
        senderContainer.find('.message').text(message);
        $("#messages-container").append(senderContainer.show());
        conn.send(JSON.stringify({"message": message, "senderId": {{auth()->id()}}, "receiverId": {{$user->id}}}));
        messageInput.val('');
    });

    $(document).on('keypress', function (e) {
        if (e.which === 13) {
            $('#send-btn').click();
        }
    });
</script>
</body>
</html>
