<?php
session_start();
$user = null;
if (isset($_SESSION['user_id'])) {
    include_once __DIR__ . '/../app/models/Usuarios.php';
    $res = Usuarios::buscarUsuarioPeloId($_SESSION['user_id']);
    if (!$res['erro']) $user = $res['dados'];
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - OngDogs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="imagens/logo_ong.png" alt="Logo Instituto Eu Sou Bicho" class="logo">
        <nav>
            <a href="home.php">Home</a>
            <a href="home.php#como-ajudar">Como Ajudar</a>
            <a href="home.php#animais">Animais</a>
            <a href="home.php#missao">Missão</a>
            <a href="home.php#contato">Contato</a>
            <a href="home.php#formularios">Formulários</a>
            <a href="home.php#depoimentos">Depoimentos</a>
            <?php if ($user): ?>
                <a href="admin.php">Admin</a>
                <a id="logoutLink" href="login.php">Sair (<?php echo htmlspecialchars($user['nome'] ?? 'Conta'); ?>)</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="auth-container" id="appBox">
        <div id="loginView">
            <h2>Entrar</h2>
            <form id="loginForm">
                <label for="email">Email</label>
                <div class="input-pill">
                    <input id="email" name="email" type="email" required placeholder="email@exemplo.com" aria-label="Email">
                    <span class="input-icon" aria-hidden="true"> 
                        <!-- envelope SVG -->
                        <svg width="18" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 6.5L12 13L21 6.5" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 6V18C21 18.5523 20.5523 19 20 19H4C3.44772 19 3 18.5523 3 18V6" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>

                <label for="senha">Senha</label>
                <div class="input-pill">
                    <input id="senha" name="senha" type="password" required placeholder="Sua senha" aria-label="Senha">
                    <span class="input-icon" aria-hidden="true">
                        <!-- lock SVG -->
                        <svg width="16" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="11" width="18" height="10" rx="2" stroke="#fff" stroke-width="1.6"/><path d="M7 11V8C7 5.23858 9.23858 3 12 3C14.7614 3 17 5.23858 17 8V11" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </div>
                <div class="pw-bar-wrapper" aria-hidden="true"><div class="pw-bar" id="pwBar"></div></div>
                <div style="display:flex;gap:8px;margin-top:8px;">
                    <button type="button" id="togglePass" class="btn-small" aria-label="Mostrar senha">Mostrar</button>
                    <div style="flex:1"></div>
                </div>

                <button type="submit" class="btn-primary">Entrar</button>
                <div id="msg" class="error" role="alert" aria-live="polite"></div> 
            </form>
        </div>

        <div id="profileView" class="profile-card" style="display:none">
            <h2>Meu Perfil</h2>
            <div id="content">Carregando...</div>
            <p>
                <button id="logoutBtn">Sair</button>
            </p>
            <hr>
            
            <!-- Abas de navegação -->
            <div class="tabs-container">
                <button class="tab-btn active" data-tab="info">Informações</button>
                <button class="tab-btn" data-tab="doacoes">Minhas Doações</button>
                <button class="tab-btn" data-tab="apadrinhamentos">Apadrinhamentos</button>
                <button class="tab-btn" data-tab="eventos">Eventos</button>
            </div>

            <!-- Aba: Informações -->
            <div id="tab-info" class="tab-content active">
                <div id="navLinks" class="nav-links">
                    <a class="btn-link" href="adocoes.php">Adoções</a>
                    <a class="btn-link" href="animais.php">Animais</a>
                    <a class="btn-link" href="apadrinhamento.php">Apadrinhamento</a>
                    <a class="btn-link" href="doacoes.php">Doações</a>
                    <a class="btn-link" href="eventos.php">Eventos</a>
                </div>
            </div>

            <!-- Aba: Doações -->
            <div id="tab-doacoes" class="tab-content">
                <h3>Minhas Doações</h3>
                <div id="listaDoacoes" class="lista-container">Carregando...</div>
            </div>

            <!-- Aba: Apadrinhamentos -->
            <div id="tab-apadrinhamentos" class="tab-content">
                <h3>Meus Apadrinhamentos</h3>
                <div id="listaApadrinhamentos" class="lista-container">Carregando...</div>
            </div>

            <!-- Aba: Eventos -->
            <div id="tab-eventos" class="tab-content">
                <h3>Eventos</h3>
                <div id="listaEventos" class="lista-container">Carregando...</div>
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
const togglePass = document.getElementById('togglePass');
const senhaInput = document.getElementById('senha');

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

// Mostrar / ocultar senha
if (togglePass) {
    togglePass.addEventListener('click', () => {
        if (senhaInput.type === 'password') {
            senhaInput.type = 'text';
            togglePass.textContent = '🙈';
            togglePass.setAttribute('aria-pressed','true');
        } else {
            senhaInput.type = 'password';
            togglePass.textContent = '👁️';
            togglePass.setAttribute('aria-pressed','false');
        }
        senhaInput.focus();
    });
}

// atualiza barra de força da senha
const pwBar = document.getElementById('pwBar');
if (senhaInput && pwBar) {
    senhaInput.addEventListener('input', () => {
        const len = senhaInput.value.length;
        const pct = Math.min(100, Math.round((len / 16) * 100));
        pwBar.style.width = pct + '%';
        if (len === 0) {
            pwBar.style.background = 'linear-gradient(90deg, rgba(255,255,255,0.04), rgba(255,255,255,0.04))';
            pwBar.style.boxShadow = 'none';
        } else if (len < 6) {
            pwBar.style.background = 'linear-gradient(90deg, #ff4757, #ff7a7a)';
            pwBar.style.boxShadow = '0 6px 18px rgba(255,74,87,0.28)';
        } else if (len < 10) {
            pwBar.style.background = 'linear-gradient(90deg, #ffb86b, #ffd08a)';
            pwBar.style.boxShadow = '0 6px 18px rgba(255,184,107,0.18)';
        } else {
            pwBar.style.background = 'linear-gradient(90deg, #2ee08a, #2ecf7a)';
            pwBar.style.boxShadow = '0 6px 18px rgba(46,224,138,0.18)';
        }
    });
}

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
        
        // Carregar listas de dados
        carregarDoacoes();
        carregarApadrinhamentos();
        carregarEventos();
        
        // Configurar abas
        configurarAbas();
        
    }catch(e){
        content.textContent = 'Erro ao carregar perfil';
    }
}

