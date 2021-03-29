<?php
namespace PPApp\Models;

use Faker\Factory;
use PPApp\Models\UserModel;
use PPApp\Utils\Uuid;

class UserModelFactory
{
    protected function make(string $uuid = null, string $name = null, string $email = null, string $password = null): UserModel
    {
        $fake = Factory::create();
        $attributes = array(
            "uuid" => $uuid ?? Uuid::create(),
            "name" => $name ?? $fake->name(),
            "email" => $email ?? $fake->email(),
            "password" => $password ?? $fake->password(),
        );
        $userModel = new UserModel($attributes);
        return $userModel;
    }

    /**
     * makeBusinessUser
     *
     * @return UserModel
     */
    public function makeBusinessUser(string $uuid = null, string $name = null, string $email = null, string $password = null): UserModel
    {
        $userModel = $this->make($uuid, $name, $email, $password);
        $userModel->type = UserModel::USER_TYPE_BUSINESS;
        return $userModel;
    }

    /**
     * makePersonUser
     *
     * @return UserModel
     */
    public function makePersonUser(string $uuid = null, string $name = null, string $email = null, string $password = null)
    {
        $userModel = $this->make($uuid, $name, $email, $password);
        $userModel->type = UserModel::USER_TYPE_PERSON;
        return $userModel;
    }
}
