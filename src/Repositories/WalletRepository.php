<?php
namespace PPApp\Repositories;

use PPApp\Models\WalletModel;
use PPApp\Repositories\RepositoryInterface;

class WalletRepository implements RepositoryInterface
{
    /**
     * @var WalletModel
     */
    protected $walletModel;

    public function __construct(WalletModel $walletModel)
    {
        $this->walletModel = $walletModel;
    }

    public function getWalletByUserId(int $userId): array
    {
        $wallet = $this->walletModel::where('id_user', $userId)->first();
        return (null !== $wallet) ? $wallet->toArray() : [];
    }

    public function getWalletByUserUuid(string $uuid): array
    {
        $wallet = $this->walletModel::where('uuid', $uuid)->first();
        return (null !== $wallet) ? $wallet->toArray() : [];
    }
}
