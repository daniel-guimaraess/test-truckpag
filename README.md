# Backend Challenge 20230105

## Descrição
Projeto focado em desenvolver uma REST API, com os dados do projeto Open Food Facts, realizando a importação dos dados a partir de uma automação com Cron, e persistência no banco de dados. Conta também com um sistema para emissão de alertas das importações dos dados.

## Tecnologias utilizadas
**Linguagem:** PHP 8.3.20<br>
**Framework:** Laravel 12<br>
**Banco de dados:** MySQL 8.0<br>
**Documentação:** Swagger Open API 3.0<br>
**Containerização:** Docker<br>
**Integração de Mensagens:** API do Telegram (Bot) e Open Food Facts

## Instalação e configuração local
### Instalação tecnologias
- PHP 8.3 - <a href="https://www.php.net/downloads.php">Link</a>
- Composer - <a href="https://getcomposer.org/">Link</a>
- Laravel 12 - <a href="https://laravel.com/docs/12.x/installation">Link</a>
- MySQL - <a href="https://www.digitalocean.com/community/tutorials/how-to-install-mysql-on-ubuntu-20-04">Link</a>

### Variáveis de ambiente (.env):
Após realizar as instalações das tecnologias acima, e clonar o projeto para a máquina local, é necessário configurar as variáveis de ambiente abaixo:

Renomeie arquivo .env.example para .env 

- **Telegram**
    - **CHAT_ID_TELEGRAM:** ID do canal ou conversa do Telegram.
    - **BOT_TOKEN_TELEGRAM:** Token do bot criado, para envio de alertas.

- **Banco de dados**
    - Preencha com as informações do seu banco de dados, os campos abaixo:
    - **DB_CONNECTION**, **DB_HOST**, **DB_PORT**, **DB_DATABASE**, **DB_USERNAME**, **DB_PASSWORD**


### Comandos PHP

#### 1. Instalar as depêndencias do Laravel
```bash
composer install
```

#### 2. Criar no dotenv a chave do projeto
    
```bash
php artisan key:generate
```

#### 3. Executar as migrations, criando as tabelas no banco de dados

```bash
php artisan migrate
```

#### 4. Executar os Seeders, inserindo os dados de usuário e alerta no banco de dados

```bash
php artisan db:seed
```

#### 5. Criar chave secreta para assinatura do token

```bash
php artisan jwt:secret
```

#### 6. Iniciar o servidor e sobe o projeto
    
```bash
php artisan serve
```

Link para acessar aplicação no browser: http://127.0.0.1:8000

### Cron Schedule (Linux)
Para as tarefas agendadas, é necessário adicionar o cron, pelo comando:
```bash
crontab -e
```

- E adicionar a linha:

```bash
*/30 * * * * cd /pasta/para/projeto && php artisan schedule:run >> /dev/null 2>&1
```

### Documentação API - Swagger

Acessível no link: http://127.0.0.1:8000/api/documentation

### Usuário para teste (autenticação)

**E-mail:** admin@backend.com.br<br>
**Password:** #Admin10

### Linha de raciocínio no desenvolvimento

#### Configuração ambiente
Meu primeiro passo foi definir qual banco de dados e optei pelo MySQL, pela minha experência maior diretamente com o framework Laravel. Após definido, realizei a configuração do driver e as informações no arquivo .env para acesso ao banco.

#### Design Pattern
Para o Design Pattern da aplicação utilizarei três camadas (Three-Tier Architecture), separando em controller, service e repository, deixando a aplicação mais modular e abstraindo a comunicação com o banco de dados das demais lógicas, onde o controller recebe a requisição, realiza as validações dos campos, em seguida é enviado ao service onde serão implementadas validações, funções de comunicação e regras de negócio (se necessário), por fim chegando ao repository para persistência dos dados, usando também injeção de depêndencia, trabalho com essa arquitetura há alguns anos e acho muito eficiente, por isso a minha escolha.

#### Produto
Após analise dos dados (Open Food Facts) irei criar a model do produto, definindo o schema com a migration com base no <a href="https://github.com/daniel-guimaraess/test-truckpag/blob/master/products.json">products.json</a> fornecido, utilizando os campos dele. Uma coisa importante foi enquanto eu analisava, percebi alguns campos faltantes em alguns produtos, que foram determinantes para o uso do nullable no campo específico.

#### Importação (Cron)
Seguindo o desafio, agora vou criar uma model chamada ImportData para gerenciar as importações, nela irei armazenar informações relevantes, para facilitar a validação e importação dos dados mais atualizados, como por exemplo salvar qual o último número do arquivo que foi importado, para posteriormente ir direto para o próximo.

#### ImportData
Agora irei criar as camada de service e repository para a importação, onde terá a lógica para que tudo aconteça corretamente, eu adicionei os campos last_file_number e status nesta model para controle de toda importação, que funcionará da seguinte forma:

**1)** Será disparado uma vez ao dia, primeiro sera checado se existe algum registro, senão significa que será a primeira vez.<br>
**2)** Se não existir ele irá buscar o primeiro arquivo (número 1).<br>
**3)** Se existir irei pegar o último registro, e o conteúdo do campo last_file_number, e consultar API da Open Food Fact no próximo arquivo (ex: número 2).<br>
**4)** Realizar a importação de 100 registros, adicionando ao banco de dados pela camada do repository.<br>
**5)** Se tudo for sucesso, registro essa importação, armazenando o last_file_number, atualizando o status para success, e com isso também teremos o created_at, para termos o controle de data e hora que foi realizada aquela importação, que será muito útil para o endpoint de checar API.

