const API_URL = "http://localhost/OngDogs/public/router.php/api/usuarios";

// utilitário para pegar campo com fallback entre várias chaves possíveis
function pegarCampo(obj, possiveis) {
    for (let key of possiveis) {
        if (obj.hasOwnProperty(key) && obj[key] !== undefined && obj[key] !== null) return obj[key];
    }
    return ""; // padrão vazio se não achar
}

// 🔵 1. LISTAR USUÁRIOS (GET)
async function carregarUsuarios() {
    try {
        const resp = await fetch(API_URL);
        const data = await resp.json();

        console.log("RETORNO API COMPLETO:", data);
        console.log("Primeiro objeto:", data.dados[0]);

        const tabela = document.getElementById("tabela-usuarios");
        tabela.innerHTML = "";

        if (!Array.isArray(data.dados)) {
            tabela.innerHTML = "<tr><td colspan='7'>API retornou formato inesperado.</td></tr>";
            return;
        }

        data.dados.forEach(user => {
            const id = pegarCampo(user, ["id_usuario","id","ID","usuario_id"]);
            const nome = pegarCampo(user, ["nome","Nome","name"]);
            const email = pegarCampo(user, ["email","Email"]);
            const telefone = pegarCampo(user, ["telefone","telefone_usuario","telefone_user","phone"]);
            const cpf = pegarCampo(user, ["cpf","CPF","cpf_usuario","CPF_usuario","cpf_user","CPF_user"]);
            const tipo = pegarCampo(user, ["tipo_usuario","tipo","tipoUser","tipo_usuario"]);

            tabela.innerHTML += `
                <tr>
                    <td>${id || "?"}</td>
                    <td>${nome}</td>
                    <td>${email}</td>
                    <td>${telefone}</td>
                    <td>${cpf}</td>
                    <td>${tipo}</td>
                    <td>
                        <button class="btn-edit" onclick="editarUsuario(${id})">Editar</button>
                        <button class="btn-delete" onclick="deletarUsuario(${id})">Excluir</button>
                    </td>
                </tr>
            `;
        });

    } catch (e) {
        console.error("Erro ao carregar usuários:", e);
    }
}

   

window.onload = carregarUsuarios;

