<?php
namespace PPApp\Services;

use PPApp\Dto\UserDto;
use PPApp\Exceptions\User\UserNotFoundException;
use PPApp\Repositories\UserRepository;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * arrayToUserDto
     *
     * @param array $user
     * @return UserDto
     */
    private function arrayToUserDto(array $user): UserDto
    {
        $uuid = $user["uuid"] ?? null;
        $name = $user["name"] ?? null;
        $email = $user["email"] ?? null;
        $password = $user["password"] ?? null;
        $type = $user["type"] ?? null;

        $userDto = new UserDto($uuid, $name, $email, $password, $type);
        return $userDto;
    }

    /**
     * getUserById
     *
     * @param integer $id
     * @return UserDto
     */
    public function getUserById(int $id): UserDto
    {
        $user = $this->userRepository->getUserById($id);
        if (empty($user)) {
            throw new UserNotFoundException();
        }
        return $this->arrayToUserDto($user);
    }

    /**
     * getUserByUuidAsArray
     *
     * @param string $uuid
     * @return array
     * @throws UserNotFoundException
     */
    private function getUserByUuidAsArray(string $uuid): array
    {
        $user = $this->userRepository->getUserByUuid($uuid);
        if (empty($user)) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    /**
     * getUserByUuid
     *
     * @param string $uuid
     * @return UserDto
     */
    public function getUserByUuid(string $uuid): UserDto
    {
        $user = $this->getUserByUuidAsArray($uuid);
        return $this->arrayToUserDto($user);
    }

    /**
     * getUserIdByUuid
     *
     * @param string $uuid
     * @return integer
     */
    public function getUserIdByUuid(string $uuid): int
    {
        $user = $this->getUserByUuidAsArray($uuid);
        return $user["id"];
    }

    /**
     * getUserNameByUuid
     *
     * @param string $uuid
     * @return string
     */
    public function getUserNameByUuid(string $uuid): string
    {
        $user = $this->getUserByUuidAsArray($uuid);
        return $user["name"];
    }
}
