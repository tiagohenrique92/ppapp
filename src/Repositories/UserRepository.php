<?php
namespace PPApp\Repositories;

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
}