**Ps:** O campo status será bastante útil, pois de 1h em 1h, o Cron irá checar se a última importação foi realizada com sucesso, porque se por algum motivo isso não ocorrer, por diversas situações, como até mesmo indisponibilidade no serviço da Open Food Facts, não é interessante ter que esperar até o próximo dia para realizar a importação. Então basicamente terei 2 comandos via Cron, um para checar a ultima importação, se tiver sido sucesso ele não faz nada, caso ao contrario ele tenta novamente a última importação (sempre validando com a data de hoje e a data da última importação), e o outro comando é o padrão todo dia as 02h00 da manhã, pois é um horario com menor pico de acessos.

#### Camada de serviço ImportData:

Encontrei alguns problemas ao tentar descompactar o arquivo .gz devido ao tamanho muito grande e o processo ocorrer direto em memória, então resolvi iterar diretamente no .gz (precisei consultar a <a href="https://www.php.net/manual/pt_BR/function.gzopen.php">documentação</a> do PHP), pegando os 100 primeiros, e parando o processo, ao invés de descompacta-lo por completo. Outros problemas foram o tamanho do conteudo que vinha nos campos url, categories e ingredients_text, resolvido alterando na migration para longText, e por fim alguns campos "code" estavam vindo com uma aspas a mais, onde fiz uma validação para limpar essa aspas caso ela venha junto. 

Efetuei a implementação do service e repository do ImportData e também criei os dois comandos (se encontra em routes/console.php na nova versão do laravel) que serão executados via Cron, para validar os dados. Ao receber as informações está sendo armazenado 100 produtos da base de dados, com status inicial draft (rascunho).

####  Produto API (controller, service e repository)
Neste momento irei iniciar o desenvolvimento das API's, iniciando pelas rotas, e em seguida o controller para validar os campos da requisição.

Desenvolvi as camadas, sendo elas o controller, service e repository para os produtos, ja adiconando as validações necessárias para uso da API. Implementei também autenticação com JWT, algo que ja utilizo há bastante tempo com a biblioteca <a href="https://jwt-auth.readthedocs.io/en/develop/laravel-installation/">tymon/jwt-auth</a>, com ela desenvolvi um middleware para proteção das rotas, onde será necessário efetuar o login para consumir as API's, por padrão agora as rotas utilizam o prefixo /api, incluindo login e logout, explicarei o passo a passo no final.

Pensando sobre os status dos produtos, e como ao importar estou salvando como rascunho, achei válido desenvolver um endpoint para publicação.

Para o endpoint de checar a API, eu criei ele público, sem a necessidade de autenticação, a ideia foi retornar a conexão de leitura e escrita com uma string dizendo se esta ok, após acesso usando o <a href="https://stackoverflow.com/questions/42241934/how-can-i-make-a-database-connection-in-laravel">método DB::connection()->getPdo()</a>, para a informação do último Cron, utilizo a data de criação do último ImportData, pois o Cron foi efetuado naquele momento, para o tempo online, utilizei shell_script para consultar tempo ligado do servidor e para memória utilizo a função nativas do PHP como <a href="https://www.php.net/manual/en/function.memory-get-usage.php">memory_get_usage</a>.

Para monitorar importação dos dados, criei um log personalizado chamado import_data, dentro de storage/logs, onde será detalhado todas as vezes que o Cron for executado, para um sistema de alertas, tive a idéia de utilizar o Telegram para receber as mensagens, ja que o mesmo tem a API gratuita, utilizando um bot, pois recentemente utilizei este método em um projeto pessoal de monitoramento com inteligencia artificial, sendo assim, optei por desenvolver uma camada de serviço para o Telegram, com uma configuração bem simples via API, para alertar tanto sobre uma nova importação, quanto uma possível falha.

#### Camada serviço de Alerta
Desenvolvi uma camada de serviço com a função de enviar o alerta para o Telegram, consumindo a API gratuita, podendo ser utilizada em qualquer lugar da aplicação.

#### Telegram
Para criar utilizar o Telegram, precisaremos somente de duas informações o chat_id e o bot_token.

**bot_token** é acessivel assim que você cria o bot.<br>
**chat_id** (do canal ou conversa) é possível acessar após adicionar o bot dentro do canal.

Tutorial: https://painel.inouweb.com.br/knowledge-base/article/criar-um-bot-no-telegram-e-obter-seu-chat-id

Com essas informações em mãos, é só configurar no arquivo .env as variáveis:

CHAT_ID_TELEGRAM
BOT_TOKEN_TELEGRAM

**PS**: Irei deixar um vídeo de demonstração dos alertas chegando em tempo real no repositório.

#### Rotas desenvolvidas

**GET** - /api/checkapi -> Retorna um overview da API<br>
**GET** - /api/products -> Retorna todos produtos<br>
**GET** - /api/products/{code} -> Retorna um produto especifico a partir do code<br>
**POST** - /api/products/{code}/publish -> Publica um produto, alterando status para "published"<br>
**PUT** - /api/products/{code} -> Atualiza um produto especifico a partir do code<br>
**DELETE** - /api/products/{code} -> Remove um produto especifico a partir do code, alterando status para "trash"

#### Documentação API

Documentação API Swagger desenvolvida e acessivel via /api/documentation.

#### Testes unitários
Implementação de testes com PHPUnit, para os endpoints:

**GET** - /api/products<br>
**GET** - /api/products/{code}<br>
**PUT** - /api/products/{code}