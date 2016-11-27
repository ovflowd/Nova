<?php

namespace Hab\Core;

use Hab\Database\DatabaseManager;

/**
 * Class HabEngine
 * @package Hab\Core
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class HabEngine
{
    /**
     * Nova API Settings
     *
     * @var object
     */
    private $apiSettings = null;

    /**
     * Nova Engine Settings
     *
     * @var object
     */
    private $engineSettings = null;

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
     * Compatible Nova App Versions
     *
     * @var array
     */
    private $appVersions = [];

    /**
     * Get the Current Instance of the Engine Class
     *
     * Singleton Method
     *
     * @return HabEngine
     */
    public static function getInstance()
    {
        static $instance = null;

        if (null === $instance) {
            /** @var HabEngine $instance */
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Prepares the Nova Engine
     *
     * @param string $apiSettings
     * @param string $engineSettings
     */
    public function prepare($apiSettings, $engineSettings)
    {
        HabUtils::habDebug('[Nova] Starting Nova v' . ENGINE_VERSION, 'cyan');

        // Decodes into Objects
        $this->apiSettings = json_decode($apiSettings);
        $this->engineSettings = json_decode($engineSettings);
        $this->appVersions = json_decode(COMPATIBLE_APP);

        HabUtils::habDebug('[Nova] Decoding Objects...', 'blue');

        // Set Database Credentials
        DatabaseManager::getInstance()->setCredentials($this->engineSettings->database);

        HabUtils::habDebug('[Nova] Ready. ', 'green');
    }

    /**
     * Create Response for the Requested Page
     *
     * @return string
     */
    public function createResponse()
    {
        return (new HabTemplate($this->routeEngine()))->getResponse();
    }

    /**
     * Returns the Requested Page
     *
     * @return string
     */
    public function routeEngine()
    {
        HabUtils::habDebug("[Nova][Router][{$_SERVER['REMOTE_ADDR']}] Received Request!", 'red');

        // Check if Query String exists. If exists, continue.
        if (array_key_exists('QUERY_STRING', $_SERVER)) {

            // Parse Query String into Array by Key=Value
            parse_str($_SERVER['QUERY_STRING'], $this->queryString);

            // Check if Token Entry Exists
            if (array_key_exists('Token', $this->queryString)) {
                $this->tokenAuth = $this->queryString['Token'];
            }

            // Check if Page Entry exists
            if (array_key_exists('Page', $this->queryString)) {
                HabUtils::habDebug("[Nova][Router][{$_SERVER['REMOTE_ADDR']}] Selected Module: {$this->queryString['Page']}", 'purple');

                return $this->queryString['Page'];
            }
        }

        // Work around of multiple Token generation
        if ($_SERVER['REQUEST_URI'] == '/favicon.ico') {
            return 'NotFound';
        }

        return 'Home';
    }

    /**
     * Get Engine Settings
     *
     * @return object
     */
    public function getEngineSettings()
    {
        return $this->engineSettings;
    }

    /**
     * Get the API Settings
     *
     * @return object
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

    /**
     * Set the Token Auth
     *
     * @param string $tokenAuth
     */
    public function setTokenAuth($tokenAuth)
    {
        $this->tokenAuth = $tokenAuth;
    }

    /**
     * Get Compatible App Versions with this Engine
     *
     * @return array
     */
    public function getAppVersion()
    {
        return $this->appVersions;
    }
}
