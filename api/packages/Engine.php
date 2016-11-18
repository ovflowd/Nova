<?php

/**
 * Class DatabaseHandler
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
                die (createError('Fatal!', "The HClient can't connect to the provided Database. Please check Connection String."));
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

/**
 * Class DatabaseManager
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
     * @var array
     */
    private $databaseCredentials;

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
            $this->databaseHandler = new DatabaseHandler($this->databaseCredentials);
            $this->databaseHandler->connect();
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
     * @param array $databaseCredentials
     */
    public function setCredentials($databaseCredentials)
    {
        $this->databaseCredentials = $databaseCredentials;
    }
}

/**
 * Class Engine
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class Engine
{
    /**
     * HClient API Settings
     *
     * @var array
     */
    private $apiSettings = [];

    /**
     * HClient Engine Settings
     *
     * @var array
     */
    private $engineSettings = [];

    /**
     * Requested URI Query String
     *
     * @var array
     */
    private $queryString = [];

    /**
     * Used Token in this Authentication
     *
     * @var string
     */
    private $tokenAuth = '';

    /**
     * Get the Current Instance of the Engine Class
     *
     * @return Engine
     */
    public static function getInstance()
    {
        static $instance = null;

        if (null === $instance) {
            /** @var Engine $instance */
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Prepares the HClient Engine
     *
     * @param string $apiSettings
     * @param string $engineSettings
     */
    public function prepare($apiSettings, $engineSettings)
    {
        $this->apiSettings = json_decode($apiSettings);
        $this->engineSettings = json_decode($engineSettings);

        DatabaseManager::getInstance()->setCredentials($this->engineSettings['database']);
    }

    /**
     * Returns the Requested Page
     *
     * @return string
     */
    public function routeEngine()
    {
        parse_str($_SERVER['QUERY_STRING'], $this->queryString);

        if (array_key_exists('Token', $this->queryString)) {
            $this->tokenAuth = $this->queryString['Token'];
        }

        if (array_key_exists('Page', $this->queryString)) {
            switch ($this->queryString['Page']) {
                case 'User':
                case 'Hotel':
                    return $this->queryString['Page'];
                default:
                    return 'NotFound';
            }
        }

        return 'Home';
    }

    /**
     * Create Response for the Requested Page
     *
     * @return string
     */
    public function createResponse()
    {
        $pageContainer = new Template($this->routeEngine());

        return $pageContainer->getResponse();
    }

    /**
     * Get Engine Settings
     *
     * @return string
     */
    public function getEngineSettings()
    {
        return $this->engineSettings;
    }

    /**
     * Get the API Settings
     *
     * @return string
     */
    public function getApiSettings()
    {
        return $this->apiSettings;
    }

    /**
     * Get the Query String
     *
     * @return array
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * Get Used Token in the Current Communication
     *
     * @return string
     */
    public function getTokenAuth()
    {
        return $this->tokenAuth;
    }
}

/**
 * Class Template
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class Template
{
    /**
     * Response from Template
     *
     * @var string
     */
    private $response = '';

    /**
     * Template constructor.
     *
     * @param string $pageName
     */
    public function __construct($pageName)
    {
        if (method_exists($this, $pageName)) {
            $this->response = $this->{$pageName}();

            return;
        }

        $this->response = createError('Not Found', "The Desired Template wasn't found on our Records");
    }

    /**
     * User Sub Routine
     *
     * @return string
     */
    public function User()
    {
        $queryString = Engine::getInstance()->getQueryString();

        if (array_key_exists('SubPage', $queryString)) {
            switch ($queryString['SubPage']) {
                case 'Login':
                    return $this->UserAuth(Engine::getInstance()->getTokenAuth());
                default:
                    return $this->NotFound();
            }
        }

        return $this->NotFound();
    }

    /**
     * Hotel Sub Routine
     *
     * @return string
     */
    public function Hotel()
    {
        $queryString = Engine::getInstance()->getQueryString();

        if (array_key_exists('SubPage', $queryString)) {
            switch ($queryString['SubPage']) {
                case 'Client':
                    return $this->ClientData(Engine::getInstance()->getTokenAuth());
                default:
                    return $this->NotFound();
            }
        }

        return $this->NotFound();
    }

    /**
     * Not Found Message
     *
     * @return string
     */
    public function NotFound()
    {
        return (new Message(404, "The Requested Uri wasn't found in this HClient."))->renderJson();
    }

    /**
     * User Auth Message
     *
     * @param string $oldToken
     * @return string
     */
    private function UserAuth($oldToken = '')
    {
        if (Utils::checkToken($oldToken)) {

            $user = Utils::getUserData($oldToken);

            $message = new Message(200, 'Authentication OK');
            $message->addField('User', $user);
            $message->addField('NewToken', Utils::updateToken($oldToken));

            return $message->renderJson();
        }

        return (new Message(403, "Your Token isn't valid! Authentication Failed to obtain User Data."))->renderJson();
    }

    /**
     * Obtain Hotel Client Settings
     *
     * @param string $oldToken
     * @return string
     */
    private function ClientData($oldToken)
    {
        if (Utils::checkToken($oldToken)) {

            $client = Engine::getInstance()->getApiSettings();

            $message = new Message(200, 'Authentication OK');
            $message->addField('Client', $client);
            $message->addField('NewToken', Utils::updateToken($oldToken));

            return $message->renderJson();
        }

        return (new Message(403, "Your Token isn't valid! Authentication Failed to obtain Hotel Data"))->renderJson();
    }

    /**
     * Home Page Controller
     *
     * @return string
     */
    public function Home()
    {
        $stringBuilder = '';

        $stringBuilder .= '<h1>Welcome to the HClient</h1>';

        $stringBuilder .= '<br>';

        $externalUri = Utils::generateExternal();

        $stringBuilder .= "<a href='{$externalUri}'>Enter in Client</a>";

        return $stringBuilder;
    }

    /**
     * Get Response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }
}

/**
 * Class Utils
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class Utils
{
    /**
     * Generate Token
     *
     * @return string
     */
    public static function TokenCrypto()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * Check if the Token is Valid
     *
     * If Is Return true;
     *
     * @param string $tokenHash
     * @return bool
     */
    public static function checkToken($tokenHash)
    {
        $engine = Engine::getInstance()->getEngineSettings()['tables'];

        return DatabaseManager::getInstance()->rowCount("SELECT {$engine['tokenColumn']} FROM {$engine['tokenTable']}" .
            " WHERE {$engine['tokenColumn']} = :tokenValue LIMIT 1", [':tokenValue' => $tokenHash]) > 0;
    }

    /**
     * Update the Token of a specific User based in an old Token
     *
     * @param string $oldToken
     * @return string
     */
    public static function updateToken($oldToken = '')
    {
        $tokenHash = self::TokenCrypto();

        $engine = Engine::getInstance()->getEngineSettings()['tables'];

        DatabaseManager::getInstance()->query("UPDATE {$engine['tokenTable']} SET {$engine['tokenColumn']} = '{$tokenHash}'" .
            " WHERE {$engine['tokenColumn']} = :oldToken", [':oldToken' => $oldToken]);

        return $tokenHash;
    }

    /**
     * Create a Token based on a Logged User in the Browser
     *
     * @return string
     */
    public static function createToken()
    {
        $tokenHash = self::TokenCrypto();

        $engine = Engine::getInstance()->getEngineSettings()['tables'];

        DatabaseManager::getInstance()->query("UPDATE {$engine['tokenTable']} SET {$engine['tokenColumn']} = '{$tokenHash}'" .
            " WHERE {$engine['tokenCriteria']} = {$engine['tokenCriteriaValue']}");

        return $tokenHash;
    }

    /**
     * Return User Data
     *
     * @param string $usedToken
     * @return object|stdClass
     */
    public static function getUserData($usedToken)
    {
        $engine = Engine::getInstance()->getEngineSettings()['tables'];

        if (!self::checkToken($usedToken)) {
            return new stdClass();
        }

        $returnedData = DatabaseManager::getInstance()->fetchAll("SELECT " .
            "{$engine['usersColumns']['id']}, {$engine['usersColumns']['name']}, {$engine['usersColumns']['email']}, {$engine['usersColumns']['look']}" .
            " FROM {$engine['usersTable']} WHERE {$engine['tokenColumn']} = :usedToken LIMIT 1", [':usedToken' => $usedToken]);

        return (Object)$returnedData;
    }

    /**
     * Generate and Store Token Hash
     *
     * Usable for Client Logon and External Client Auth
     *
     * @return string
     */
    public static function generateExternal()
    {
        $tokenHash = self::createToken();

        $api = Engine::getInstance()->getApiSettings()['hotel'];

        return "hhotel://{$api['base']}?token={$tokenHash}";
    }
}

/**
 * Class Message
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class Message
{
    /**
     * Message Code
     *
     * @var int
     */
    private $Code = 404;

    /**
     * Message Content
     *
     * @var string
     */
    private $Message = 'Not Found';

    /**
     * Message constructor.
     *
     * @param int $code
     * @param string $message
     */
    public function __construct($code, $message)
    {
        $this->Code = $code;
        $this->Message = $message;
    }

    /**
     * Add Custom Field to Message
     *
     * @param string $fieldName
     * @param mixed $fieldValue
     */
    public function addField($fieldName, $fieldValue)
    {
        $this->{$fieldName} = $fieldValue;
    }

    /**
     * Create the Message in jSON String
     *
     * @return string
     */
    public function renderJson()
    {
        header('Content-Type: application/json');

        return json_encode($this);
    }
}
