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

Modifique o arquivo `ppapp/app/config.php` com as informações de conexão com o banco de dados: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_PORT`.

> Certifique-se de que o banco de dados informado já esteja criado, caso contrário o próximo passo irá falhar.

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

Crie um virtual host apontando para o diretório `public`.
```
# http
<VirtualHost myppapp.test:80>
    DocumentRoot "/home/user/ppapp/public"
    ServerName myppapp.test
    ErrorLog "/home/user/ppapp/logs/ppapp.test-error_log"
    CustomLog "/home/user/ppapp/logs/ppapp.test-access_log" common

    <Directory "/home/user/ppapp/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# https
<VirtualHost myppapp.test:443>
    DocumentRoot "/home/user/ppapp/public"
    ServerName myppapp.test
    ErrorLog "/home/user/ppapp/logs/ppapp.test-error_log"
    CustomLog "/home/user/ppapp/logs/ppapp.test-access_log" common

    SSLEngine on
    SSLCertificateFile "/opt/lampp/etc/ssl.crt/myppapp/server.crt"
    SSLCertificateKeyFile "/opt/lampp/etc/ssl.key/myppapp/server.key"

    <Directory "/home/user/ppapp/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Crie uma entrada para o virtual host no arquivo `/etc/hosts`.
```
127.0.0.1 myppapp.test
```

## API

Segue abaixo a lista de métodos suportados pela API.

### /api/v1/payment/transfer
#### POST
Realiza uma transferência entre dois usuários

**Request**
```json
{
    "payerUuid": "1eb8f484-5467-6396-afbf-02423599a7ad",
    "payeeUuid": "1eb8f484-5467-6396-afbf-02423599a7ad",
    "amount": 50
}
```

| Propriedade | Tipo          | Obrigatório | Descrição          |
|-------------|---------------|-------------|--------------------|
| payerUuid   | string(36)    | Sim         | Uuid do pagador.   |
| payeeUuid   | string(36)    | Sim         | Uuid do recebedor. |
| amount      | decimal(16,2) | Sim         | Valor transferido. |

**Response**
```json
{
    "uuid": "1eb8fc74-fe42-61fc-ae05-024261ea6440"
}
```

| Propriedade | Tipo       | Descrição          |
|-------------|------------|--------------------|
| uuid        | string(36) | Uuid da transação. |

## Lista de erros

| Code | Title                                        | Message                                      | Description                                                                                                                                                                      |
|------|----------------------------------------------|----------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 1    | Unexpected error                             | Unexpected error                             | An unexpected error has occurred. Please contact support.                                                                                                                        |
| 2    | Param not found                              | Param not found                              | Request parameter not found in the payload. The "details.name" property will return the name of the parameter.                                                                   |
| 3    | Payer not found                              | Payer not found                              | The transaction payer was not found. The "details.payerUuid" property will return the payer uuid.                                                                                |
| 4    | Payee not found                              | Payee not found                              | The transaction payee was not found. The "details.payeeUuid" property will return the payee uuid.                                                                                |
| 5    | The payer is a business user                 | The payer can't be a business user           | The payer can't be a business user. The "details.payerUuid" property will return the payer uuid.                                                                                 |
| 6    | The payer and the payee are the same user    | The payer must be different from the payee   | The payer must be different from the payee. The "details.payerUuid" property will return the payer uuid and the "details.payeeUuid" property will return the payee uuid.         |
| 7    | Payee's wallet not found                     | Payee's wallet not found                     | Payee's wallet not found. The "details.payeeUuid" property will return the payee uuid.                                                                                           |
| 8    | Payer's wallet not found                     | Payer's wallet not found                     | Payer's wallet not found. The "details.payerUuid" property will return the payer uuid.                                                                                           |
| 9    | The payer's wallet has no sufficient balance | The payer's wallet has no sufficient balance | The payer's wallet has no sufficient balance.                                                                                                                                    |
| 10   | Invalid payment amount                       | Invalid payment amount                       | Invalid payment amount. The "details.amount" property will return the payment amount. Make sure that the payment amount is greater than zero and that it is formatted correctly. |
| 11   | User not found                               | User not found                               | The user was not found. The "details.userUuid" property will return the user uuid.                                                                                               |
| 12   | User's wallet not found                      | User's wallet not found                      | User's wallet not found. The "details.userUuid" property will return the user uuid.                                                                                              |
| 13   | Failed to authorize the transaction          | Failed to authorize the transaction          | Failed to authorize the transaction. Please contact support.                                                                                                                     |
| 14   | Failed to sent the transaction notfication   | Failed to sent the transaction notfication   | Failed to sent the transaction notfication. Please contact support.                                                                                                              |
