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

    public function getWalletByUserId(int $id): array
    {
        $wallet = $this->walletModel::where('id', $id)->first();
        return (null !== $wallet) ? $wallet->toArray() : [];
    }
}
