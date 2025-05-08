Linha de raciocínio

Meu primeiro passo foi definir qual banco de dados e optei pelo MySQL, pela minha experência maior diretamente com o framework. Estou utilizando Laravel na versão 12.

Após definido, realizei a configuração do driver e as informações no arquivo .env para acesso ao banco.

Para o Design Pattern da aplicação utilizarei três camadas (Three-Tier Architecture), separando em controller, service e repository, deixando a aplicação mais modular e abstraindo a comunicação com o banco de dados das demais lógicas, onde o controller recebe a requisição, realiza as validações dos campos, em seguida é enviado a camada de serviço onde serão implementadas as regras de negócio, por fim chegando ao repository para persistência dos dados, usando também injeção de depêndencia, trabalho com essa arquitetura há alguns anos e acho muito eficiente, por isso a minha escolha.

Neste momento irei criar a model do produto, definindo o schema com a migration com base no products.json fornecido, utilizando os campos dele. Uma coisa importante foi enquanto eu analisava os dados nos jsons da Open Food Facts, alguns campos faltantes em alguns produtos, determinaram a adição do nullable no campo especifico.

Seguindo o desafio, agora vou criar uma model para gerenciar as importações, nela irei armazenar informações relevantes, para facilitar a validação e importação dos dados mais atualizados, como por exemplo salvar qual o ultimo arquivo de produtos (número) que foi importado, para na próxima importação, ir direto a partir dele.

Agora irei criar as camada de service e repository para a importação, onde terá a lógica para que tudo aconteça corretamente, eu adicionei os campos last_file e status nesta model para controle de toda importação, que funcionará da seguinte forma:

1) Será disparado uma vez ao dia, primeiro sera checado se existe algum registro, senão significa que será a primeira vez.
2) Se não existir ele irá buscar o primeiro arquivo (número 1).
3) Senão irei pegar o último registro, e o conteúdo do campo last_file e verificar com um regex qual arquivo foi o último, assim passando para o próximo (ex: número 2).
4) Realizar a importação dos 100 primeiros registros, adicionando ao banco de dados pela camada do repository.
5) Se tudo for sucesso, registro essa importação, armazenando o last_file, atualizando o status para success, e com isso também teremos o created_at, para termos o controle de data e hora que foi realizada aquela importação.

Ps: O campo status será bastante útil, pois de 1h em 1h, ele irá checar se a última importação foi realizada com sucesso, porque se por algum motivo isso não ocorrer, por diversos situações, como até mesmo indisponibilidade no serviço da Open Food Facts, não é interessante ter que esperar até o próximo dia para realizar a importação. Então basicamente terei 2 comandos, via Cron, um para checar a ultima importação, se tiver sido sucesso ele não faz nada, caso ao contrario ele tenta novamente a última importação (sempre validando com a data de hoje e a data da última importação), e o outro comando é o padrão todo dia as 02h00 da manhã, pois é um horario com menor pico de acessos.

Camada de serviço ImportData:

Encontrei alguns problemas ao tentar descompactar o arquivo .gz devido ao tamanho muito grande e o processo ocorrer direto em memória, então resolvi iterar diretamente no .gz (precisei consultar a documentação do PHP) pegando os 100 primeiros, e parando o processo, ao invés de descompacta-lo por completo. Outros problemas foram o tamanho do conteudo que vinha nos campos url, categories e ingredients_text, resolvido alterando na migration para longText, e por fim alguns campos "code" estavam vindo com uma aspas a mais, onde fiz uma validação para limpar essa aspas caso ela venha junto. 

Efetuei a implementação do service e repository do ImportData e também criei os dois comandos (se encontra em routes/console.php na nova versão do laravel) que serão executados via Cron, para validar os dados. Ao receber as informações está sendo armazenado 100 produtos da base de dados, com status inicial draft (rascunho).

Utilizei o comando cron abaixo, para ser executado a cada 30 minutos, assim o schedule run será executado e também os comandos.

*/30 * * * * cd /pasta/para/projeto && php artisan schedule:run >> /dev/null 2>&1

Neste momento irei iniciar o desenvolvimento da API, iniciando pelas rotas, e em seguida o controller para validar os campos da requisição.

Desenvolvi as camadas, sendo elas o controller, service e repository para os produtos, ja adiconando as validações necessárias para uso da API. Implementei também autenticação com JWT, algo que ja utilizo há bastante tempo com a biblioteca tymon/jwt-auth, com ela desenvolvi um middleware para proteção das rotas, onde será necessário efetuar o login para consumir as API's, por padrão agora as rotas utilizam o prefixo /api, com as rotas novas de login e logout, explicarei o passo a passo no final.

Rotas desenvolvidas

GET - /api/products -> Retorna todos produtos
GET - /api/products/{code} -> Retorna um produto especifico a partir do code
POST - /api/products/{code}/publish -> Publica um produto, alterando status para "published"
PUT - /api/products/{code} -> Atualiza um produto especifico a partir do code
DELETE - /api/products/{code} -> Remove um produto especifico a partir do code, alterando status para "trash"