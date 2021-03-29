<?php
namespace PPApp\Models;

use PPApp\Models\UserModel;
use Illuminate\Database\Eloquent\Model;

class WalletModel extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;
    
    protected $table = "wallets";
    protected $primaryKey = "id";

    protected $fillable = array(
        "uuid",
        "id_user",
        "balance",
    );

    public function user()
    {
        return $this->belongsTo(UserModel::class, "id_user");
    }
}
