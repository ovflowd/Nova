<?php

namespace Hab\Database;

use Hab\Core\HabEngine;
use Hab\Core\HabUtils;
use stdClass;

/**
 * Class DatabaseQueries
 * @package Hab\Database
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class DatabaseQueries
{
    /**
     * Update the Token of a specific User based in an old Token
     *
     * @param string $oldToken
     * @return string
     */
    public static function updateToken($oldToken = '')
    {
        // Generates a random Token
        $tokenHash = HabUtils::TokenCrypto();

        // Get Engine Tables Section
        $engine = HabEngine::getInstance()->getEngineSettings()->tables;

        // Update Token Dynamically, only if the last token exists.
        DatabaseManager::getInstance()->query("UPDATE {$engine->tokenTable} SET {$engine->tokenColumn} = '{$tokenHash}'" .
            " WHERE {$engine->tokenColumn} = :oldToken", [':oldToken' => $oldToken]);

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
        // Get Engine Tables
        $engine = HabEngine::getInstance()->getEngineSettings()->tables;

        // Get User Data according from a Token. If User Data doesn't exists, will return Empty Object
        $returnedData = DatabaseManager::getInstance()->fetch("SELECT {$engine->usersColumns->id}, {$engine->usersColumns->name}, {$engine->usersColumns->email}, {$engine->usersColumns->email}" .
            " FROM {$engine->usersTable} WHERE {$engine->tokenColumn} = :usedToken LIMIT 1", [':usedToken' => $usedToken]);

        return (Object)$returnedData;
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
        // Get Engine Tables
        $engine = HabEngine::getInstance()->getEngineSettings()->tables;

        // Check if Token is valid dynamically.
        return DatabaseManager::getInstance()->rowCount("SELECT {$engine->tokenColumn} FROM {$engine->tokenTable}" .
            " WHERE {$engine->tokenColumn} = :tokenValue LIMIT 1", [':tokenValue' => $tokenHash]) > 0;
    }

    /**
     * Create a Token based on a Logged User in the Browser
     *
     * And returns the Generated Token Hash
     *
     * @return string
     */
    public static function createToken()
    {
        // Generate the Token
        $tokenHash = HabUtils::TokenCrypto();

        // Get the Engine Tables
        $engine = HabEngine::getInstance()->getEngineSettings()->tables;

        // Create a New token Based in the User logged in the CMS.
        // Used for the First Token in the Communication
        DatabaseManager::getInstance()->query("UPDATE {$engine->tokenTable} SET {$engine->tokenColumn} = '{$tokenHash}'" .
            " WHERE {$engine->tokenCriteria} = {$engine->tokenCriteriaValue}");

        return $tokenHash;
    }

    /**
     * Get Server Status Data
     * (Online User Count and if Server is Online)
     *
     * @return object
     */
    public static function getHotelStatus()
    {
        // Get Engine Tables
        $engine = HabEngine::getInstance()->getEngineSettings()->tables;

        // Get Server Status Data according from a Token.
        $returnedData = DatabaseManager::getInstance()->fetch("SELECT {$engine->serverColumns->online}, {$engine->serverColumns->onlineCount}" .
            " FROM {$engine->serverTable} LIMIT 1");

        return (Object)$returnedData;
    }
}
