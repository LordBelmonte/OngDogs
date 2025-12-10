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
    <script src="script.js"></script>
</head>
<body>
    <header>
        <img src="imagens/logo_ong.png" alt="Logo Instituto Eu Sou Bicho" class="logo">
        <nav>
            <a href="home.php">Home</a>
            <a href="home.php#como-ajudar">Como Ajudar</a>
            <a href="home.php#animais">Animais</a>
            <a href="home.php#missao">Missão</a>
            <a href="home.php#formularios">Formulários</a>
            <a href="home.php#contato">Contato</a>            
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
                <button class="tab-btn" data-tab="doacoes">Minhas Doações</button>
                <button class="tab-btn" data-tab="apadrinhamentos">Apadrinhamentos</button>
                <button class="tab-btn" data-tab="adocoes">Adoções</button>
                <button class="tab-btn" data-tab="eventos">Eventos</button>
            </div>

            <!-- Aba: Doações -->
            <div id="tab-doacoes" class="tab-content">
                <h3>Minhas Doações</h3>
                <div style="margin-bottom:12px;">
                    <button onclick="document.getElementById('form-doacao').classList.toggle('hidden')">+ Nova Doação</button>
                </div>
                <form id="form-doacao" class="form-card hidden">
                    <div class="form-group"><label for="valor">Valor da Doação (R$):</label><input type="text" id="valor" name="valor" required placeholder="Ex: 50.00"></div>
                    <div class="form-group"><label for="forma_paga">Forma de Pagamento:</label>
                        <select id="forma_paga" name="forma_paga" required>
                            <option value="">Selecione</option>
                            <option value="PIX">PIX</option>
                            <option value="Cartão">Cartão de Crédito</option>
                            <option value="Boleto">Boleto</option>
                        </select>
                    </div>
                    <button type="submit" id="btn-doar">Doar Agora</button>
                    <div class="form-feedback" id="feedback-doacao"></div>
                </form>
                <div id="listaDoacoes" class="lista-container">Carregando...</div>
            </div>

            <!-- Aba: Apadrinhamentos -->
            <div id="tab-apadrinhamentos" class="tab-content">
                <h3>Meus Apadrinhamentos</h3>
                <div style="margin-bottom:12px;">
                    <button onclick="document.getElementById('form-apadrinhamento').classList.toggle('hidden')">+ Novo Apadrinhamento</button>
                </div>
                <form id="form-apadrinhamento" class="form-card hidden">
                    <div class="form-group"><label for="id_animal_padrinho">Animal que deseja apadrinhar:</label><input type="number" id="id_animal_padrinho" name="id_animal" required placeholder="Insira o ID do Animal (Ex: 2)"></div>
                    <div class="form-group"><label for="valor_contribuicao">Valor de Contribuição Mensal (R$):</label><input type="text" id="valor_contribuicao" name="valor_contribuicao" required placeholder="Ex: 30.00"></div>
                    <button type="submit" id="btn-apadrinhar">Apadrinhar Animal</button>
                    <div class="form-feedback" id="feedback-apadrinhamento"></div>
                </form>
                <div id="listaApadrinhamentos" class="lista-container">Carregando...</div>
            </div>

            <!-- Aba: Adoções -->
            <div id="tab-adocoes" class="tab-content">
                <h3>Solicitação de Adoção</h3>
                <div style="margin-bottom:12px;">
                    <button onclick="document.getElementById('form-adocao').classList.toggle('hidden')">+ Solicitar Adoção</button>
                </div>
                <form id="form-adocao" class="form-card hidden">
                    <div class="form-group"><label for="id_animal">ID do Animal Desejado:</label><input type="number" id="id_animal" name="id_animal" required placeholder="Insira o ID (Ex: 1 para Rex)"></div>
                    <div class="form-group"><label for="motivo_adocao">Por que deseja adotar?</label><textarea id="motivo_adocao" name="motivo_adocao" rows="4" required></textarea></div>
                    <button type="submit" id="btn-adocao">Solicitar Adoção</button>
                    <div class="form-feedback" id="feedback-adocao"></div>
                </form>
                <div id="listaAdocoes" class="lista-container">Carregando...</div>
            </div>

            <!-- Aba: Eventos -->
            <div id="tab-eventos" class="tab-content">
                <h3>Eventos</h3>
                <div id="listaEventos" class="lista-container">Carregando...</div>
                <h4 style="margin-top:18px">Minhas Participações</h4>
                <div id="listaParticipacoes" class="lista-container">Carregando...</div>
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
        carregarAdocoes();
        carregarEventos();
        carregarParticipacoes();
        
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
            const valorFmt = (d.valor !== null && d.valor !== undefined && d.valor !== '') ? new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(d.valor)) : '-';
            html += `<tr>
                <td>${d.id_doacoes}</td>
                <td>${valorFmt}</td>
                <td>${d.forma_paga || '-'}</td>
                <td>${d.data_doacao ? new Date(d.data_doacao).toLocaleDateString('pt-BR') : ''}</td>
                <td><button class="btn-delete" onclick="excluirDoacao(${d.id_doacoes})">Excluir</button></td>
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

async function carregarAdocoes() {
    try {
        const { res, j } = await fetchJson('../app/models/Adocoes.php?action=listar');
        const div = document.getElementById('listaAdocoes');

        if (j.erro || !j.dados || j.dados.length === 0) {
            div.innerHTML = '<div class="lista-empty">Nenhuma solicitação de adoção registrada</div>';
            return;
        }

        let html = '<table class="lista-table"><thead><tr><th>ID</th><th>Animal ID</th><th>Motivo</th><th>Data</th><th>Ação</th></tr></thead><tbody>';

        j.dados.forEach(d => {
            html += `<tr>
                <td>${d.id_adocao}</td>
                <td>${d.animal_id || d.id_animal || ''}</td>
                <td>${d.motivo_adocao || d.motivo || '-'}</td>
                <td>${d.data_adocao ? new Date(d.data_adocao).toLocaleDateString('pt-BR') : ''}</td>
                <td><button class="btn-delete" onclick="excluirAdocao(${d.id_adocao})">Excluir</button></td>
            </tr>`;
        });

        html += '</tbody></table>';
        div.innerHTML = html;
    } catch (e) {
        document.getElementById('listaAdocoes').innerHTML = '<div class="lista-empty">Erro ao carregar adoções</div>';
    }
}

async function excluirAdocao(id) {
    if (!confirm('Tem certeza que deseja cancelar esta solicitação de adoção?')) return;
    try {
        const { res, j } = await fetchJson(`../app/models/Adocoes.php?action=deletar&id=${id}`, { method: 'POST' });
        if (!j.erro) {
            alert('Solicitação cancelada com sucesso!');
            carregarAdocoes();
        } else {
            alert('Erro: ' + j.mensagem);
        }
    } catch (e) {
        alert('Erro ao excluir solicitação');
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
        
        // obter participações do usuário para marcar presença
        let participacoes = [];
        try {
            const p = await fetchJson('../app/models/Participacoes.php?action=listar');
            if (!p.j.erro && Array.isArray(p.j.dados)) participacoes = p.j.dados;
        } catch (err) {
            // ignora
        }

        j.dados.forEach(e => {
            const dataEv = e.data_inicio || e.data_evento || '';
            const part = participacoes.find(p => Number(p.id_evento) === Number(e.id_evento));
            const actionBtn = part ? `<button class="btn-delete" onclick="cancelarParticipacao(${part.id_participacao})">Cancelar participação</button>` : `<button class="btn-primary" onclick="participarEvento(${e.id_evento})">Participar</button>`;
            html += `<tr>
                <td>${e.id_evento}</td>
                <td>${e.nome || e.titulo || ''}</td>
                <td>${dataEv ? new Date(dataEv).toLocaleDateString('pt-BR') : ''}</td>
                <td>${actionBtn}</td>
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

