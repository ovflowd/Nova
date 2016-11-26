<?php

/**
 *       __          ___      ___
 * |__| /  ` |    | |__  |\ |  |
 * |  | \__, |___ | |___ | \|  |
 *
 * @version 0.1
 * @author Claudio Santoro
 */

## Nova Bootstrap starts here.
## Include Nova Files

// Define NovaEngine Version

use Hab\Core\HabMessage;

define('ENGINE_VERSION', '012');

// Compatible Versions of HabClient App with the current Engine Version
define('COMPATIBLE_APP', json_encode([
    '011',
    '011',
    '010',
    '012'
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
    die((new HabMessage(500, "The current configuration of Nova engine is invalid, please check the manuals."))->renderJson());
}

## End of Nova Bootstrap
