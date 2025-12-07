<?php
session_start();
$user = null;
if (isset($_SESSION['user_id'])) {
    include_once __DIR__ . '/../app/models/Usuarios.php';
    $res = Usuarios::buscarUsuarioPeloId($_SESSION['user_id']);
    if (!$res['erro']) $user = $res['dados'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instituto Eu Sou Bicho - Adoção e Cuidado Animal</title>
    <link rel="stylesheet" href="style.css?v=final">

    

</head>
<body >
    <header>
        <img src="imagens/logo_ong.png" alt="Logo Instituto Eu Sou Bicho" class="logo">
        <nav>
            <a href="#home">Home</a>
            <a href="#como-ajudar">Como Ajudar</a>
            <a href="#animais">Animais</a>
            <a href="#missao">Missão</a>
            <a href="#contato">Contato</a>
            <a href="#formularios">Formulários</a>
            <a href="#depoimentos">Depoimentos</a>
            <?php if ($user): ?>
                <a href="admin.php">Admin</a>
                <a id="logoutLink" href="login.php">Sair (<?php echo htmlspecialchars($user['nome'] ?? 'Conta'); ?>)</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
            <?php endif; ?>
        </nav>
    </header>

    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Uma Nova Chance. Um Novo Lar.</h1>
        </div>
    </section>

    <section id="como-ajudar">
        <h2>Como Você Pode Ajudar?</h2>
        <div class="servicos-container">
            <div class="servico">
                <h3>Adoção</h3>
                <p> O ato de amor definitivo. Transforme a vida de um animal resgatado. </p>
            </div>
            <div class="servico">
                <h3> Doação </h3>
                <p> Ajude a custear ração, vacinas e cuidados veterinários essenciais. </p>
            </div>
            <div class="servico">
                <h3> Apadrinhamento </h3>
                <p> Apoie financeiramente um animal específico até que ele encontre um lar. </p>
            </div>
            <div class="servico">
                <h3> Voluntariado </h3>
                <p> Doe seu tempo. Ajude na limpeza, passeios e eventos de resgate. </p>
            </div>
        </div>
    </section>
    
    <br>
    <br>

    <section id="animais">

        <h2>Animais Disponíveis para Adoção</h2>
    
        <div class="galeria-container">
    
            <div class="item">
    
            <img src="imagens/animal_rex.jpeg" alt="Cachorro Rex Vira-lata">
    
            <p> Rex, 3 anos. Amigável e brincalhão. </p>
    
        </div>
    
        <div class="item">
    
            <img src="imagens/animal_luna.jpeg" alt="Cachorra Luna Poodle">
    
            <p> Luna, 5 anos. Tranquila e carinhosa. </p>
    
        </div>
    
        <div class="item">
    
            <img src="imagens/animal_mingau.jpeg" alt="Gato Mingau Persa">
    
            <p> Mingau, 2 anos. Calmo e independente. </p>
    
        </div>
    
        </div>
    
     </section>

    <section id="missao">
        <h2>Nossa Missão</h2>
        <p>
            O Instituto Eu Sou Bicho dedica-se a transformar a vida de cães e gatos em situação de vulnerabilidade. Nosso foco é o resgate, a reabilitação e a promoção da adoção responsável.
        </p>
        <p>
            Mais do que prover cuidados veterinários e alimentação de qualidade, nosso compromisso é garantir que cada animal resgatado encontre um lar permanente, seguro e, acima de tudo, cheio de amor.
        </p>
    </section>

    <section id="formularios" class="formulario-padrao">
        <h2>Área de Formulários</h2>
        <p>Utilize os formulários abaixo para interagir com o Instituto.</p>

        <div class="forms-container">
            
            <h3>1. Cadastro de Novo Usuário</h3>
            <form id="form-cadastro" class="form-card">
                <div class="form-group"><label for="nome">Nome:</label><input type="text" id="nome" name="nome" required maxlength="50"></div>
                <div class="form-group"><label for="email">E-mail:</label><input type="email" id="email" name="email" required maxlength="40"></div>
                <div class="form-group"><label for="senha">Senha:</label><input type="password" id="senha" name="senha" required maxlength="20"></div>
                <div class="form-group"><label for="telefone">Telefone:</label><input type="tel" id="telefone" name="telefone" placeholder="(DD) 9XXXX-XXXX" maxlength="16"></div>
                <div class="form-group"><label for="cpf">CPF:</label><input type="text" id="cpf" name="CPF" required maxlength="16"></div>
                <input type="hidden" name="tipo_usuario" value="Adotante">
                <button type="submit" id="btn-cadastrar">Cadastrar</button>
                <div class="form-feedback" id="feedback-cadastro"></div>
            </form>
            <br>

            <h3>2. Formulário de Doação</h3>
            <form id="form-doacao" class="form-card">
                <div class="form-group"><label for="valor">Valor da Doação (R$):</label><input type="text" id="valor" name="valor" required placeholder="Ex: 50.00"></div>
                <div class="form-group">
                    <label for="forma_paga">Forma de Pagamento:</label>
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

            <h3>3. Formulário de Solicitação de Adoção</h3>
            <form id="form-adocao" class="form-card">
                <div class="form-group"><label for="id_animal">ID do Animal Desejado:</label><input type="number" id="id_animal" name="id_animal" required placeholder="Insira o ID (Ex: 1 para Rex)"></div>
                <div class="form-group"><label for="motivo_adocao">Por que deseja adotar?</label><textarea id="motivo_adocao" name="motivo_adocao" rows="4" required></textarea></div>
                <button type="submit" id="btn-adocao">Solicitar Adoção</button>
                <div class="form-feedback" id="feedback-adocao"></div>
            </form>

            <h3>4. Formulário de Apadrinhamento</h3>
            <form id="form-apadrinhamento" class="form-card">
                <div class="form-group"><label for="id_animal_padrinho">Animal que deseja apadrinhar:</label><input type="number" id="id_animal_padrinho" name="id_animal" required placeholder="Insira o ID do Animal (Ex: 2)"></div>
                <div class="form-group"><label for="valor_contribuicao">Valor de Contribuição Mensal (R$):</label><input type="text" id="valor_contribuicao" name="valor_contribuicao" required placeholder="Ex: 30.00"></div>
                <button type="submit" id="btn-apadrinhar">Apadrinhar Animal</button>
                <div class="form-feedback" id="feedback-apadrinhamento"></div>
            </form>

            <h3>5. Formulário de Cadastro de Eventos</h3>
            <form id="form-eventos" class="form-card">
                <div class="form-group"><label for="nome_evento">Nome do Evento:</label><input type="text" id="nome_evento" name="nome" required maxlength="50"></div>
                <div class="form-group"><label for="descricao_evento">Descrição (máx. 90 caracteres):</label><textarea id="descricao_evento" name="descricao" rows="3" maxlength="90"></textarea></div>
                <div class="form-group"><label for="data_inicio">Data de Início:</label><input type="date" id="data_inicio" name="data_inicio" required></div>
                <div class="form-group"><label for="data_fim">Data de Fim (Opcional):</label><input type="date" id="data_fim" name="data_fim"></div>
                <button type="submit" id="btn-cadastrar-evento">Cadastrar Evento</button>
                <div class="form-feedback" id="feedback-eventos"></div>
            </form>

        </div>
    </section>

    <section id="contato">
        <h2 class="titulo-contato">Entre em Contato</h2>
        <p>
            E-mail: <a href="mailto:contato@institutoeusouobicho.org.br" target="_blank">contato@institutoeusouobicho.org.br</a> <br>  
            Instagram: <a href="https://www.instagram.com/institutoeusouobicho/" target="_blank">@InstitutoEuSouBicho</a>
        </p>
        <p>
            📍 Endereço da Sede: Av. Alberto Byington, 2554 - Vila Maria, São Paulo - SP
        </p>
        
        <div class="mapa">
            <h3>Nossa Localização</h3>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3658.83372277741!2d-46.582796099999996!3d-23.502497899999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce5f0c99eff367%3A0xfaeb18916a1c481b!2sInstituto%20Eu%20Sou%20o%20Bicho!5e0!3m2!1spt-BR!2sbr!4v1763303470559!5m2!1spt-BR!2sbr..." 
                width="100%" 
                height="350" 
                style="border:0; border-radius:12px;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>

    <section id="depoimentos">
        <h2>Histórias de Sucesso e Depoimentos</h2>
        
        <div class="avaliacoes-container">

            <div class="avaliacao-card">
                <p class="comentario">"Adotei a Mel..."</p>
                <div class="estrelas">⭐⭐⭐⭐⭐</div>
                <span class="cliente">- Família G.</span>
            </div>

            <div class="avaliacao-card">
                <p class="comentario">"Contribuo mensalmente. É ótimo ver o impacto real das doações!"</p>
                <div class="estrelas">⭐⭐⭐⭐⭐</div>
                <span class="cliente">- Roberto A.</span>
            </div>
            
            <div class="avaliacao-card">
                <p class="comentario">"Apadrinhar o Tobi foi a melhor decisão. Recebo fotos dele e sei que estou fazendo a diferença, mesmo de longe."</p>
                <div class="estrelas">⭐⭐⭐⭐⭐</div>
                <span class="cliente">- Clara R.</span>
            </div>
            
            <div class="avaliacao-card">
                <p class="comentario">"Fiquei impressionado com a organização e o cuidado que eles têm com os animais. O processo de adoção foi sério e muito humano."</p>
                <div class="estrelas">⭐⭐⭐⭐</div>
                <span class="cliente">- Marcos P.</span>
            </div>

            <div class="avaliacao-card">
                <p class="comentario">"Ser voluntário mudou minha perspectiva. A equipe é incrivelmente dedicada e os animais recebem muito amor."</p>
                <div class="estrelas">⭐⭐⭐⭐⭐</div>
                <span class="cliente">- Júlia S.</span>
            </div>

            <div class="avaliacao-card">
                <p class="comentario">"Fiz uma doação e pude ver exatamente onde o dinheiro foi aplicado. Transparência total e um trabalho maravilhoso."</p>
                <div class="estrelas">⭐⭐⭐⭐</div>
                <span class="cliente">- Paulo F.</span>
            </div>
            </div> </section>

    <footer>
        <p>©2025 Instituto Eu Sou Bicho - Todos os direitos reservados.</p>
    </footer>

    <div class="floating-buttons-container">
        <a href="https://www.instagram.com/institutoeusouobicho/" class="floating-btn instagram-float" target="_blank">
            <img src="imagens/instagram.png" alt="Instagram" />
        </a>
        <a href="mailto:contato@institutoeusouobicho.org.br" class="floating-btn email-float">
            <img src="imagens/email.png" alt="E-mail" />
        </a>
    </div>
    
    <script src="script.js"></script>

</body>
</html>