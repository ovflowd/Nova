<?php

namespace Hab\Database;

use Hab\Core\HabMessage;
use PDO;
use PDOException;
use stdClass;

/**
 * Class DatabaseHandler
 * @package Hab\Database
 *
 * @version 0.1
 * @author Claudio Santoro
 */
class DatabaseHandler
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
     * @var stdClass
     */
    private $databaseSettings = null;

    /**
     * Creates a Database Handler Instance.
     *
     * @param stdClass $databaseSettings
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
                    "mysql:host={$this->databaseSettings->host};" .
                    "port={$this->databaseSettings->port};" .
                    "dbname={$this->databaseSettings->name}",
                    $this->databaseSettings->user, $this->databaseSettings->password
                );
            } catch (PDOException $e) {
                die ((new HabMessage(500, "Failed to connect to the Database, using the provided details!"))->renderJson());
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
