<?php
// Página de login simples
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - OngDogs</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; padding: 2rem; }
        .box { max-width:420px; margin:0 auto; border:1px solid #ddd; padding:1.2rem; border-radius:6px }
        label{display:block;margin-top:.6rem}
        input[type="email"], input[type="password"]{width:100%;padding:.6rem;margin-top:.3rem}
        button{margin-top:1rem;padding:.6rem 1rem}
        .error{color:#a00;margin-top:.6rem}
    </style>
</head>
<body>
    <div class="box" id="appBox">
        <div id="loginView">
            <h2>Entrar</h2>
            <form id="loginForm">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required>

                <label for="senha">Senha</label>
                <input id="senha" name="senha" type="password" required>

                <button type="submit">Entrar</button>
                <div id="msg" class="error" role="alert"></div>
            </form>
        </div>

        <div id="profileView" style="display:none">
            <h2>Meu Perfil</h2>
            <div id="content">Carregando...</div>
            <p>
                <button id="logoutBtn">Sair</button>
            </p>
            <hr>
            <div id="navLinks" style="margin-top:1rem; display:flex; gap:8px; flex-wrap:wrap">
                <a class="btn-link" href="adocoes.php">Adoções</a>
                <a class="btn-link" href="animais.php">Animais</a>
                <a class="btn-link" href="apadrinhamento.php">Apadrinhamento</a>
                <a class="btn-link" href="doacoes.php">Doações</a>
                <a class="btn-link" href="eventos.php">Eventos</a>
            </div>
        </div>
    </div>

<script>
const form = document.getElementById('loginForm');
const msg = document.getElementById('msg');
const loginView = document.getElementById('loginView');
const profileView = document.getElementById('profileView');
const content = document.getElementById('content');
const logoutBtn = document.getElementById('logoutBtn');

async function fetchJson(url, opts = {}){
    opts.credentials = opts.credentials || 'same-origin';
    const res = await fetch(url, opts);
    const j = await res.json();
    return { res, j };
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    msg.textContent = '';
    const data = new FormData(form);

    try {
        const { res, j } = await fetchJson('../app/models/auth.php', { method: 'POST', body: data });
        if (!j.erro) {
            // mostra perfil na mesma página
            showProfile();
        } else {
            msg.textContent = j.mensagem || 'Erro ao autenticar';
        }
    } catch (err) {
        msg.textContent = 'Erro de conexão';
    }
});

async function showProfile(){
    loginView.style.display = 'none';
    profileView.style.display = '';
    content.textContent = 'Carregando...';
    try{
        const { res, j } = await fetchJson('../app/models/auth.php');
        if (j.erro) {
            content.innerHTML = '<div style="color:#a00">' + j.mensagem + '</div>';
            if (res.status === 401) {
                // voltar para login
                setTimeout(()=> {
                    profileView.style.display = 'none';
                    loginView.style.display = '';
                }, 800);
            }
            return;
        }
        const u = j.dados;
        content.innerHTML = `
            <div class="field"><span class="label">Nome:</span> ${u.nome}</div>
            <div class="field"><span class="label">Email:</span> ${u.email}</div>
            <div class="field"><span class="label">Telefone:</span> ${u.telefone}</div>
            <div class="field"><span class="label">CPF:</span> ${u.cpf}</div>
            <div class="field"><span class="label">Tipo:</span> ${u.tipo_usuario}</div>
        `;
        // mostra links de navegação após popular o perfil
        document.getElementById('navLinks').style.display = '';
    }catch(e){
        content.textContent = 'Erro ao carregar perfil';
    }
}

logoutBtn.addEventListener('click', async () =>{
    try{
        const body = new FormData();
        body.append('action','logout');
        const { res, j } = await fetchJson('../app/models/auth.php', { method: 'POST', body });
        // Volta para tela de login
        profileView.style.display = 'none';
        loginView.style.display = '';
        form.reset();
        msg.textContent = j.mensagem || '';
    }catch(e){
        content.textContent = 'Erro ao sair';
    }
});

// Ao abrir a página, checar sessão existente
(async function init(){
    try{
        const { res, j } = await fetchJson('../app/models/auth.php');
        if (!j.erro) {
            showProfile();
        }
    }catch(e){
        // ignora
    }
})();
</script>
</body>
</html>