// inicialização para abas e eventos
document.addEventListener('DOMContentLoaded', function(){
    const tabBtns = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.tab-panel');
    tabBtns.forEach(btn => {

// ---------------- Doações (Admin) ----------------
function carregarDoacoesAdmin(){
    fetch('../app/models/Doacoes.php?action=listar_todos')
    .then(r=>r.json())
    .then(json=>{
        const tbody = document.getElementById('tabela-doacoes');
        tbody.innerHTML = '';
        json.dados.forEach(d =>{
            const tr = document.createElement('tr');
            const valorFmt = (d.valor !== null && d.valor !== undefined && d.valor !== '') ? new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number(d.valor)) : '-';
            tr.innerHTML = `<td>${d.id_doacoes}</td><td>${d.id_usuario||''}</td><td>${valorFmt}</td><td>${d.forma_paga||''}</td><td>${d.status||''}</td><td>${d.data_doacao||''}</td><td><button onclick="editarDoacao(${d.id_doacoes})">Editar</button> <button onclick="deletarDoacao(${d.id_doacoes})">Excluir</button></td>`;
            tbody.appendChild(tr);
        });
    })
}

function editarDoacao(id){
    fetch('../app/models/Doacoes.php?action=listar_todos')
    .then(r=>r.json())
    .then(json =>{
        const item = json.dados.find(x=>x.id_doacoes==id);
        if(!item) return alert('Doação não encontrada');
        document.getElementById('edit-doacao-id').value = item.id_doacoes;
        document.getElementById('edit-doacao-usuario').value = item.id_usuario || '';
        document.getElementById('edit-doacao-valor').value = item.valor || '';
        // descricao may not exist in current schema; use status/forma_paga as available
        document.getElementById('edit-doacao-descricao').value = item.status || item.forma_paga || '';
        document.getElementById('edit-doacao-data').value = item.data_doacao ? item.data_doacao.replace(' ', 'T') : '';
        document.getElementById('edit-doacao-form').classList.remove('hidden');
    })
}

function salvarEdicaoDoacao(){
    const id = document.getElementById('edit-doacao-id').value;
    const payload = {
        id_usuario: document.getElementById('edit-doacao-usuario').value,
        valor: document.getElementById('edit-doacao-valor').value,
        forma_paga: document.getElementById('edit-doacao-descricao').value,
        status: document.getElementById('edit-doacao-descricao').value,
        data_doacao: document.getElementById('edit-doacao-data').value
    };
    fetch(`../app/models/Doacoes.php?action=alterar&id=${id}`,{
        method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)
    }).then(r=>r.json()).then(j=>{alert(j.mensagem||'Atualizado'); document.getElementById('edit-doacao-form').classList.add('hidden'); carregarDoacoesAdmin();})
}

function cancelarEdicaoDoacao(){
    document.getElementById('edit-doacao-form').classList.add('hidden');
}

function deletarDoacao(id){
    if(!confirm('Excluir doação?')) return;
    fetch(`../app/models/Doacoes.php?action=deletar&id=${id}`,{method:'POST'})
    .then(r=>r.json()).then(j=>{alert(j.mensagem||'Excluído'); carregarDoacoesAdmin();})
}

// ---------------- Apadrinhamentos (Admin) ----------------
function carregarApadrinhamentosAdmin(){
    fetch('../app/models/Apadrinhamento.php?action=listar_todos')
    .then(r=>r.json())
    .then(json=>{
        const tbody = document.getElementById('tabela-apadrinhamentos');
        tbody.innerHTML = '';
        json.dados.forEach(d =>{
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${d.id_apadrinhamento}</td><td>${d.usuario_id||''}</td><td>${d.animal_id||''}</td><td>${d.valor_mensal||''}</td><td><button onclick="editarApadrinhamento(${d.id_apadrinhamento})">Editar</button> <button onclick="deletarApadrinhamento(${d.id_apadrinhamento})">Excluir</button></td>`;
            tbody.appendChild(tr);
        });
    })
}

function editarApadrinhamento(id){
    fetch('../app/models/Apadrinhamento.php?action=listar_todos')
    .then(r=>r.json()).then(json=>{
        const item = json.dados.find(x=>x.id_apadrinhamento==id);
        if(!item) return alert('Apadrinhamento não encontrado');
        document.getElementById('edit-apadrinhamento-id').value = item.id_apadrinhamento;
        document.getElementById('edit-apadrinhamento-usuario').value = item.usuario_id||'';
        document.getElementById('edit-apadrinhamento-animal').value = item.animal_id||'';
        document.getElementById('edit-apadrinhamento-valor').value = item.valor_mensal||'';
        document.getElementById('edit-apadrinhamento-form').classList.remove('hidden');
    })
}

function salvarEdicaoApadrinhamento(){
    const id = document.getElementById('edit-apadrinhamento-id').value;
    const payload = {usuario_id:document.getElementById('edit-apadrinhamento-usuario').value, animal_id:document.getElementById('edit-apadrinhamento-animal').value, valor_mensal:document.getElementById('edit-apadrinhamento-valor').value};
    fetch(`../app/models/Apadrinhamento.php?action=alterar&id=${id}`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)})
    .then(r=>r.json()).then(j=>{alert(j.mensagem||'Atualizado'); document.getElementById('edit-apadrinhamento-form').classList.add('hidden'); carregarApadrinhamentosAdmin();})
}

function cancelarEdicaoApadrinhamento(){ document.getElementById('edit-apadrinhamento-form').classList.add('hidden'); }

function deletarApadrinhamento(id){ if(!confirm('Excluir apadrinhamento?')) return; fetch(`../app/models/Apadrinhamento.php?action=deletar&id=${id}`,{method:'POST'}).then(r=>r.json()).then(j=>{alert(j.mensagem||'Excluído'); carregarApadrinhamentosAdmin();}) }

// ---------------- Adoções (Admin) ----------------
function carregarAdocoesAdmin(){
    fetch('../app/models/Adocoes.php?action=listar_todos')
    .then(r=>r.json())
    .then(json=>{
        const tbody = document.getElementById('tabela-adocoes');
        tbody.innerHTML = '';
        json.dados.forEach(d =>{
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${d.id_adocao}</td><td>${d.usuario_id||''}</td><td>${d.animal_id||''}</td><td>${d.data_adocao||''}</td><td><button onclick="editarAdocao(${d.id_adocao})">Editar</button> <button onclick="deletarAdocao(${d.id_adocao})">Excluir</button></td>`;
            tbody.appendChild(tr);
        });
    })
}

function editarAdocao(id){
    fetch('../app/models/Adocoes.php?action=listar_todos')
    .then(r=>r.json()).then(json=>{
        const item = json.dados.find(x=>x.id_adocao==id);
        if(!item) return alert('Adoção não encontrada');
        document.getElementById('edit-adocao-id').value = item.id_adocao;
        document.getElementById('edit-adocao-usuario').value = item.usuario_id||'';
        document.getElementById('edit-adocao-animal').value = item.animal_id||'';
        document.getElementById('edit-adocao-data').value = item.data_adocao ? item.data_adocao.replace(' ', 'T') : '';
        document.getElementById('edit-adocao-form').classList.remove('hidden');
    })
}

function salvarEdicaoAdocao(){
    const id = document.getElementById('edit-adocao-id').value;
    const payload = {usuario_id:document.getElementById('edit-adocao-usuario').value, animal_id:document.getElementById('edit-adocao-animal').value, data_adocao:document.getElementById('edit-adocao-data').value};
    fetch(`../app/models/Adocoes.php?action=alterar&id=${id}`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)}).then(r=>r.json()).then(j=>{alert(j.mensagem||'Atualizado'); document.getElementById('edit-adocao-form').classList.add('hidden'); carregarAdocoesAdmin();})
}

