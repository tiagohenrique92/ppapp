<?php
namespace PPApp\Models;

use PPApp\Models\WalletModel;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = "users";
    protected $primaryKey = "id";

    public function wallet()
    {
        return $this->hasOne(WalletModel::class, "id_user");
    }
}