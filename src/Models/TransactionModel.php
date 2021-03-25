<?php
namespace PPApp\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionModel extends Model
{
    const CREATED_AT = "created_at";
    const UPDATED_AT = null;

    protected $table = "transactions";
    protected $primaryKey = "id";
}
