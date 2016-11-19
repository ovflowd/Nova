<?php

namespace Hab\Database;

use PDO;
use PDOException;

/**
 * Class Handler
 * @package Hab\Database
 *
 * @version 0.1
 * @author Claudio Santoro
 */
class Handler
{
    /**
     * Database Connection Instance
     *
     * @var PDO
     */
    private $databaseInstance;

    /**
     * Database Settings Block
     *
     * @var array
     */
    private $databaseSettings;

    /**
     * Creates a Database Handler Instance.
     *
     * @param array $databaseSettings
     */
    public function __construct($databaseSettings)
    {
        $this->databaseSettings = $databaseSettings;
        $this->databaseInstance = null;
    }

    /**
     * Connects to the MySQL Database
     */
    public function connect()
    {
        if ($this->databaseInstance === null) {
            try {
                $this->databaseInstance = new PDO(
                    "mysql:host={$this->databaseSettings['host']};" .
                    "port={$this->databaseSettings['port']};" .
                    "dbname={$this->databaseSettings['name']}",
                    $this->databaseSettings['user'], $this->databaseSettings['password']
                );
            } catch (PDOException $e) {
                die (createError('Fatal!', "The HabClient can't connect to the provided Database. Please check Connection String."));
            }
        }
    }

    /**
     * Get's the Connection Instance from MySQL
     *
     * @warning Instance can be Null
     *
     * @return PDO|null
     */
    public function getConnection()
    {
        return $this->databaseInstance;
    }

    /**
     * Closes MySQL Connection Instance
     */
    public function closeConnection()
    {
        $this->databaseInstance = null;
    }
}
