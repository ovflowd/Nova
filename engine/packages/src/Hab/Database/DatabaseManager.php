<?php

namespace Hab\Database;

use Hab\Core\HabUtils;
use PDO;
use PDOStatement;
use stdClass;

/**
 * Class DatabaseManager
 * @package Hab\Database
 *
 * @version 0.1
 * @author Claudio Santoro
 */
class DatabaseManager
{
    /**
     * Database Handler Instance
     *
     * @var DatabaseHandler
     */
    private $databaseHandler;

    /**
     * Database Credentials
     *
     * @var stdClass
     */
    private $databaseCredentials = null;

    /**
     * Get Database Manager Instance
     *
     * @return DatabaseManager
     */
    public static function getInstance()
    {
        static $instance = null;

        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Prepares, Executes a MySQL Query
     * returning a single row if exists.
     * Uses PHP's PDO Engine
     *
     * @param string $queryString MySQL Query
     * @param array $preparedStatements Array with Prepared Statements
     * @return mixed If successful a single object if not an error
     */
    public function fetch($queryString, array $preparedStatements = array())
    {
        return $this->query($queryString, $preparedStatements)->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Prepares and Executes a MySQL Query
     * through PHP's PDO Engine
     *
     * @param string $queryString MySQL Query
     * @param array $preparedStatements Array with Prepared Statements
     * @return PDOStatement Executed Statement
     */
    public function query($queryString, array $preparedStatements = array())
    {
        $statement = $this->getHandler()->getConnection()->prepare($queryString);
        $statement->execute($preparedStatements);

        return $statement;
    }

    /**
     * Get Database Handler
     *
     * @return DatabaseHandler
     */
    public function getHandler()
    {
        if (null === $this->databaseHandler) {

            HabUtils::habDebug('[Nova][Database] Instantiating Database Session...', 'blue');

            $this->databaseHandler = new DatabaseHandler($this->databaseCredentials);
            $this->databaseHandler->connect();

            HabUtils::habDebug('[Nova][Database] Database Connected. Waiting for Queries.', 'blue');
        }

        return $this->databaseHandler;
    }

    /**
     * Get the Last Inserted Id
     * By the last Resource Inserted in the Database
     *
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->getHandler()->getConnection()->lastInsertId();
    }

    /**
     * Prepares, Executes a MySQL Query
     * returning a set of rows if exists.
     * Uses PHP's PDO Engine
     *
     * @param string $queryString MySQL Query
     * @param array $preparedStatements Array with Prepared Statements
     * @return mixed|array If successful an object array if not an error
     */
    public function fetchAll($queryString, array $preparedStatements = array())
    {
        return $this->query($queryString, $preparedStatements)->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Prepares, Executes a MySQL Query
     * returning the row Count
     * Uses PHP's PDO Engine
     *
     * @param string $queryString MySQL Query
     * @param array $preparedStatements Array with Prepared Statements
     * @return mixed|int If successful a number if not an error
     */
    public function rowCount($queryString, array $preparedStatements = array())
    {
        return $this->query($queryString, $preparedStatements)->rowCount();
    }

    /**
     * Set the Database Credentials
     *
     * @param stdClass $databaseCredentials
     */
    public function setCredentials($databaseCredentials)
    {
        HabUtils::habDebug('[Nova][Database] Configuring Database...', 'blue');

        $this->databaseCredentials = $databaseCredentials;
    }
}
