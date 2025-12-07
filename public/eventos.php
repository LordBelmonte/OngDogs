<?php
// Página de Eventos - esqueleto para integração com API
$rootPath = dirname(__DIR__);
require_once $rootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eventos - OngDogs</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body{font-family:Arial,helvetica,sans-serif;padding:1.2rem}
        .container{max-width:900px;margin:0 auto}
        table{width:100%;border-collapse:collapse}
        th,td{padding:.5rem;border:1px solid #ddd}
        form > *{display:block;margin:6px 0}
    </style>
</head>
<body>
<div class="container">
    <h1>Eventos</h1>
    <p>Lista de eventos. Formulário abaixo envia dados para o service `Eventos`.</p>

    <div id="msg" style="color:#a00"></div>

    <h3>Cadastrar evento</h3>
    <form id="form">
        <input name="titulo" placeholder="Título" required>
        <input name="data" placeholder="Data">
        <input name="local" placeholder="Local">
        <button type="submit">Salvar</button>
    </form>

    <h3>Registros</h3>
    <table id="table">
        <thead><tr><th>ID</th><th>Título</th><th>Data</th><th>Local</th></tr></thead>
        <tbody></tbody>
    </table>
</div>

<script>
const service = 'Eventos';
const tableBody = document.querySelector('#table tbody');
const msg = document.getElementById('msg');

async function load(){
    tableBody.innerHTML = '<tr><td colspan="4">Carregando...</td></tr>';
    try{
        const res = await fetch('router.php/api/' + service);
        const j = await res.json();
        if (j.erro){
            tableBody.innerHTML = '<tr><td colspan="4">' + j.mensagem + '</td></tr>';
            return;
        }
        const rows = j.dados || [];
        if (rows.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4">Nenhum registro</td></tr>';
            return;
        }
        tableBody.innerHTML = rows.map(r=>`<tr><td>${r.id ?? '-'}</td><td>${r.titulo ?? '-'}</td><td>${r.data ?? '-'}</td><td>${r.local ?? '-'}</td></tr>`).join('');
    }catch(e){
        tableBody.innerHTML = '<tr><td colspan="4">Erro ao carregar</td></tr>';
    }
}

document.getElementById('form').addEventListener('submit', async (ev)=>{
    ev.preventDefault();
    msg.textContent = '';
    const form = ev.target;
    const data = Object.fromEntries(new FormData(form));
    try{
        const res = await fetch('router.php/api/' + service, {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(data)
        });
        const j = await res.json();
        if (j.erro) msg.textContent = j.mensagem;
        else { form.reset(); load(); }
    }catch(e){ msg.textContent = 'Erro de conexão'; }
});

load();
</script>
</body>
</html>
