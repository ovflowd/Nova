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

// Initialize Class Register
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    require_once('' . $class . '.php');
});
