<?php

declare(strict_types=1);

namespace Moham\Test\Repository;

use PDO;
use PDOException;
use PDOStatement;

abstract class Repository
{
    protected $SAVE_PRODUCT_QUERY;
    protected $DELETE_PRODUCT_QUERY;
    protected $SELECT_ALL_QUERY;
    protected $RECORD_EXISTS_QUERY;

    public function getAll(): mixed
    {
        $res = [];
        try {
            $conn = new PDO(getenv("DB_URL"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare($this->SELECT_ALL_QUERY);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            $res = $stmt->fetchAll();

            return static::createResult($res);
        } catch (PDOException $ex) {
            return $ex->getCode();
        } finally {
            /*close db connection*/
            $conn = null;
        }
    }

    public function exists(string $id): mixed
    {
        try {
            $conn = new PDO(getenv("DB_URL"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare($this->RECORD_EXISTS_QUERY);
            $stmt->bindValue(1, $id);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            return $ex->getCode();
        } finally {
            /*close the connection*/
            $conn = null;
        }
    }

    public function save($product): mixed
    {
        try {
            $conn = new PDO(getenv("DB_URL"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare($this->SAVE_PRODUCT_QUERY);
            $stmt->bindValue(1, $product->getSku());
            $stmt->bindValue(2, $product->getName());
            $stmt->bindValue(3, $product->getPrice());
            static::bindValue(4, $stmt, $product);

            return $stmt->execute();
        } catch (PDOException $ex) {
            return $ex->getCode();
        } finally {
            /*close db connection*/
            $conn = null;
        }
    }

    public function deleteAll(array $arr): mixed
    {
        try {
            $conn = new PDO(getenv("DB_URL"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare($this->DELETE_PRODUCT_QUERY);

            foreach ($arr as $id) {
                $stmt->bindValue(1, $id);
                $stmt->execute();
            }
        } catch (PDOException $ex) {
            return $ex->getCode();
        } finally {
            /*close db connection*/
            $conn = null;
        }

        return true;
    }
    /*a template method, knows how to create the result of a specific product type*/
    protected abstract function createResult(array $arr): array;
    /*a template method, binds the different value, and could be used to bind more values if needed*/
    protected abstract function bindValue(int $pos, PDOStatement &$stmt, &$product): void;
}
