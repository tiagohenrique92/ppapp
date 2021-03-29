<?php
namespace PPApp\Models;

use Illuminate\Database\Eloquent\Model;
use PPApp\Models\WalletModel;

class UserModel extends Model
{
    const USER_TYPE_PERSON = 1;
    const USER_TYPE_BUSINESS = 2;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $table = "users";
    protected $primaryKey = "id";

    protected $fillable = array(
        "uuid",
        "name",
        "email",
        "password",
        "type",
    );

    public function wallet()
    {
        return $this->hasOne(WalletModel::class, "id_user");
    }
}
