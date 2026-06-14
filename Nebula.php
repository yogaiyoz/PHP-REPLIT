<?php

$file = "data.json";

if (!file_exists($file)) {
    file_put_contents($file, "[]");
}

if (isset($_GET['api'])) {

    header("Content-Type: application/json");

    if ($_GET['api'] === "get") {
        echo file_get_contents($file);
        exit;
    }

    if ($_GET['api'] === "save") {

        $input = json_decode(file_get_contents("php://input"), true);

        $list = json_decode(file_get_contents($file), true);

        $list[] = [
            "id" => uniqid(),
            "time" => date("Y-m-d H:i:s"),
            "message" => htmlspecialchars($input["message"] ?? "")
        ];

        file_put_contents(
            $file,
            json_encode($list, JSON_PRETTY_PRINT)
        );

        echo json_encode([
            "status" => true
        ]);

        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dynamic Single Server</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Segoe UI,sans-serif;
}

body{
background:#0f172a;
color:white;
padding:20px;
}

.container{
max-width:1000px;
margin:auto;
}

.card{
background:#1e293b;
padding:20px;
border-radius:16px;
margin-bottom:20px;
box-shadow:0 5px 20px rgba(0,0,0,.3);
}

h1{
margin-bottom:10px;
}

input{
width:100%;
padding:14px;
border:none;
border-radius:12px;
margin-bottom:10px;
outline:none;
}

button{
padding:14px 25px;
border:none;
border-radius:12px;
background:#3b82f6;
color:white;
cursor:pointer;
font-weight:bold;
}

button:hover{
opacity:.9;
}

#logs{
max-height:500px;
overflow-y:auto;
}

.log{
background:#334155;
padding:12px;
border-radius:10px;
margin-bottom:10px;
}

.time{
font-size:12px;
opacity:.7;
margin-top:5px;
}

.status{
color:#22c55e;
font-weight:bold;
}

</style>
</head>
<body>

<div class="container">

<div class="card">
<h1>🚀 Single PHP Server</h1>
<p>Status :
<span class="status">ONLINE</span>
</p>
</div>

<div class="card">
<input
id="message"
placeholder="Tulis sesuatu..."
>

<button onclick="sendMessage()">
Kirim
</button>
</div>

<div class="card">
<h2>📜 Live Data</h2>
<br>
<div id="logs">
Loading...
</div>
</div>

</div>

<script>

async function loadData(){

let req = await fetch('?api=get');
let data = await req.json();

let html = '';

data.reverse().forEach(item=>{

html += `
<div class="log">
<div>${item.message}</div>
<div class="time">${item.time}</div>
</div>
`;

});

document.getElementById('logs').innerHTML =
html || 'Belum ada data';

}

async function sendMessage(){

let input =
document.getElementById('message');

let message =
input.value.trim();

if(!message) return;

await fetch('?api=save',{

method:'POST',

headers:{
'Content-Type':'application/json'
},

body:JSON.stringify({
message:message
})

});

input.value='';

loadData();

}

loadData();

setInterval(loadData,2000);

</script>

</body>
</html>