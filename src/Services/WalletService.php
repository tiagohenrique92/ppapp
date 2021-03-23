<?php

namespace PPApp\Services;

use PPApp\Vos\WalletVo;
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

    public function getWalletByUserId(int $id): WalletVo
    {
        $wallet = $this->walletRepository->getWalletByUserId($id);
        $walletVo = new WalletVo($wallet);
        return $walletVo;
    }
}