function configurarAbas() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove ativo de todos
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(t => t.classList.remove('active'));
            
            // Ativa o selecionado
            btn.classList.add('active');
            const tabName = btn.getAttribute('data-tab');
            document.getElementById('tab-' + tabName).classList.add('active');
        });
    });
}

async function carregarDoacoes() {
    try {
        const { res, j } = await fetchJson('../app/models/Doacoes.php?action=listar');
        const div = document.getElementById('listaDoacoes');
        
        if (j.erro || !j.dados || j.dados.length === 0) {
            div.innerHTML = '<div class="lista-empty">Nenhuma doação registrada</div>';
            return;
        }
        
        let html = '<table class="lista-table"><thead><tr><th>ID</th><th>Valor (R$)</th><th>Forma de Pagamento</th><th>Data</th><th>Ação</th></tr></thead><tbody>';
        
        j.dados.forEach(d => {
            html += `<tr>
                <td>${d.id_doacao}</td>
                <td>R$ ${parseFloat(d.valor).toFixed(2)}</td>
                <td>${d.forma_paga || '-'}</td>
                <td>${new Date(d.data_doacao).toLocaleDateString('pt-BR')}</td>
                <td><button class="btn-delete" onclick="excluirDoacao(${d.id_doacao})">Excluir</button></td>
            </tr>`;
        });
        
        html += '</tbody></table>';
        div.innerHTML = html;
    } catch (e) {
        document.getElementById('listaDoacoes').innerHTML = '<div class="lista-empty">Erro ao carregar doações</div>';
    }
}

