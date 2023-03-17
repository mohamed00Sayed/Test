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
    protected $DB_USERNAME;
    protected $DB_PASSWORD;
    protected $DB_URL;

    public function __construct()
    {
        $this->DB_USERNAME = $_ENV['DB_USERNAME'];
        $this->DB_PASSWORD = $_ENV['DB_PASSWORD'];
        $this->DB_URL = $_ENV['DB_URL'];
    }

    public function getAll(): mixed
    {
        $res = [];
        try {
            $conn = new PDO($this->DB_URL, $this->DB_USERNAME, $this->DB_PASSWORD);
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
            $conn = new PDO($this->DB_URL, $this->DB_USERNAME, $this->DB_PASSWORD);
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
            $conn = new PDO($this->DB_URL, $this->DB_USERNAME, $this->DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare($this->SAVE_PRODUCT_QUERY);
            /*defer all bindings to subclasses*/
            static::bindValues($stmt, $product);

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
            $conn = new PDO($this->DB_URL, $this->DB_USERNAME, $this->DB_PASSWORD);
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
    abstract protected function createResult(array $arr): array;
    /*a template method, binds the different value, and could be used to bind more values if needed*/
    abstract protected function bindValues(PDOStatement &$stmt, &$product): void;
}
