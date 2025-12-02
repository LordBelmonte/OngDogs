console.log("JS CARREGADO!");


/* ============================================================
INICIALIZAÇÃO
============================================================ */
document.addEventListener("DOMContentLoaded", function () {

iniciarSmoothScroll();
iniciarFormCadastro();

});

/* ============================================================
SMOOTH SCROLL
============================================================ */
function iniciarSmoothScroll() {
const linksDoMenu = document.querySelectorAll('header nav a[href^="#"]');


linksDoMenu.forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();

        const destino = document.querySelector(this.getAttribute('href'));
        if (destino) {
            destino.scrollIntoView({ behavior: 'smooth' });
        }
    });
});


}

/* ============================================================
FORM CADASTRO DE USUÁRIO
============================================================ */
function iniciarFormCadastro() {
const form = document.getElementById("form-cadastro");
if (!form) {
    console.error("Form #form-cadastro não encontrado.");
    return;
}

form.addEventListener("submit", async function (e) {
    e.preventDefault();

    // Captura segura dos campos
    const dados = {
        nome: form.querySelector("#nome")?.value ?? "",
        email: form.querySelector("#email")?.value ?? "",
        senha: form.querySelector("#senha")?.value ?? "",
        telefone: form.querySelector("#telefone")?.value ?? "",
        cpf: form.querySelector("#cpf")?.value 
           ?? form.querySelector('[name="cpf"]')?.value
           ?? form.querySelector('[name="CPF"]')?.value
           ?? "",
        tipo_usuario: "Adotante"
    };

    console.log("DEBUG → Dados a enviar:", dados);

    try {
        const resposta = await fetch("http://localhost/OngDogs/public/router.php/api/usuarios", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dados)
        });

        console.log("DEBUG → status:", resposta.status, resposta.statusText);

        const texto = await resposta.text();
        console.log("DEBUG → body bruto:", texto);

        // Tentar converter em JSON
        let json;
        try {
            json = JSON.parse(texto);
        } catch (err) {
            console.warn("Resposta não é JSON válido.", err);
            alert("Erro: resposta inválida da API. Veja console.");
            return;
        }

        console.log("DEBUG → JSON parseado:", json);

        if (!json.erro) {
            alert("Usuário cadastrado com sucesso!");
            form.reset();
        } else {
            alert("Erro da API: " + json.mensagem);
        }

    } catch (erro) {
        console.error("DEBUG → Erro no fetch:", erro);
        alert("Falha ao conectar com a API. Veja console.");
    }
});
}
