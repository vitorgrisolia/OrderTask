Sistema Lista de Tarefas - PHP
Este é um sistema web desenvolvido para o cadastro e gerenciamento de tarefas. A aplicação permite ao usuário adicionar, editar, excluir, reordenar e visualizar tarefas. Os dados são mantidos em um banco de dados MySQL, e as funcionalidades incluem um CRUD completo (Criar, Ler, Atualizar, Excluir).

Funcionalidades
1. Lista de Tarefas
A página principal do sistema exibe todas as tarefas cadastradas na tabela tarefas. Os registros são exibidos com os seguintes campos:

Nome da Tarefa: Nome da tarefa a ser realizada.
Custo (R$): O custo associado à tarefa.
Data Limite: A data limite para concluir a tarefa.
As tarefas são ordenadas pelo campo Ordem de apresentação, garantindo que a lista seja exibida de acordo com a ordem numérica definida.

Destaque para Tarefas com Custo >= R$1.000,00: As tarefas que possuem custo maior ou igual a R$1.000,00 serão destacadas com uma cor de fundo amarela (ou outra cor de sua preferência).
Botões de ação: Para cada tarefa, há dois botões (ícones) para editar ou excluir a tarefa.
Botão "Incluir": Ao final da lista, há um botão para adicionar novas tarefas.
2. Excluir
A função Excluir permite que o usuário remova uma tarefa do banco de dados. Ao clicar no botão Excluir, o sistema solicitará uma confirmação para a exclusão (Sim/Não). Após a confirmação, a tarefa será apagada.

3. Editar
A função Editar permite ao usuário modificar os seguintes campos de uma tarefa:

Nome da Tarefa
Custo
Data Limite
Antes de permitir a alteração, o sistema verifica se o novo nome da tarefa já existe no banco de dados. Caso o nome já esteja em uso, a alteração não será realizada.

Existem duas formas de edição:

Edição direta na tela principal (Lista de Tarefas): Os campos serão habilitados diretamente na lista para que o usuário possa editá-los.
Edição em uma nova tela (popup): Ao clicar em editar, uma janela de edição será aberta, permitindo que o usuário altere os campos.
4. Incluir
A função Incluir permite que o usuário cadastre novas tarefas. Para incluir uma tarefa, o usuário deve preencher os campos:

Nome da Tarefa
Custo
Data Limite
O campo Ordem de apresentação é gerado automaticamente pelo sistema. O novo registro será inserido ao final da lista e não será permitido que duas tarefas tenham o mesmo nome.

5. Reordenação das Tarefas
A função de reordenação permite que o usuário altere a ordem de apresentação das tarefas. Existem duas formas de implementar a reordenação:

Arraste e solte (drag-and-drop): O usuário pode arrastar uma tarefa para cima ou para baixo, soltando-a na posição desejada.
Botões de "Subir" e "Descer": Para cada tarefa, haverá dois botões que permitem alterar sua posição na lista (subir ou descer). A primeira tarefa não poderá ser movida para cima e a última não poderá ser movida para baixo.
Banco de Dados
O sistema utiliza um banco de dados MySQL com uma tabela chamada tarefas. A tabela possui os seguintes campos:

id (INT AUTO_INCREMENT, chave primária): Identificador único da tarefa.
nome (VARCHAR(255), único): Nome da tarefa.
custo (DECIMAL(10,2)): Custo da tarefa (em R$).
data_limite (DATE): Data limite para a tarefa ser concluída.
ordem (INT, único): Ordem de apresentação das tarefas na interface.
Estrutura da Tabela
sql
Copiar código
CREATE TABLE IF NOT EXISTS tarefas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE,
    custo DECIMAL(10,2) NOT NULL,
    data_limite DATE NOT NULL,
    ordem INT NOT NULL UNIQUE
);
Tecnologias Utilizadas
Frontend: HTML, CSS, JavaScript (com possibilidade de frameworks como Bootstrap ou jQuery para facilitar a interface).
Backend: PHP (com uso de MVC ou estrutura simples).
Banco de Dados: MySQL.
Biblioteca para Reordenação: Para a funcionalidade de drag-and-drop, pode ser utilizada a biblioteca SortableJS.
Como Rodar o Sistema
1. Clone o Repositório
Clone este repositório para sua máquina local:

bash
Copiar código
git clone https://github.com/seu-usuario/sistema-lista-de-tarefas.git
2. Instale as Dependências
Para este projeto, não há dependências externas necessárias para o backend, pois ele utiliza PHP puro. Mas, se for utilizar algum framework ou biblioteca no frontend (ex: jQuery, Bootstrap), instale-os manualmente ou através de gerenciadores como npm ou composer.

3. Configuração do Banco de Dados
Crie um banco de dados no MySQL e execute o script SQL para criar a tabela tarefas.
sql
Copiar código
CREATE DATABASE lista_tarefas;
USE lista_tarefas;
Importe o script de criação da tabela ou execute o código SQL:
sql
Copiar código
CREATE TABLE IF NOT EXISTS tarefas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE,
    custo DECIMAL(10,2) NOT NULL,
    data_limite DATE NOT NULL,
    ordem INT NOT NULL UNIQUE
);
Atualize as configurações de conexão com o banco de dados no arquivo config.php ou onde for necessário no código.
4. Configuração do Servidor PHP
Se você não tiver um servidor local, pode usar o XAMPP ou o WAMP para rodar o PHP e MySQL em sua máquina.

Inicie o servidor PHP e abra o projeto em seu navegador.

bash
Copiar código
php -S localhost:8000
5. Acesse a Aplicação
Abra seu navegador e acesse a URL fornecida pelo servidor. Por exemplo:

arduino
Copiar código
http://localhost:8000
Contribuições
Contribuições são bem-vindas! Se você encontrar algum erro ou desejar melhorar o sistema, fique à vontade para criar um pull request ou abrir uma issue.