async function carregarApadrinhamentos() {
    try {
        const { res, j } = await fetchJson('../app/models/Apadrinhamento.php?action=listar');
        const div = document.getElementById('listaApadrinhamentos');
        
        if (j.erro || !j.dados || j.dados.length === 0) {
            div.innerHTML = '<div class="lista-empty">Nenhum apadrinhamento registrado</div>';
            return;
        }
        
        let html = '<table class="lista-table"><thead><tr><th>ID</th><th>Animal ID</th><th>Valor Mensal (R$)</th><th>Ação</th></tr></thead><tbody>';
        
        j.dados.forEach(a => {
            html += `<tr>
                <td>${a.id_apadrinhamento}</td>
                <td>${a.id_animal}</td>
                <td>R$ ${parseFloat(a.valor_mensal).toFixed(2)}</td>
                <td><button class="btn-delete" onclick="excluirApadrinhamento(${a.id_apadrinhamento})">Excluir</button></td>
            </tr>`;
        });
        
        html += '</tbody></table>';
        div.innerHTML = html;
    } catch (e) {
        document.getElementById('listaApadrinhamentos').innerHTML = '<div class="lista-empty">Erro ao carregar apadrinhamentos</div>';
    }
}

async function carregarEventos() {
    try {
        const { res, j } = await fetchJson('../app/models/Eventos.php?action=listar');
        const div = document.getElementById('listaEventos');
        
        if (j.erro || !j.dados || j.dados.length === 0) {
            div.innerHTML = '<div class="lista-empty">Nenhum evento registrado</div>';
            return;
        }
        
        let html = '<table class="lista-table"><thead><tr><th>ID</th><th>Título</th><th>Data do Evento</th><th>Ação</th></tr></thead><tbody>';
        
        j.dados.forEach(e => {
            html += `<tr>
                <td>${e.id_evento}</td>
                <td>${e.titulo}</td>
                <td>${new Date(e.data_evento).toLocaleDateString('pt-BR')}</td>
                <td><button class="btn-delete" onclick="excluirEvento(${e.id_evento})">Excluir</button></td>
            </tr>`;
        });
        
        html += '</tbody></table>';
        div.innerHTML = html;
    } catch (e) {
        document.getElementById('listaEventos').innerHTML = '<div class="lista-empty">Erro ao carregar eventos</div>';
    }
}

async function excluirDoacao(id) {
    if (!confirm('Tem certeza que deseja excluir esta doação?')) return;
    
    try {
        const { res, j } = await fetchJson(`../app/models/Doacoes.php?action=deletar&id=${id}`, { method: 'POST' });
        if (!j.erro) {
            alert('Doação excluída com sucesso!');
            carregarDoacoes();
        } else {
            alert('Erro: ' + j.mensagem);
        }
    } catch (e) {
        alert('Erro ao excluir doação');
    }
}

async function excluirApadrinhamento(id) {
    if (!confirm('Tem certeza que deseja excluir este apadrinhamento?')) return;
    
    try {
        const { res, j } = await fetchJson(`../app/models/Apadrinhamento.php?action=deletar&id=${id}`, { method: 'POST' });
        if (!j.erro) {
            alert('Apadrinhamento excluído com sucesso!');
            carregarApadrinhamentos();
        } else {
            alert('Erro: ' + j.mensagem);
        }
    } catch (e) {
        alert('Erro ao excluir apadrinhamento');
    }
}

async function excluirEvento(id) {
    if (!confirm('Tem certeza que deseja excluir este evento?')) return;
    
    try {
        const { res, j } = await fetchJson(`../app/models/Eventos.php?action=deletar&id=${id}`, { method: 'POST' });
        if (!j.erro) {
            alert('Evento excluído com sucesso!');
            carregarEventos();
        } else {
            alert('Erro: ' + j.mensagem);
        }
    } catch (e) {
        alert('Erro ao excluir evento');
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