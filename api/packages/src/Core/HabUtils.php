<?php

namespace Hab\Core;

use Hab\Database\DatabaseManager;
use stdClass;

/**
 * Class Utils
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class HabUtils
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
        $engine = HabEngine::getInstance()->getEngineSettings()['tables'];

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

        $engine = HabEngine::getInstance()->getEngineSettings()['tables'];

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

        $engine = HabEngine::getInstance()->getEngineSettings()['tables'];

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
        $engine = HabEngine::getInstance()->getEngineSettings()['tables'];

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

        $api = HabEngine::getInstance()->getApiSettings()['hotel'];

        return "hhotel://{$api['base']}?token={$tokenHash}";
    }
}
