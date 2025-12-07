console.log("JS CARREGADO!");

/* ============================================================
INICIALIZAÇÃO
============================================================ */
document.addEventListener("DOMContentLoaded", function () {
    iniciarSmoothScroll();
    iniciarFormCadastro();
    iniciarFormDoacao();
    iniciarFormAdocao();
    iniciarFormApadrinhamento();
    iniciarFormEventos();
    iniciarCarouselEventos();
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
CAROUSEL DE EVENTOS (HOME)
============================================================ */
async function iniciarCarouselEventos() {
    const container = document.getElementById('carouselEventos');
    const slidesEl = document.getElementById('carouselSlides');
    if (!container || !slidesEl) return;

    slidesEl.textContent = 'Carregando eventos...';

    try {
        const res = await fetch('../app/models/Eventos.php?action=listar');
        const json = await res.json();
        if (json.erro || !json.dados || json.dados.length === 0) {
            slidesEl.innerHTML = '<div class="lista-empty">Nenhum evento para exibir</div>';
            return;
        }

        const eventos = json.dados;
        slidesEl.innerHTML = '';

        eventos.forEach((ev, idx) => {
            const slide = document.createElement('div');
            slide.className = 'carousel-slide';
            slide.style.display = (idx === 0) ? 'block' : 'none';

            const img = document.createElement('img');
            img.src = ev.imagem || 'imagens/banner_ong.jpeg';
            img.alt = ev.nome || ev.titulo || 'Evento';
            img.className = 'carousel-image';

            const info = document.createElement('div');
            info.className = 'carousel-info';
            const title = document.createElement('h4');
            title.textContent = ev.nome || ev.titulo || '';
            const date = document.createElement('div');
            const d = ev.data_inicio || ev.data_evento || '';
            date.textContent = d ? (new Date(d)).toLocaleDateString('pt-BR') : '';
            const desc = document.createElement('p');
            desc.textContent = ev.descricao || '';

            info.appendChild(title);
            info.appendChild(date);
            info.appendChild(desc);

            slide.appendChild(img);
            slide.appendChild(info);
            slidesEl.appendChild(slide);
        });

        // controles
        const prev = container.querySelector('.carousel-btn.prev');
        const next = container.querySelector('.carousel-btn.next');
        let current = 0;
        const slideEls = slidesEl.querySelectorAll('.carousel-slide');

        function show(n) {
            slideEls.forEach((s, i) => s.style.display = (i === n) ? 'block' : 'none');
            current = n;
        }

        if (prev) prev.addEventListener('click', () => show((current - 1 + slideEls.length) % slideEls.length));
        if (next) next.addEventListener('click', () => show((current + 1) % slideEls.length));

        // auto-advance a cada 6s
        setInterval(() => {
            show((current + 1) % slideEls.length);
        }, 6000);

    } catch (e) {
        slidesEl.innerHTML = '<div class="lista-empty">Erro ao carregar eventos</div>';
    }
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

        console.log("DEBUG → Dados cadastro:", dados);

        try {
            const resposta = await fetch("../app/models/Usuarios.php?action=cadastrar", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(dados)
            });

            const texto = await resposta.text();
            console.log("DEBUG → Resposta cadastro:", texto);

            let json;
            try {
                json = JSON.parse(texto);
            } catch (err) {
                console.warn("Resposta não é JSON válido.", err);
                mostrarFeedbackCadastro("Erro: resposta inválida", "error");
                return;
            }

            if (!json.erro) {
                mostrarFeedbackCadastro("✓ Usuário cadastrado com sucesso!", "success");
                form.reset();
            } else {
                mostrarFeedbackCadastro("❌ " + json.mensagem, "error");
            }

        } catch (erro) {
            console.error("Erro no fetch cadastro:", erro);
            mostrarFeedbackCadastro("❌ Erro de conexão. Tente novamente.", "error");
        }
    });
}

function mostrarFeedbackCadastro(mensagem, tipo) {
    const feedback = document.getElementById("feedback-cadastro");
    if (feedback) {
        feedback.textContent = mensagem;
        feedback.className = 'form-feedback ' + tipo;
        
        setTimeout(() => {
            feedback.className = 'form-feedback';
        }, 5000);
    }
}

/* ============================================================
FORM DOAÇÃO
============================================================ */
function iniciarFormDoacao() {
    const form = document.getElementById("form-doacao");
    if (!form) {
        console.error("Form #form-doacao não encontrado.");
        return;
    }

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const dados = {
            valor: form.querySelector("#valor")?.value ?? "",
            forma_paga: form.querySelector("#forma_paga")?.value ?? ""
        };

        console.log("DEBUG → Dados doação:", dados);

        try {
            const resposta = await fetch("../app/models/Doacoes.php?action=criar", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(dados)
            });

            const texto = await resposta.text();
            let json;
            try {
                json = JSON.parse(texto);
            } catch (err) {
                mostrarFeedbackDoacao("Erro: resposta inválida", "error");
                return;
            }

            if (!json.erro) {
                mostrarFeedbackDoacao("✓ Doação registrada com sucesso!", "success");
                form.reset();
            } else {
                mostrarFeedbackDoacao("❌ " + json.mensagem, "error");
            }

        } catch (erro) {
            console.error("Erro no fetch doação:", erro);
            mostrarFeedbackDoacao("❌ Erro de conexão. Tente novamente.", "error");
        }
    });
}

