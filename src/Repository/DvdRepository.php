<?php

declare(strict_types=1);

namespace Moham\Test\Repository;

use PDOStatement;
use Moham\Test\Main\Dvd;

class DvdRepository extends Repository
{
    public function __construct()
    {
        parent::__construct();
        $this->SAVE_PRODUCT_QUERY = 'INSERT INTO DVD(sku, name, price, size) VALUES(?, ?, ?, ?)';
        $this->DELETE_PRODUCT_QUERY = 'DELETE FROM DVD WHERE sku = ?';
        $this->SELECT_ALL_QUERY = 'SELECT * FROM DVD';
        $this->RECORD_EXISTS_QUERY = 'SELECT CASE WHEN EXISTS (SELECT 1 FROM DVD WHERE sku = ?) THEN 1 ELSE 0 END';
    }

    protected function createResult(array $arr): array
    {
        $x = 0;
        $result = array();
        foreach ($arr as $row) {
            $book = new Dvd($row['sku'], $row['name'], (float)$row['price'], (int)$row['size']);
            $result[$x++] = $book;
        }

        return $result;
    }

    protected function bindValues(PDOStatement &$stmt, &$dvd): void
    {
        $stmt->bindValue(1, $dvd->getSku());
        $stmt->bindValue(2, $dvd->getName());
        $stmt->bindValue(3, $dvd->getPrice());
        $stmt->bindValue(4, $dvd->getSize());
    }
}
