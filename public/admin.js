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

    document.getElementById("edit-id").value = u.id_usuario;
    document.getElementById("edit-nome").value = u.nome;
    document.getElementById("edit-email").value = u.email;
    document.getElementById("edit-telefone").value = u.telefone;
    document.getElementById("edit-cpf").value = u.cpf;
    document.getElementById("edit-tipo").value = u.tipo_usuario;

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
    }

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
    carregarUsuarios();
}

// ⚪ 5. CANCELAR EDIÇÃO
function cancelarEdicao() {
    document.getElementById("edit-form").classList.add("hidden");
}
