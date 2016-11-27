<?php

namespace Hab\Core;

use Hab\Database\DatabaseQueries;

/**
 * Class TokenManager
 * @package Hab\Core
 *
 * @version 0.1
 * @author Claudio Santoro
 */
class TokenManager
{
    private $usedToken = null;

    /**
     * Get Token Manager Instance
     *
     * @return TokenManager
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
     * Get the Last Used Token
     *
     * @return string
     */
    public function getToken()
    {
        if ($this->usedToken == null) {
            $this->usedToken = DatabaseQueries::getLastToken();
        }

        return $this->usedToken;
    }

    /**
     * Create Token based in an User
     *
     * @param bool $forceCreation Froce Creation
     * @return null|string
     */
    public function createToken($forceCreation = true)
    {
        if (!$this->checkToken() || $forceCreation) {
            $this->usedToken = DatabaseQueries::createToken();
        }

        return $this->usedToken;
    }

    /**
     * Check the Existence of the Token
     *
     * @return bool
     */
    public function checkToken()
    {
        return DatabaseQueries::checkTokenExistence();
    }
}
