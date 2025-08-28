# Social-Network

Rede social acadêmica desenvolvida em PHP, MySQL, HTML, CSS e JavaScript. Permite cadastro, login, criação de publicações, grupos, amizades, notificações e gerenciamento de perfil.

## Estrutura de Pastas

```
app/
  controllers/         # Lógica dos controladores (ex: autenticação, posts, usuários)
  models/              # Modelos de dados
  views/               # Templates das páginas
database/
  config.php           # Configuração de conexão com o banco de dados
public/
  actions/             # Scripts PHP para ações (CRUD, autenticação, grupos, etc)
  pages/               # Páginas principais do site (login, perfil, início, grupo)
  src/
    css/               # Arquivos de estilo CSS
    js/                # Scripts JavaScript
    icons/             # Ícones do site
  uploads/             # Imagens de perfil e grupos
README.md              # Este arquivo
```

## Principais Arquivos

- [`database/config.php`](database/config.php) ⚙️: Configuração da conexão MySQL.
- [`public/pages/index.php`](public/pages/index.php) 🏠: Página inicial, registro e login.
- [`public/pages/inicio.php`](public/pages/inicio.php) 📰: Feed principal, publicações, grupos e amigos.
- [`public/pages/perfil.php`](public/pages/perfil.php) 👤: Página de perfil do usuário.
- [`public/pages/grupo.php`](public/pages/grupo.php) 👥: Página de detalhes de grupo.
- [`public/actions/criar_publicacao.php`](public/actions/criar_publicacao.php) ✍️: Criação de publicações.
- [`public/actions/criar_grupo.php`](public/actions/criar_grupo.php) ➕: Criação de grupos.
- [`public/actions/enviar_pedido.php`](public/actions/enviar_pedido.php) 🤝: Envio de pedido de amizade.
- [`public/actions/aceitar_pedido.php`](public/actions/aceitar_pedido.php) ✅: Aceitação de pedido de amizade.
- [`public/actions/recusar_pedido.php`](public/actions/recusar_pedido.php) ❌: Recusa de pedido de amizade.
- [`public/actions/editar-perfil.php`](public/actions/editar-perfil.php) 📝: Edição de perfil.
- [`public/actions/atualizar_foto.php`](public/actions/atualizar_foto.php) 🖼️: Atualização da foto de perfil.
- [`public/actions/buscar.php`](public/actions/buscar.php) 🔍: Busca de usuários e grupos.
- [`public/src/css/`](public/src/css/) 🎨: Estilos visuais do site.
- [`public/src/js/`](public/src/js/) ⚡: Scripts de interação do frontend.
- [`public/src/icons/`](public/src/icons/) 🖼️: Ícones utilizados no site.

## Instalação

1. Clone o repositório e configure o ambiente local (XAMPP recomendado).
2. Importe o banco de dados MySQL conforme o modelo em `database/config.php`.
3. Ajuste as credenciais do banco em [`database/config.php`](database/config.php).
4. Certifique-se que a pasta `uploads/` tem permissão de escrita.

## Funcionalidades

- **Cadastro e Login:** Usuários podem se registrar e autenticar.
- **Perfil:** Visualização e edição de perfil, foto, biografia.
- **Publicações:** Criar, visualizar e excluir publicações com imagens.
- **Grupos:** Criar, entrar, sair e gerenciar grupos.
- **Amizades:** Enviar, aceitar e recusar pedidos de amizade.
- **Notificações:** Receber e marcar notificações como lidas.
- **Busca:** Pesquisar usuários e grupos.
- **Feed:** Visualizar atividades recentes e amigos online.

## Documentação das Rotas Principais

- `/public/pages/index.php` 🏠 - Página de login e cadastro.
- `/public/pages/inicio.php` 📰 - Feed principal após login.
- `/public/pages/perfil.php?id={id}` 👤 - Perfil do usuário.
- `/public/pages/grupo.php?id={id}` 👥 - Página de grupo.
- `/public/actions/criar_publicacao.php` ✍️ - POST para criar publicação.
- `/public/actions/criar_grupo.php` ➕ - POST para criar grupo.
- `/public/actions/enviar_pedido.php?id={id}` 🤝 - GET para enviar pedido de amizade.
- `/public/actions/aceitar_pedido.php?id={id}` ✅ - GET para aceitar pedido.
- `/public/actions/recusar_pedido.php?id={id}` ❌ - GET para recusar pedido.
- `/public/actions/editar-perfil.php` 📝 - GET/POST para editar perfil.
- `/public/actions/atualizar_foto.php` 🖼️ - POST para atualizar foto de perfil.
- `/public/actions/buscar.php?termo={busca}` 🔍 - GET para buscar usuários/grupos.

## Observações

- O projeto utiliza Bootstrap e Font Awesome para o frontend.
- Scripts JavaScript para interações dinâmicas estão em [`public/src/js/`](public/src/js/).
- As ações PHP utilizam prepared statements para segurança contra SQL Injection.
- Os ícones do site estão em [`public/src/icons/`](public/src/icons/).

## Licença

---

Dúvidas ou sugestões? Abra uma issue ou entre em contato!