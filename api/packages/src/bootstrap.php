<?php

/**
 *       __          ___      ___
 * |__| /  ` |    | |__  |\ |  |
 * |  | \__, |___ | |___ | \|  |
 *
 * @version 0.1
 * @author Claudio Santoro
 */

## Include HabClient Files

// Define HaEngine Version
define('ENGINE_VERSION', '0110');

// Compatible Versions of HabClient Java App with the current Engine Version
define('COMPATIBLE_JAVA', json_encode([
    '0111',
    '0110',
    '0100'
]));

// Initialize Class Register
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    require_once('' . $class . '.php');
});
