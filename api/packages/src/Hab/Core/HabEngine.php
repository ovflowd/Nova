<?php

namespace Hab\Core;

use Hab\Database\DatabaseManager;
use stdClass;

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
     * HabClient API Settings
     *
     * @var stdClass
     */
    private $apiSettings = null;

    /**
     * HabClient Engine Settings
     *
     * @var stdClass
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
     * Get the Current Instance of the Engine Class
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
     * Prepares the HabClient Engine
     *
     * @param string $apiSettings
     * @param string $engineSettings
     */
    public function prepare($apiSettings, $engineSettings)
    {
        $this->apiSettings = json_decode($apiSettings);
        $this->engineSettings = json_decode($engineSettings);

        DatabaseManager::getInstance()->setCredentials($this->engineSettings->database);
    }

    /**
     * Create Response for the Requested Page
     *
     * @return string
     */
    public function createResponse()
    {
        $pageContainer = new HabTemplate($this->routeEngine());

        return $pageContainer->getResponse();
    }

    /**
     * Returns the Requested Page
     *
     * @return string
     */
    public function routeEngine()
    {
        if (!array_key_exists('QUERY_STRING', $_SERVER)) {
            return 'Home';
        }

        parse_str($_SERVER['QUERY_STRING'], $this->queryString);

        if (array_key_exists('Token', $this->queryString)) {
            $this->tokenAuth = $this->queryString['Token'];
        }

        if (array_key_exists('Page', $this->queryString)) {
            return $this->queryString['Page'];
        }

        return 'Home';
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
