<?php
namespace PPApp\Repositories;

use PPApp\Exceptions\User\UserNotFoundException;
use PPApp\Exceptions\User\UserWalletNotFoundException;
use PPApp\Models\UserModel;

class UserRepository implements RepositoryInterface
{
    /**
    * @var UserModel
    */
    protected $userModel;
    
    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }
    
    public function getUserById(int $id): array
    {
        $user = $this->userModel::where('id', $id)->first();
        return (null !== $user) ? $user->toArray() : [];
    }
    
    public function getUserByUuid(string $uuid): array
    {
        $user = $this->userModel::where('uuid', $uuid)->first();
        return (null !== $user) ? $user->toArray() : [];
    }

    public function getWalletByUserUuid(UserModel $userModel): array
    {
        $user = $this->userModel::where('uuid', $uuid)->first();
        if (empty($user)) {
            throw new UserNotFoundException();
        }

        die('<pre>' . __FILE__ . '[' . __LINE__ . ']' . PHP_EOL . var_dump($user) . '</pre>');

        $wallet = $user->wallet()->first();
        if (empty($wallet)) {
            throw new UserWalletNotFoundException();
        }
    }
}