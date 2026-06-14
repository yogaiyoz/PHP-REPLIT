<?php
session_start();

/*
=========================================
 GLOBAL CHAT PROFESSIONAL SINGLE FILE
=========================================
*/

$tokenFile = __DIR__ . "/chat_token.dat";

if (!file_exists($tokenFile)) {
    $token = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
    file_put_contents($tokenFile, $token);
} else {
    $token = trim(file_get_contents($tokenFile));
}

$chatFile = __DIR__ . "/chats{$token}.txt";

if (!file_exists($chatFile)) {
    file_put_contents($chatFile, "");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json');

    $action = $_POST['action'] ?? '';

    if ($action === 'send') {

        $name = trim($_POST['name'] ?? 'Guest');
        $message = trim($_POST['message'] ?? '');

        if ($name === '') {
            $name = 'Guest';
        }

        if ($message !== '') {

            $record = [
                'time' => date('Y-m-d H:i:s'),
                'name' => htmlspecialchars($name, ENT_QUOTES),
                'message' => htmlspecialchars($message, ENT_QUOTES)
            ];

            file_put_contents(
                $chatFile,
                json_encode($record) . PHP_EOL,
                FILE_APPEND | LOCK_EX
            );
        }

        echo json_encode(['status' => 'ok']);
        exit;
    }

    if ($action === 'load') {

        $messages = [];

        $lines = file($chatFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $json = json_decode($line, true);

            if ($json) {
                $messages[] = $json;
            }
        }

        echo json_encode([
            'token' => $token,
            'messages' => $messages
        ]);

        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>Global Chat</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Segoe UI,Tahoma,sans-serif;
}

body{
background:#0f172a;
height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:20px;
}

.chat-container{
width:100%;
max-width:900px;
height:90vh;
background:#1e293b;
border-radius:20px;
overflow:hidden;
box-shadow:0 0 40px rgba(0,0,0,.5);
display:flex;
flex-direction:column;
}

.header{
background:linear-gradient(135deg,#2563eb,#06b6d4);
padding:18px;
color:white;
}

.header h1{
font-size:22px;
}

.token{
font-size:13px;
opacity:.9;
margin-top:5px;
}

.chat-box{
flex:1;
overflow-y:auto;
padding:20px;
background:#0f172a;
}

.message{
background:#1e293b;
border-left:4px solid #3b82f6;
padding:12px;
margin-bottom:12px;
border-radius:10px;
animation:fade .3s;
}

.user{
font-weight:bold;
color:#60a5fa;
}

.time{
font-size:11px;
color:#94a3b8;
margin-top:4px;
}

.text{
margin-top:8px;
color:white;
word-break:break-word;
}

.form{
padding:15px;
background:#1e293b;
display:flex;
gap:10px;
flex-wrap:wrap;
}

input{
padding:12px;
border:none;
border-radius:10px;
background:#334155;
color:white;
}

#name{
width:180px;
}

#message{
flex:1;
}

button{
padding:12px 20px;
border:none;
border-radius:10px;
cursor:pointer;
background:#2563eb;
color:white;
font-weight:bold;
transition:.2s;
}

button:hover{
transform:scale(1.05);
}

::-webkit-scrollbar{
width:8px;
}

::-webkit-scrollbar-thumb{
background:#3b82f6;
border-radius:10px;
}

@keyframes fade{
from{
opacity:0;
transform:translateY(10px);
}
to{
opacity:1;
transform:translateY(0);
}
}

</style>
</head>
<body>

<div class="chat-container">

<div class="header">
<h1>🌐 Global Chat</h1>
<div class="token" id="tokenDisplay">
Loading...
</div>
</div>

<div class="chat-box" id="chatBox"></div>

<div class="form">
<input type="text" id="name" placeholder="Nama">
<input type="text" id="message" placeholder="Tulis pesan..." autocomplete="off">
<button onclick="sendMessage()">Kirim</button>
</div>

</div>

<script>

const chatBox = document.getElementById("chatBox");

function escapeHtml(text){
    let div = document.createElement("div");
    div.innerText = text;
    return div.innerHTML;
}

function loadMessages(){

    let form = new FormData();
    form.append("action","load");

    fetch("",{
        method:"POST",
        body:form
    })
    .then(r=>r.json())
    .then(data=>{

        document.getElementById(
            "tokenDisplay"
        ).innerText =
            "Room Token : " + data.token;

        let html = "";

        data.messages.forEach(msg=>{

            html += `
            <div class="message">
                <div class="user">${escapeHtml(msg.name)}</div>
                <div class="time">${escapeHtml(msg.time)}</div>
                <div class="text">${escapeHtml(msg.message)}</div>
            </div>
            `;
        });

        chatBox.innerHTML = html;

        chatBox.scrollTop =
            chatBox.scrollHeight;
    });
}

function sendMessage(){

    let name =
        document.getElementById("name").value;

    let message =
        document.getElementById("message").value;

    if(message.trim()===""){
        return;
    }

    let form = new FormData();

    form.append("action","send");
    form.append("name",name);
    form.append("message",message);

    fetch("",{
        method:"POST",
        body:form
    })
    .then(r=>r.json())
    .then(()=>{

        document.getElementById(
            "message"
        ).value="";

        loadMessages();
    });
}

document.getElementById("message")
.addEventListener("keypress",function(e){

    if(e.key==="Enter"){
        sendMessage();
    }
});

loadMessages();

setInterval(loadMessages,2000);

</script>

</body>
</html>