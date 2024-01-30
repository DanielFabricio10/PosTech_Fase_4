# PosTech_Fase_4

Projeto PosTech - Instruções de Execução

Olá este é o projeto PosTech! Neste documento você encontrará as instruções necessárias para configurar e executar o projeto em seu ambiente local. Certifique-se de seguir as etapas abaixo para garantir uma execução bem-sucedida do projeto.

# Passo 1: Clonar o Repositório Realize o clone do repositório do projeto a partir do GitHub. Abra o seu terminal e execute o seguinte comando:
git clone https://github.com/DanielFabricio10/PosTech_Fase_4.git *
Isso criará uma cópia local de todos os arquivos e código do projeto em um diretório chamado "PosTech".


# Passo 2: Configuração do Ambiente Certifique-se de que o Docker está instalado em seu sistema. Caso ainda não esteja, siga as instruções de instalação no site oficial do Docker.


# Passo 3: Executar o Projeto No terminal, navegue até o diretório "PosTech" que você clonou anteriormente:

- cd PosTech\Fase-4 *
Agora, use o Docker Compose para iniciar o projeto. Execute o seguinte comando:

- docker-compose up -d *
Isso criará e executará os contêineres necessários para o projeto em segundo plano ("-d"). Aguarde até que o processo seja concluído.

# Passo 4: Realizar a importação da Collection Postman que se encontra junto a raiz do projeto no arquivo PosTech.postman_collection.json

Obs: A Collection já contas com as informações de autenticação e Url pré definidas.


# Acessando o Projeto Após a concluir todas as etapas anteriores, você pode acessar o projeto em um navegador da web usando o endereço apropriado. Geralmente, você pode acessá-lo em:
http://localhost:8080/app/ *
Encerrando o Projeto Assim que concluir o trabalho com o projeto, você pode parar a execução dos contêineres Docker. No terminal, no diretório "PosTech", execute o seguinte comando:
docker-compose down *
Isso encerrará os contêineres do projeto.

# Conclusão Agora você deverá estar com tudo pronto para executar o projeto em seu ambiente local. Lembre-se de consultar a documentação do projeto para obter mais detalhes sobre como usar os recursos específicos oferecidos pelo projeto.
