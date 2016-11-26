<?php

namespace Hab\Core;

/**
 * Class HabUpdater
 * @package Hab\Core
 *
 * @version 0.1
 * @author Claudio Santoro
 */
class HabUpdater
{
    /**
     * Defines the new Version of Nova
     *
     * @var string
     */
    private static $newVersion = ENGINE_VERSION;

    /**
     * Return a DIV with Rendered if Updates exists if not Returns nothing
     *
     * @param bool $jsonMessage
     * @return string
     */
    public static function renderUpdates($jsonMessage = false)
    {
        // Check the Updates and get the new Version
        $existsUpdates = self::checkUpdates();
        $newVersion = self::$newVersion;

        if ($existsUpdates) {
            HabUtils::habDebug("[Nova][Updater] A new Update for Nova was found. It's recommended to Update the Engine.", 'yellow');
        }

        // Check if needs to be jSON or not
        // Carefully!! If the HabMessage constructor goes out from the condition, the Content-Type goes to Application/JSON
        $content = $jsonMessage ? (new HabMessage(300, "A new Version of Nova is Available! To continue using the API, please download latest version ($newVersion)"))->renderJson()
            : "<div style='padding: 8px 10px;border: 1px solid #d64242;margin: 5px 0 5px;border-radius: 3px;background: #d64242;color: white;'><b>Hoy!!</b> A new version of Nova it's available. <b>({$newVersion})!</b></div>";

        return $existsUpdates ? $content : '';
    }

    /**
     * Check for new Versions in the Repository
     *
     * @Attention if the cURL goes wrong, the newVersion continues being the actual version.
     * @param bool $ignoreUpdates If need ignore updates
     * @return bool If New Version (true) if not (false)
     */
    public static function checkUpdates($ignoreUpdates = false)
    {
        // New Updates Being Ignored if Force Update Engine Disabled
        if (FORCE_UPDATE_ENGINE == false && $ignoreUpdates) {
            return false;
        }

        // If already checked Updates, Ignore the Download Again
        if (self::$newVersion != ENGINE_VERSION) {
            return true;
        }

        // Get RAW content
        $newVersion = str_replace("\n", '', HabUtils::getRemoteContent('https://raw.githubusercontent.com/sant0ro/Nova/master/VERSION'));

        // Check if content is valid VERSION content.
        if (!empty($newVersion) && is_numeric($newVersion)) {
            self::$newVersion = $newVersion;
        }

        return self::$newVersion != ENGINE_VERSION;
    }

    /**
     * Check if the Nova App version is compatible with the current Engine version
     *
     * @param integer $checkVersion
     * @return bool Return (true) if the Version it's compatible (false) if not.
     */
    public static function checkEngineApp($checkVersion)
    {
        $compatibleVersions = HabEngine::getInstance()->getAppVersion();

        return (in_array($checkVersion, $compatibleVersions));
    }
}
