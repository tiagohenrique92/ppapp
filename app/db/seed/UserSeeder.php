<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    public function run()
    {
        $tbUsers = $this->table("users");
        $tbBusinessUsers = $this->table("business_users");
        $tbPersonUsers = $this->table("person_users");
        $tbWallets = $this->table("wallets");
        $users = json_decode(file_get_contents(__DIR__ . "/../userSeed.json"), true);

        $this->getAdapter()->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $tbPersonUsers->truncate();
        $tbBusinessUsers->truncate();
        $tbUsers->truncate();
        $tbWallets->truncate();
        $this->getAdapter()->execute("SET FOREIGN_KEY_CHECKS = 1;");

        foreach ($users as $user) {
            $tbUsers->insert(array(
                "id" => $user['id'],
                "uuid" => $user['uuid'],
                "name" => $user['name'],
                "email" => $user['email'],
                "password" => $user['password'],
                "type" => $user['type'],
            ));

            switch ((int) $user['type']) {
                case \PPApp\Services\UserService::USER_TYPE_PERSON:
                    $tbPersonUsers->insert(array(
                        "id_user" => $user['id'],
                        "cpf" => $user['cpf'],
                    ));
                    break;
                case \PPApp\Services\UserService::USER_TYPE_BUSINESS:
                    $tbBusinessUsers->insert(array(
                        "id_user" => $user['id'],
                        "cnpj" => $user['cnpj'],
                    ));
                    break;
            }

            $tbWallets->insert($user['wallet']);
        }

        $tbUsers->saveData();
        $tbPersonUsers->saveData();
        $tbBusinessUsers->saveData();
        $tbWallets->saveData();
    }
}
