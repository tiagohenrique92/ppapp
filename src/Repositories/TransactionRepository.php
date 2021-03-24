<?php
namespace PPApp\Repositories;

use PPApp\Models\TransactionModel;

class TransactionRepository implements RepositoryInterface
{
    /**
    * @var TransactionModel
    */
    protected $transactionModel;
    
    public function __construct(TransactionModel $transactionModel)
    {
        $this->transactionModel = $transactionModel;
    }

    /**
     * create
     *
     * @param array $data
     * @return boolean
     */
    public function create(array $data): bool
    {
        $transactionModel = $this->transactionModel;
        $transactionModel->uuid = $data['uuid'];
        $transactionModel->datetime_created = $data['datetime_created'];
        $transactionModel->datetime_authorized = $data['datetime_authorized'];
        $transactionModel->amount = $data['amount'];
        $transactionModel->amount = $data['id_payer'];
        $transactionModel->id_payee = $data['id_payee'];
        $transactionModel->status = $data['status'];
        return $$transactionModel->save();
    }
}
