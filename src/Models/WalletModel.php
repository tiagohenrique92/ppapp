<?php
namespace PPApp\Models;

use PPApp\Models\UserModel;
use Illuminate\Database\Eloquent\Model;

class WalletModel extends Model
{
    protected $table = "wallets";
    protected $primaryKey = "id";

    public function user()
    {
        return $this->belongsTo(UserModel::class, "id_user");
    }
}
