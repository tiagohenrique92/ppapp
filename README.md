# PPApp

## Pré-requisitos
Para este projeto é necessário:
1. PHP 7.2, ou superior
2. MySql 5.6, ou superior
3. [Composer](https://getcomposer.org/download/)

## Instalação
Após baixar o projeto, acesse o diretório principal `ppapp` e execute o composer para instalar as dependencias.

```bash
$ composer install
```

Execute o docker para subir o projeto.
```bash
$ docker-compose up
```

Modifique o arquivo `ppapp/app/config.php` com as informações de conexão com o banco de dados: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_PORT`.

> Certifique-se de que o banco de dados informado já esteja criado, caso contrário o próximo passo irá falhar.

Acesse o container ppapp:

```bash
$ docker exec -it ppapp sh
```

Na raiz do projeto (ppapp), execute o comando para criar as tabelas do banco de dados (migration).

```bash
$ php vendor/bin/phinx migrate -c app/phinx.php
```

Para popular as tabelas com dados para testes, execute o comando *seed* do *phinx*.

```bash
$ php vendor/bin/phinx seed:run -c app/phinx.php
```

> Caso queira modificar os dados a serem populados, altere o arquivo `ppapp/app/db/userSeed.json`.

> A propriedade `type` deve ser 1 para usuários comuns(cpf) e 2 para usuários empresariais(cnpj).

Crie uma entrada para o virtual host no seu arquivo de hosts.
```
127.0.0.1 myppapp.test
```

## API

No link abaixo você encontrará a documentação da API e no diretório `/ppapp/postman` você encontrará a *collection* do Postman.
> [https://documenter.getpostman.com/view/15054112/TzCL7njZ#ppapp](https://documenter.getpostman.com/view/15054112/TzCL7njZ#ppapp)

## Testes

Acesse o container ppapp e na raiz do projeto (ppapp), utilize o comando abaixo para executar os testes.
```bash
$ ./vendor/bin/phpunit --testdox --colors tests
```