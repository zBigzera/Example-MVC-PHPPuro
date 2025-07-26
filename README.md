# Example MVC em PHP Puro

Este projeto é uma implementação simples do padrão MVC (Model-View-Controller) utilizando PHP puro, com objetivo educacional e como base para futuros projetos.

## 📖 Histórico do Projeto

  🔹 Primeira versão (branch `WDEV`): baseada na playlist do canal WDEV no YouTube — estrutura inicial do MVC em PHP puro.

  🔸 Segunda versão (branch `twig`): adição do Twig como template engine para melhorar a organização e reutilização de views.
  🔺 Versão anterior (branch `fat-model`): diversas melhorias na estrutura, organização e legibilidade do código, uso de singleton para conexão com o banco de dados, uso de PHP-DI para injeção de dependências, versão recomendada para projetos reais, mas ainda sofre de sobrecarga na model.
  ⭐ Versão principal (branch `main`): Diminuição da sobrecarga da model com MVC Layered, adicionando camadas SIMPLES de abstração.

## 📚 Conceitos Abordados

- Estrutura MVC (Model-View-Controller)
- Sistema de rotas customizado
- CRUD com paginação
- Uso de variáveis de ambiente
- Middlewares personalizados
- Autenticação de usuários
- Painel administrativo básico
- Twig template engine
- APIs RESTful com autenticação:
  - HTTP Basic Auth
  - JWT (JSON Web Token)
- Gerenciamento de cache
- PHP orientado a objetos
- Organização e boas práticas em PHP puro
- Abstração da model em camadas de DAO, DTO e Services.
