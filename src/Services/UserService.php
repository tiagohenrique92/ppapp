<?php

namespace PPApp\Services;

use PPApp\Vos\UserVo;
use PPApp\Repositories\UserRepository;

class UserService 
{
    const USER_TYPE_PERSON = 1;
    const USER_TYPE_BUSINESS = 2;
    
    /**
    * @var UserRepository
    */
    private $userRepository;
    
    public function __construct(UserRepository $userRepository) 
    {
        $this->userRepository = $userRepository;
    }

    public function getUserById(int $id): UserVo
    {
        $user = $this->userRepository->getUserById($id);
        $userVo = new UserVo($user);
        return $userVo;
    }
    
    public function getUserByUuid(string $uuid): UserVo
    {
        $user = $this->userRepository->getUserByUuid($uuid);
        $userVo = new UserVo($user);
        return $userVo;
    }
}