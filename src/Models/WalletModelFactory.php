<?php
namespace PPApp\Models;

use PPApp\Models\WalletModel;
use PPApp\Utils\Uuid;

class WalletModelFactory
{
    /**
     * make
     *
     * @param integer $id_user
     * @param float $balance
     * @param string $uuid
     * @return WalletModel
     */
    public function make(int $id_user, float $balance, string $uuid = null): WalletModel
    {
        $attributes = array(
            "uuid" => $uuid ?? Uuid::create(),
            "id_user" => $id_user,
            "balance" => $balance,
        );
        $walletModel = new WalletModel($attributes);
        return $walletModel;
    }
}