function cancelarEdicaoAdocao(){ document.getElementById('edit-adocao-form').classList.add('hidden'); }

function deletarAdocao(id){ if(!confirm('Excluir adoção?')) return; fetch(`../app/models/Adocoes.php?action=deletar&id=${id}`,{method:'POST'}).then(r=>r.json()).then(j=>{alert(j.mensagem||'Excluído'); carregarAdocoesAdmin();}) }

// Ensure admin panels load data when selected
function onAdminTabChange(tabId){
    if(tabId==='tab-doacoes') carregarDoacoesAdmin();
    if(tabId==='tab-apadrinhamentos') carregarApadrinhamentosAdmin();
    if(tabId==='tab-adocoes') carregarAdocoesAdmin();
}
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            panels.forEach(p => p.classList.add('hidden'));
            btn.classList.add('active');
            const tab = btn.getAttribute('data-tab');
            if (tab === 'usuarios') document.getElementById('tab-usuarios').classList.remove('hidden');
            if (tab === 'eventos') document.getElementById('tab-eventos-admin').classList.remove('hidden');
            if (tab === 'doacoes') { document.getElementById('tab-doacoes').classList.remove('hidden'); onAdminTabChange('tab-doacoes'); }
            if (tab === 'apadrinhamentos') { document.getElementById('tab-apadrinhamentos').classList.remove('hidden'); onAdminTabChange('tab-apadrinhamentos'); }
            if (tab === 'adocoes') { document.getElementById('tab-adocoes').classList.remove('hidden'); onAdminTabChange('tab-adocoes'); }
            if (tab === 'formularios') document.getElementById('tab-formularios').classList.remove('hidden');
        });
    });

    // hook: criar evento pelo admin
    const formAdmin = document.getElementById('form-eventos-admin');
    if (formAdmin) {
        formAdmin.addEventListener('submit', async function(e){
            e.preventDefault();
            const dados = {
                nome: document.getElementById('admin_nome_evento').value,
                descricao: document.getElementById('admin_descricao_evento').value,
                data_inicio: document.getElementById('admin_data_inicio').value,
                data_fim: document.getElementById('admin_data_fim').value
            };
            try {
                const resp = await fetch('../app/models/Eventos.php?action=criar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dados)
                });
                const json = await resp.json();
                const fb = document.getElementById('feedback-eventos-admin');
                if (!json.erro) {
                    fb.textContent = json.mensagem || 'Evento criado';
                    fb.className = 'form-feedback success';
                    formAdmin.reset();
                    carregarEventosAdmin();
                } else {
                    fb.textContent = json.mensagem || 'Erro';
                    fb.className = 'form-feedback error';
                }
                setTimeout(()=>{ if (fb) fb.className = 'form-feedback'; }, 4000);
            } catch(e) {
                console.error(e);
            }
        });
    }
    // carregar eventos inicialmente quando admin abrir
    if (document.getElementById('tabela-eventos')) carregarEventosAdmin();
});

/* ============================================================
EVENTOS (ADMIN)
============================================================ */
async function carregarEventosAdmin() {
    try {
        const res = await fetch('../app/models/Eventos.php?action=listar');
        const json = await res.json();
        const tbody = document.getElementById('tabela-eventos');
        tbody.innerHTML = '';
        if (json.erro || !json.dados || json.dados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="lista-empty">Nenhum evento encontrado</td></tr>';
            return;
        }
        json.dados.forEach(ev => {
            const id = ev.id_evento;
            const nome = ev.nome || ev.titulo || '';
            const di = ev.data_inicio || ev.data_evento || '';
            const df = ev.data_fim || '';
            tbody.innerHTML += `<tr>
                <td>${id}</td>
                <td>${nome}</td>
                <td>${di ? new Date(di).toLocaleDateString('pt-BR') : ''}</td>
                <td>${df ? new Date(df).toLocaleDateString('pt-BR') : ''}</td>
                <td>
                    <button class="btn-edit" onclick="editarEvento(${id})">Editar</button>
                    <button class="btn-delete" onclick="deletarEventoAdmin(${id})">Excluir</button>
                </td>
            </tr>`;
        });
    } catch(e) {
        console.error('Erro ao carregar eventos', e);
    }
}

