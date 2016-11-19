<?php

/**
 *       __          ___      ___
 * |__| /  ` |    | |__  |\ |  |
 * |  | \__, |___ | |___ | \|  |
 *
 * @version 0.1
 * @author Claudio Santoro
 */

## HabClient Bootstrap starts here.
## Include HabClient Files

// Define HaEngine Version
define('ENGINE_VERSION', '0110');

// Compatible Versions of HabClient Java App with the current Engine Version
define('COMPATIBLE_JAVA', json_encode([
    '0111',
    '0110',
    '0100'
]));

// Disable Error Reporting if Required
if (defined('ENGINE_ERROR_REPORTING') && ENGINE_ERROR_REPORTING == false) {
    error_reporting(0);
}

// Initialize Class Register
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    // @ is workaroung for class_exists
    @include_once('' . $class . '.php');
});

// If the ENGINE SETTINGS or API SETTINGS aren't configured. We have a problem.
if (!defined('ENGINE_SETTINGS') || !defined('API_SETTINGS') || !defined('FORCE_UPDATE_ENGINE')) {
    die((new \Hab\Core\HabMessage(500, "The current configuration of HabClient engine is invalid, please check the manuals."))->renderJson());
}

## End of HabClient Bootstrap