# Sistema de Chat com WebSocket em Laravel

Este é um sistema de chat simples semelhante ao WhatsApp, desenvolvido com **Laravel** para backend e WebSocket para comunicação em tempo real. Ele permite conversas privadas e em grupo.

## Funcionalidades

- **Autenticação de Usuários**: Login, registro e logout de usuários.
- **Conversas Privadas**: Troca de mensagens entre dois usuários.
- **Conversas em Grupo**: Troca de mensagens em grupos de múltiplos usuários.
- **Notificações em Tempo Real**: Utilizando WebSocket para notificações de novas mensagens em tempo real.

## Requisitos

- **PHP >= 8.3
- **Laravel >= 11.9
- **MySQL** ou outro banco de dados compatível
- **Node.js** (para execução de WebSockets e frontend)
- **Redis** (para filas de trabalho)

## Instalação

### Passos para configurar o projeto:

1. Clone o repositório:
    ```bash
    git clone https://github.com/seuusuario/chat-app.git
    cd sistema-chat-laravel
    ```

2. Instale as dependências do PHP:
    ```bash
    composer install
    ```

3. Copie o arquivo `.env` e configure o banco de dados e os detalhes de WebSocket:
    ```bash
    cp .env.example .env
    ```

4. Gere a chave da aplicação:
    ```bash
    php artisan key:generate
    ```

5. Configure as variáveis de ambiente para o **Pusher** e o **Redis** no arquivo `.env`:
    ```env
    BROADCAST_DRIVER=pusher
    PUSHER_APP_ID=your-app-id
    PUSHER_APP_KEY=your-app-key
    PUSHER_APP_SECRET=your-app-secret
    PUSHER_APP_CLUSTER=mt1
    ```

6. Execute as migrações para criar as tabelas no banco de dados:
    ```bash
    php artisan migrate
    ```

7. Inicie o servidor WebSocket:
    ```bash
    php artisan websockets:serve
    ```

8. Compile os assets frontend (opcional):
    ```bash
    npm install
    npm run dev
    ```

## Endpoints da API

### Autenticação

- **POST /api/login**: Login de usuário.
- **POST /api/register**: Registro de novo usuário.
- **POST /api/logout**: Logout do usuário autenticado.

### Conversas

- **GET /api/conversations**: Lista as conversas do usuário autenticado.
- **POST /api/conversations**: Cria uma nova conversa (privada ou em grupo).
    - Payload de exemplo para grupo:
      ```json
      {
        "name": "Nome do Grupo",
        "type": "group",
        "users": [1, 2, 3]
      }
      ```
- **GET /api/conversations/{id}**: Detalhes de uma conversa (inclui membros e mensagens).

### Mensagens

- **GET /api/conversations/{id}/messages**: Lista todas as mensagens de uma conversa.
- **POST /api/conversations/{id}/messages**: Envia uma nova mensagem na conversa.
    - Payload de exemplo:
      ```json
      {
        "content": "Hello, World!"
      }
      ```
- **GET /api/conversations/{id}/messages/{messageId}**: Visualiza uma mensagem específica.

### Usuários

- **GET /api/users**: Lista todos os usuários cadastrados.
- **GET /api/users/{id}**: Detalhes de um usuário específico.

## Estrutura do Banco de Dados

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid UUID,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE conversations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('private', 'group') DEFAULT 'private',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE conversation_user (
    conversation_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Quando o usuário entrou na conversa (útil para grupos)
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (conversation_id, user_id)
);

CREATE TABLE messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    conversation_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    content TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
