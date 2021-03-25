<?php
namespace PPApp\Models;

use Illuminate\Database\Eloquent\Model;
use PPApp\Models\WalletModel;

class UserModel extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = "users";
    protected $primaryKey = "id";

    public function wallet()
    {
        return $this->hasOne(WalletModel::class, "id_user");
    }
}
