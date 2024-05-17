<?php

declare(strict_type=1);

namespace App\Services;

use Framework\Database;

class TransactionService
{
    public function __construct(private Database $db)
    {
    }

    public function create(array $formData)
    {
        $formattedDate = "{$formData['trans_date']} 00:00:00";

        $this->db->query(
            "insert into table transactions (user_id, descrip, amount, trans_date) values (:user_id, :descrip, :amount, :trans_date",
            [
                "user_id" => $_SESSION["user"],
                "descrip" => $formData["descrip"],
                "amount" => $formData["amount"],
                "trans_date" => $formattedDate
            ]
        );
    }

    public function getUserTransactions(int $length, int $offset)
    {
        $searchTerm = addcslashes($_GET["s"] ?? "", "%_");
        $params =  [
            "user_id" => $_SESSION["user"],
            "descrip" => "%{$searchTerm}%"
        ];

        $transactions = $this->db->query(
            "select *, DATE_FORMAT(trans_date, '%Y-%m-%d) as formatted_date 
            from transactions 
            where user_id = :user_id
            and descrip like :descrip
            limit {$length} offset {$offset}",
            $params
        )->findAll();

        $transactions = array_map(function (array $transaction) {
            $transaction["receipts"] = $this->db->query(
                "select * from receipts where transaction_id = :transaction_id",
                [
                    "transaction_id" => $transaction["id"]
                ]
            )->findAll();

            return $transaction;
        }, $transactions);

        $transactionCount = $this->db->query(
            "select count(*) from transactions where user_id = :user_id
            and descrip like :descrip",
            $params
        )->count();

        return [$transactions, $transactionCount];
    }

    public function getUserTransaction(string $id)
    {
        return $this->db->query(
            "select *, DATE_FORMAT(trans_date, '%Y-%m-%d') as formatted_date from transactions where id = :id and user_id = :user_id",
            [
                "id" => $id,
                "user_id" => $_SESSION["user"]
            ]
        )->find();
    }

    public function update(array $formData, int $id)
    {
        $formattedDate = "{$formData['trans_data']} 00:00:00";

        $this->db->query(
            "update transactions set descrip = :descrip, amount = :amount, trans_date = :trans_date
            where id = :id and user_id = :user_id",
            [
                "descrip" => $formData["descrip"],
                "amount" => $formData["amount"],
                "trans_date" => $formattedDate,
                "id" => $id,
                "user_id" => $_SESSION["user"]
            ]
        );
    }

    public function delete(int $id)
    {
        $this->db->query(
            "delete from transactions where id = :id and user_id = :user_id",
            [
                "id" => $id,
                "user_id" => $_SESSION["user"]
            ]
        );
    }
}
