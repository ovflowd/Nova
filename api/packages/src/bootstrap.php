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

// Define Error Model
define('ERROR_MODEL', '
<html>
    <head>
        <title>HClient : Failed</title>
    </head>
    <body>
        <h1>{header}</h1>
         <p>{message}<br></p>
    </body>
</html>');

/**
 * Creates an Error Message
 *
 * @param string $title
 * @param string $message
 * @return string
 */
function createError($title = 'Error', $message = 'No Details provided.')
{
    return str_replace('{header}', $title, str_replace('{message}', $message, ERROR_MODEL));
}

// Initialize Class Register
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    require_once('' . $class . '.php');
});
