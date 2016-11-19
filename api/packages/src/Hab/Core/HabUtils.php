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
     * Update the Token of a specific User based in an old Token
     *
     * @param string $oldToken
     * @return string
     */
    public static function updateToken($oldToken = '')
    {
        // Generates a random Token
        $tokenHash = self::TokenCrypto();

        // Get Engine Tables Section
        $engine = HabEngine::getInstance()->getEngineSettings()->tables;

        // Update Token Dynamically, only if the last token exists.
        DatabaseManager::getInstance()->query("UPDATE {$engine->tokenTable} SET {$engine->tokenColumn} = '{$tokenHash}'" .
            " WHERE {$engine->tokenColumn} = :oldToken", [':oldToken' => $oldToken]);

        return $tokenHash;
    }

    /**
     * Generate Token
     *
     * Using OpenSSL Random Pseudo Bytes and bin2hex.
     *
     * @Attention PHP 5.3 or Higher!
     *
     * @param bool $isSSO If is SSO
     * @return string
     */
    public static function TokenCrypto($isSSO = false)
    {
        return ($isSSO ? 'SSO-' : 'HabClient-') . bin2hex(openssl_random_pseudo_bytes(16));
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
     * Generate and Store Token Hash
     *
     * Usable for Client Logon and External Client Auth
     *
     * @return string
     */
    public static function generateExternal()
    {
        // Generate the Token
        $tokenHash = self::createToken();

        // Get the Hotel Base Url
        $hotelUrl = HabEngine::getInstance()->getApiSettings()->hotel->base;

        // Return HHotel Wrapper with Token and Hotel URI
        return "hhotel://{$hotelUrl}?token={$tokenHash}";
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
        $tokenHash = self::TokenCrypto();

        // Get the Engine Tables
        $engine = HabEngine::getInstance()->getEngineSettings()->tables;

        // Create a New token Based in the User logged in the CMS.
        // Used for the First Token in the Communication
        DatabaseManager::getInstance()->query("UPDATE {$engine->tokenTable} SET {$engine->tokenColumn} = '{$tokenHash}'" .
            " WHERE {$engine->tokenCriteria} = {$engine->tokenCriteriaValue}");

        return $tokenHash;
    }

    /**
     * Retrieve Remote Content and Return the Data
     *
     * @Attention: The intent of this was to use with GitHub RAW Content
     *
     * @param string $remoteURI
     * @return mixed
     */
    public static function getRemoteContent($remoteURI)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $remoteURI);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }
}
