<?php
namespace PPApp\Services;

use PPApp\Dto\WalletDto;
use PPApp\Exceptions\User\UserWalletNotFoundException;
use PPApp\Repositories\WalletRepository;

class WalletService
{
    /**
     * @var WalletRepository
     */
    private $walletRepository;

    public function __construct(WalletRepository $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    /**
     * arrayToWalletDto
     *
     * @param array $wallet
     * @return WalletDto
     */
    private function arrayToWalletDto(array $wallet): WalletDto
    {
        $uuid = $wallet["uuid"] ?? null;
        $balance = $wallet["balance"] ?? null;

        $walletDto = new WalletDto($uuid, $balance);
        return $walletDto;
    }

    public function credit(int $idWallet, float $amount): bool
    {
        return $this->walletRepository->credit($idWallet, $amount);
    }

    public function debit(int $idWallet, float $amount): bool
    {
        return $this->walletRepository->debit($idWallet, $amount);
    }

    public function getWalletByUserId(int $userId): WalletDto
    {
        $wallet = $this->walletRepository->getWalletByUserId($userId);
        if (empty($wallet)) {
            throw new UserWalletNotFoundException();
        }
        return $this->arrayToWalletDto($wallet);
    }

    public function getWalletByUserUuid(string $uuid): WalletDto
    {
        $wallet = $this->walletRepository->getWalletByUserUuid($uuid);
        if (empty($wallet)) {
            throw new UserWalletNotFoundException();
        }
        return $this->arrayToWalletDto($wallet);
    }

    public function getWalletIdByUserId(int $userId): int
    {
        $wallet = $this->walletRepository->getWalletByUserId($userId);
        if (empty($wallet)) {
            throw new UserWalletNotFoundException();
        }
        return $wallet["id"];
    }
}
