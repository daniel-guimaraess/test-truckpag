Linha de raciocínio

Meu primeiro passo foi definir qual banco de dados e optei pelo MySQL, pela minha experência maior diretamente com o framework. Estou utilizando Laravel na versão 12.

Após definido, realizei a configuração do driver e as informações no arquivo .env para acesso ao banco.

Para o Design Pattern da aplicação utilizarei três camadas (Three-Tier Architecture), separando em controller, service e repository, deixando a aplicação mais modular e abstraindo a comunicação com o banco de dados das demais lógicas, onde o controller recebe a requisição, realiza as validações dos campos, em seguida é enviado a camada de serviço onde serão implementadas as regras de negócio, por fim chegando ao repository para persistência dos dados, trabalho com essa arquitetura há alguns anos e acho muito eficiente, por isso a minha escolha.

Neste momento irei criar a model do produto, definindo o schema com a migration com base no products.json fornecido, utilizando os campos dele. Uma coisa importante foi enquanto eu analisava os dados nos jsons da OpenFood, alguns campos faltantes em alguns produtos, determinaram a adição do nullable no campo especifico.