function mostrarFeedbackDoacao(mensagem, tipo) {
    const feedback = document.getElementById("feedback-doacao");
    if (feedback) {
        feedback.textContent = mensagem;
        feedback.className = 'form-feedback ' + tipo;
        
        setTimeout(() => {
            feedback.className = 'form-feedback';
        }, 5000);
    }
}

/* ============================================================
FORM ADOÇÃO
============================================================ */
function iniciarFormAdocao() {
    const form = document.getElementById("form-adocao");
    if (!form) {
        console.error("Form #form-adocao não encontrado.");
        return;
    }

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const dados = {
            id_animal: form.querySelector("#id_animal")?.value ?? "",
            motivo_adocao: form.querySelector("#motivo_adocao")?.value ?? ""
        };

        console.log("DEBUG → Dados adoção:", dados);

        try {
            const resposta = await fetch("../app/models/Adocoes.php?action=criar", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(dados)
            });

            const texto = await resposta.text();
            let json;
            try {
                json = JSON.parse(texto);
            } catch (err) {
                mostrarFeedbackAdocao("Erro: resposta inválida", "error");
                return;
            }

            if (!json.erro) {
                mostrarFeedbackAdocao("✓ Solicitação de adoção enviada!", "success");
                form.reset();
            } else {
                mostrarFeedbackAdocao("❌ " + json.mensagem, "error");
            }

        } catch (erro) {
            console.error("Erro no fetch adoção:", erro);
            mostrarFeedbackAdocao("❌ Erro de conexão. Tente novamente.", "error");
        }
    });
}

function mostrarFeedbackAdocao(mensagem, tipo) {
    const feedback = document.getElementById("feedback-adocao");
    if (feedback) {
        feedback.textContent = mensagem;
        feedback.className = 'form-feedback ' + tipo;
        
        setTimeout(() => {
            feedback.className = 'form-feedback';
        }, 5000);
    }
}

/* ============================================================
FORM APADRINHAMENTO
============================================================ */
function iniciarFormApadrinhamento() {
    const form = document.getElementById("form-apadrinhamento");
    if (!form) {
        console.error("Form #form-apadrinhamento não encontrado.");
        return;
    }

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const dados = {
            id_animal: form.querySelector("#id_animal_padrinho")?.value ?? "",
            valor_contribuicao: form.querySelector("#valor_contribuicao")?.value ?? ""
        };

        console.log("DEBUG → Dados apadrinhamento:", dados);

        try {
            const resposta = await fetch("../app/models/Apadrinhamento.php?action=criar", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(dados)
            });

            const texto = await resposta.text();
            let json;
            try {
                json = JSON.parse(texto);
            } catch (err) {
                mostrarFeedbackApadrinhamento("Erro: resposta inválida", "error");
                return;
            }

            if (!json.erro) {
                mostrarFeedbackApadrinhamento("✓ Apadrinhamento registrado com sucesso!", "success");
                form.reset();
            } else {
                mostrarFeedbackApadrinhamento("❌ " + json.mensagem, "error");
            }

        } catch (erro) {
            console.error("Erro no fetch apadrinhamento:", erro);
            mostrarFeedbackApadrinhamento("❌ Erro de conexão. Tente novamente.", "error");
        }
    });
}

function mostrarFeedbackApadrinhamento(mensagem, tipo) {
    const feedback = document.getElementById("feedback-apadrinhamento");
    if (feedback) {
        feedback.textContent = mensagem;
        feedback.className = 'form-feedback ' + tipo;
        
        setTimeout(() => {
            feedback.className = 'form-feedback';
        }, 5000);
    }
}

/* ============================================================
FORM EVENTOS
============================================================ */
function iniciarFormEventos() {
    const form = document.getElementById("form-eventos");
    if (!form) {
        console.error("Form #form-eventos não encontrado.");
        return;
    }

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const dados = {
            nome: form.querySelector("#nome_evento")?.value ?? "",
            descricao: form.querySelector("#descricao_evento")?.value ?? "",
            data_inicio: form.querySelector("#data_inicio")?.value ?? "",
            data_fim: form.querySelector("#data_fim")?.value ?? ""
        };

        console.log("DEBUG → Dados eventos:", dados);

        try {
            const resposta = await fetch("../app/models/Eventos.php?action=criar", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(dados)
            });

            const texto = await resposta.text();
            let json;
            try {
                json = JSON.parse(texto);
            } catch (err) {
                mostrarFeedbackEventos("Erro: resposta inválida", "error");
                return;
            }

            if (!json.erro) {
                mostrarFeedbackEventos("✓ Evento cadastrado com sucesso!", "success");
                form.reset();
                        // se existir função para recarregar lista (no perfil), chama
                        if (typeof carregarEventos === 'function') {
                            try { carregarEventos(); } catch(e) {}
                        }
            } else {
                mostrarFeedbackEventos("❌ " + json.mensagem, "error");
            }

        } catch (erro) {
            console.error("Erro no fetch eventos:", erro);
            mostrarFeedbackEventos("❌ Erro de conexão. Tente novamente.", "error");
        }
    });
}

function mostrarFeedbackEventos(mensagem, tipo) {
    const feedback = document.getElementById("feedback-eventos");
    if (feedback) {
        feedback.textContent = mensagem;
        feedback.className = 'form-feedback ' + tipo;
        
        setTimeout(() => {
            feedback.className = 'form-feedback';
        }, 5000);
    }
}
