<?php

declare(strict_types=1);

namespace Moham\Test\Repository;

use PDOStatement;
use Moham\Test\Main\Book;

class BookRepository extends Repository
{

    public function __construct()
    {
        parent::__construct();
        $this->SAVE_PRODUCT_QUERY = 'INSERT INTO BOOK(sku, name, price, weight) VALUES(?, ?, ?, ?)';
        $this->DELETE_PRODUCT_QUERY = 'DELETE FROM BOOK WHERE sku = ?';
        $this->SELECT_ALL_QUERY = 'SELECT * FROM BOOK';
        $this->RECORD_EXISTS_QUERY = 'SELECT CASE WHEN EXISTS (SELECT 1 FROM BOOK WHERE sku = ?) THEN 1 ELSE 0 END';
    }

    protected function createResult(array $arr): array
    {
        $x = 0;
        $result = array();
        foreach ($arr as $row) {
            $book = new Book($row['sku'], $row['name'], $row['price'], $row['weight']);
            $result[$x++] = $book;
        }

        return $result;
    }

    protected function bindValues(PDOStatement &$stmt, &$book): void
    {
        $stmt->bindValue(1, $book->getSku());
        $stmt->bindValue(2, $book->getName());
        $stmt->bindValue(3, $book->getPrice());
        $stmt->bindValue(4, $book->getWeight());
    }
}