async function deletarEventoAdmin(id) {
    if (!confirm('Tem certeza que deseja excluir este evento?')) return;
    try {
        const res = await fetch(`../app/models/Eventos.php?action=deletar&id=${id}`, { method: 'POST' });
        const json = await res.json();
        alert(json.mensagem || 'Resposta recebida');
        carregarEventosAdmin();
    } catch(e) { console.error(e); alert('Erro ao excluir'); }
}

async function editarEvento(id) {
    try {
        const res = await fetch(`../app/models/Eventos.php?action=listar`);
        const json = await res.json();
        if (json.erro) return alert('Erro ao recuperar eventos');
        const ev = json.dados.find(x => Number(x.id_evento) === Number(id));
        if (!ev) return alert('Evento não encontrado');
        document.getElementById('edit-evento-id').value = ev.id_evento;
        document.getElementById('edit-evento-nome').value = ev.nome || ev.titulo || '';
        document.getElementById('edit-evento-descricao').value = ev.descricao || '';
        document.getElementById('edit-evento-data_inicio').value = ev.data_inicio || ev.data_evento || '';
        document.getElementById('edit-evento-data_fim').value = ev.data_fim || '';
        document.getElementById('edit-evento-form').classList.remove('hidden');
    } catch(e) { console.error(e); }
}

async function salvarEdicaoEvento() {
    const id = document.getElementById('edit-evento-id').value;
    const dados = {
        nome: document.getElementById('edit-evento-nome').value,
        descricao: document.getElementById('edit-evento-descricao').value,
        data_inicio: document.getElementById('edit-evento-data_inicio').value,
        data_fim: document.getElementById('edit-evento-data_fim').value
    };
    try {
        const res = await fetch(`../app/models/Eventos.php?action=alterar&id=${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        });
        const json = await res.json();
        alert(json.mensagem || 'Resposta recebida');
        document.getElementById('edit-evento-form').classList.add('hidden');
        carregarEventosAdmin();
    } catch(e) { console.error(e); alert('Erro ao salvar'); }
}

function cancelarEdicaoEvento() {
    document.getElementById('edit-evento-form').classList.add('hidden');
}

// 🔴 2. DELETAR (DELETE)
async function deletarUsuario(id) {
    if (!confirm("Tem certeza que deseja excluir este usuário?")) return;

    const resp = await fetch(`${API_URL}/${id}`, { method: "DELETE" });
    const data = await resp.json();

    alert(data.mensagem);
    carregarUsuarios();
}

// 🟠 3. ABRIR FORMULÁRIO DE EDIÇÃO
async function editarUsuario(id) {
    const resp = await fetch(`${API_URL}/${id}`);
    const data = await resp.json();

    const u = data.dados;

    document.getElementById("edit-id").value       = pegarCampo(u, ["id_usuario","id","ID"]);
    document.getElementById("edit-nome").value     = pegarCampo(u, ["nome","Nome"]);
    document.getElementById("edit-email").value    = pegarCampo(u, ["email"]);
    document.getElementById("edit-telefone").value = pegarCampo(u, ["telefone","telefone_usuario"]);
    document.getElementById("edit-cpf").value      = pegarCampo(u, ["cpf","CPF","cpf_usuario","CPF_usuario","cpf_user"]);
    document.getElementById("edit-tipo").value     = pegarCampo(u, ["tipo_usuario","tipo"]);
    
    document.getElementById("edit-form").classList.remove("hidden");
}

// 🟡 4. SALVAR EDIÇÃO (PUT)
async function salvarEdicao() {
    const id = document.getElementById("edit-id").value;

    const dadosAtualizados = {
        nome: document.getElementById("edit-nome").value,
        email: document.getElementById("edit-email").value,
        telefone: document.getElementById("edit-telefone").value,
        cpf: document.getElementById("edit-cpf").value,
        tipo_usuario: document.getElementById("edit-tipo").value,
    };

    const senhaCampo = document.getElementById("edit-senha");
    if (senhaCampo && senhaCampo.value.trim() !== "") {
        dadosAtualizados.senha = senhaCampo.value;
    }

    const resp = await fetch(`${API_URL}/${id}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(dadosAtualizados)
    });

    const data = await resp.json();
    alert(data.mensagem);

    document.getElementById("edit-form").classList.add("hidden");

    // 🔥 CORRETO: recarrega a listagem
    await carregarUsuarios();
}


// ⚪ 5. CANCELAR EDIÇÃO
function cancelarEdicao() {
    document.getElementById("edit-form").classList.add("hidden");
}
 