async function participarEvento(id_evento) {
    try {
        const resp = await fetch('../app/models/Participacoes.php?action=criar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_evento })
        });
        const json = await resp.json();
        if (!json.erro) {
            alert('Participação registrada com sucesso');
        } else {
            alert('Erro: ' + (json.mensagem||''));
        }
        carregarEventos();
        carregarParticipacoes();
    } catch (e) {
        alert('Erro ao participar do evento');
    }
}

async function cancelarParticipacao(id_participacao) {
    if (!confirm('Deseja cancelar sua participação?')) return;
    try {
        const resp = await fetch(`../app/models/Participacoes.php?action=deletar&id=${id_participacao}`, { method: 'POST' });
        const json = await resp.json();
        if (!json.erro) alert('Participação cancelada'); else alert('Erro: ' + (json.mensagem||''));
        carregarEventos();
        carregarParticipacoes();
    } catch (e) {
        alert('Erro ao cancelar participação');
    }
}

async function carregarParticipacoes() {
    try {
        const { res, j } = await fetchJson('../app/models/Participacoes.php?action=listar');
        const div = document.getElementById('listaParticipacoes');
        if (j.erro || !j.dados || j.dados.length === 0) {
            if (div) div.innerHTML = '<div class="lista-empty">Nenhuma participação registrada</div>';
            return;
        }
        let html = '<table class="lista-table"><thead><tr><th>ID</th><th>Evento</th><th>Data</th><th>Ação</th></tr></thead><tbody>';
        j.dados.forEach(p => {
            html += `<tr>
                <td>${p.id_participacao}</td>
                <td>${p.nome || ''}</td>
                <td>${p.data_inicio ? new Date(p.data_inicio).toLocaleDateString('pt-BR') : ''}</td>
                <td><button class="btn-delete" onclick="cancelarParticipacao(${p.id_participacao})">Cancelar</button></td>
            </tr>`;
        });
        html += '</tbody></table>';
        if (div) div.innerHTML = html;
    } catch (e) {
        const div = document.getElementById('listaParticipacoes');
        if (div) div.innerHTML = '<div class="lista-empty">Erro ao carregar participações</div>';
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