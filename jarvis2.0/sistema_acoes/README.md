# Sistema de Cadastro de Ações

Um sistema web completo desenvolvido em PHP, HTML, CSS, Bootstrap e MySQL para cadastro e visualização de ações educacionais como filmes, debates, curtas e palestras.

## 🚀 Funcionalidades

### Página de Cadastro (index.php)
- Formulário completo para cadastro de ações
- Tipos de ação: Filme, Debate, Curta, Palestra
- Campos obrigatórios:
  - Nome do coordenador
  - Local do evento
  - Descrição
  - Data do evento
  - Horário inicial e final
  - Campus (RN, C, IP)
- Upload de foto de capa
- Upload de galeria de fotos (múltiplas imagens)
- Design responsivo com Bootstrap
- Validação de formulário

### Página de Visualização (visualizar.php)
- Exibição de todas as ações cadastradas em cards elegantes
- Informações organizadas e bem apresentadas
- Galeria de fotos com lightbox
- Filtros visuais por tipo de ação
- Botão para voltar ao cadastro
- Design responsivo

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 8.1
- **Frontend**: HTML5, CSS3, Bootstrap 5.3
- **Banco de Dados**: MySQL 8.0
- **Servidor Web**: Apache 2.4
- **Recursos Adicionais**: 
  - Font Awesome (ícones)
  - Lightbox2 (galeria de fotos)
  - Gradientes CSS personalizados
  - Animações e transições

## 📋 Pré-requisitos

- PHP 8.1 ou superior
- MySQL 8.0 ou superior
- Apache 2.4 ou superior
- Extensões PHP: pdo, pdo_mysql

## 🔧 Instalação

1. **Clone ou copie os arquivos do projeto**
   ```bash
   cp -r sistema_acoes/* /var/www/html/
   ```

2. **Configure as permissões**
   ```bash
   sudo chown -R www-data:www-data /var/www/html/
   sudo chmod -R 755 /var/www/html/
   ```

3. **Configure o banco de dados**
   ```bash
   mysql -u root -p
   ```
   ```sql
   CREATE DATABASE sistema_acoes;
   CREATE USER 'usuario'@'localhost' IDENTIFIED BY 'senha123';
   GRANT ALL PRIVILEGES ON sistema_acoes.* TO 'usuario'@'localhost';
   FLUSH PRIVILEGES;
   ```

4. **Execute o script de criação das tabelas**
   ```bash
   mysql -u usuario -psenha123 sistema_acoes < database.sql
   ```

5. **Inicie os serviços**
   ```bash
   sudo systemctl start apache2
   sudo systemctl start mysql
   ```

## 📁 Estrutura do Projeto

```
sistema_acoes/
├── index.php              # Página principal de cadastro
├── visualizar.php          # Página de visualização das ações
├── config.php             # Configurações do banco de dados
├── database.sql           # Script de criação das tabelas
├── uploads/               # Diretório para uploads
│   ├── capas/            # Fotos de capa
│   └── galerias/         # Fotos da galeria
└── README.md             # Este arquivo
```

## 🗃️ Estrutura do Banco de Dados

### Tabela: acoes
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `tipo_acao` (ENUM: 'filme', 'debate', 'curta', 'palestra')
- `nome_coordenador` (VARCHAR 255)
- `local_evento` (VARCHAR 255)
- `descricao` (TEXT)
- `data_evento` (DATE)
- `horario_inicial` (TIME)
- `horario_final` (TIME)
- `campus` (ENUM: 'RN', 'C', 'IP')
- `foto_capa` (VARCHAR 255)
- `galeria_fotos` (TEXT)
- `data_cadastro` (TIMESTAMP)

### Tabela: uploads
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `nome_arquivo` (VARCHAR 255)
- `caminho_arquivo` (VARCHAR 255)
- `acao_id` (INT, FOREIGN KEY)
- `tipo` (ENUM: 'capa', 'galeria')
- `data_upload` (TIMESTAMP)

## 🎨 Características do Design

- **Gradiente moderno**: Cores roxas e azuis
- **Cards elegantes**: Com sombras e efeitos hover
- **Ícones intuitivos**: Font Awesome para melhor UX
- **Responsividade**: Funciona em desktop e mobile
- **Animações suaves**: Transições CSS para interações
- **Tipografia moderna**: Segoe UI como fonte principal

## 🔒 Segurança

- Validação de tipos de arquivo para uploads
- Sanitização de dados de entrada
- Uso de prepared statements (PDO)
- Nomes únicos para arquivos uploadados

## 📱 Responsividade

O sistema é totalmente responsivo e funciona perfeitamente em:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (até 767px)

## 🚀 Como Usar

1. Acesse `http://localhost/index.php`
2. Preencha o formulário de cadastro com as informações da ação
3. Faça upload da foto de capa e galeria (opcional)
4. Clique em "Cadastrar Ação"
5. Para visualizar as ações, clique em "Ver Ações Cadastradas"
6. Na página de visualização, você pode ver todas as ações em cards organizados
7. Use o botão "Voltar ao Cadastro" para retornar à página principal

## 🔧 Configurações

Para alterar as configurações do banco de dados, edite o arquivo `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'usuario');
define('DB_PASS', 'senha123');
define('DB_NAME', 'sistema_acoes');
```

## 📝 Notas Importantes

- As imagens são armazenadas nos diretórios `uploads/capas/` e `uploads/galerias/`
- Formatos de imagem aceitos: JPG, JPEG, PNG, GIF
- O sistema gera nomes únicos para evitar conflitos de arquivos
- A galeria de fotos usa Lightbox2 para visualização ampliada

## 🐛 Solução de Problemas

### Erro de permissão nos uploads
```bash
sudo chmod -R 755 uploads/
sudo chown -R www-data:www-data uploads/
```

### Erro de conexão com banco
- Verifique se o MySQL está rodando
- Confirme as credenciais em `config.php`
- Execute o script `database.sql`

### Página em branco
- Verifique os logs do Apache: `tail -f /var/log/apache2/error.log`
- Confirme se o PHP está funcionando: `php -v`

## 👨‍💻 Desenvolvido com

Este sistema foi desenvolvido seguindo as melhores práticas de desenvolvimento web, com foco em:
- Código limpo e bem documentado
- Design moderno e intuitivo
- Segurança e validação de dados
- Responsividade e acessibilidade

