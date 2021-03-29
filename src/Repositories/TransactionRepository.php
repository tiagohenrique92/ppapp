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
        $transactionModel->amount = $data['amount'];
        $transactionModel->id_payer = $data['id_payer'];
        $transactionModel->id_payee = $data['id_payee'];
        return $transactionModel->save();
    }

    /**
     * getTransactionByUuid
     *
     * @param string $uuid
     * @return array
     */
    public function getTransactionByUuid(string $uuid): array
    {
        $transaction = $this->transactionModel::where('uuid', $uuid)->first();
        return (null !== $transaction) ? $transaction->toArray() : [];
    }
}
