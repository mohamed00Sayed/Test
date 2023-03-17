<?php

declare(strict_types=1);

namespace Moham\Test\Repository;

use PDOStatement;
use Moham\Test\Main\Dimension;
use Moham\Test\Main\Furniture;

class FurnitureRepository extends Repository
{
    public function __construct()
    {
        parent::__construct();
        $this->SAVE_PRODUCT_QUERY = 'INSERT INTO FURNITURE(sku, name, price, dimensions) VALUES(?, ?, ?, ?)';
        $this->DELETE_PRODUCT_QUERY = 'DELETE FROM FURNITURE WHERE sku = ?';
        $this->SELECT_ALL_QUERY = 'SELECT * FROM FURNITURE';
        $this->RECORD_EXISTS_QUERY =
            'SELECT CASE WHEN EXISTS (SELECT 1 FROM FURNITURE WHERE sku = ?) THEN 1 ELSE 0 END';
    }

    protected function createResult(array $arr): array
    {
        $x = 0;
        $result = array();
        foreach ($arr as $row) {
            $dimensions = json_decode($row['dimensions']);
            $furniture = new Furniture(
                $row['sku'],
                $row['name'],
                (float)$row['price'],
                new Dimension((int)$dimensions->length, (int)$dimensions->width, (int)$dimensions->height)
            );
            $result[$x++] = $furniture;
        }

        return $result;
    }

    protected function bindValues(PDOStatement &$stmt, &$furniture): void
    {
        $stmt->bindValue(1, $furniture->getSku());
        $stmt->bindValue(2, $furniture->getName());
        $stmt->bindValue(3, $furniture->getPrice());
        $stmt->bindValue(4, json_encode($furniture->getDimensions()));
    }
}
