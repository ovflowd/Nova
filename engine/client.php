<?php

/**
 *       __          ___      ___
 * |__| /  ` |    | |__  |\ |  |
 * |  | \__, |___ | |___ | \|  |
 *
 * @version 0.1
 * @author Claudio Santoro
 */

use Hab\Core\HabEngine;

// Define the Engine Error Reporting
// Recommended disabling it for Production Servers
define('ENGINE_ERROR_REPORTING', true);

// Define if Need Force Update with new Engine Versions
define('FORCE_UPDATE_ENGINE', false);

// Administrator Root Token (Development Only!!)
define('MASTER_TOKEN', 'Nova-48ac6b41574f59e58f74c00f8dffc5aa');

// Test Only (Remove for Production Usages)
$_SESSION['id'] = 1;

// This Settings Are Fundamental to the Engine Work.
define('ENGINE_SETTINGS', json_encode([
    'database' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'habbo',
        'password' => 'habbo',
        'name' => 'habbo'
    ],
    'tables' => [
        'usersTable' => 'users',
        'usersColumns' => [
            'id' => 'id',
            'name' => 'username',
            'email' => 'mail',
            'look' => 'look'
        ],
        'userCriteria' => 'id',
        'userCriteriaValue' => @$_SESSION['id'],
        'tokenTable' => 'users',
        'tokenColumn' => 'auth_ticket',
        'tokenCriteria' => 'id',
        'tokenCriteriaValue' => @$_SESSION['id'],
        'usedTokenColumn' => 'used_token',
        'serverTable' => 'server_status',
        'serverColumns' => [
            'online' => 'status',
            'onlineCount' => 'users_online'
        ]
    ]
]));

// Be carefully to set those settings correctly. If anything be wrong, the API wouldn't work correctly
define('API_SETTINGS', json_encode([
    'hotel' => [
        'name' => 'Habbo Hotel',
        'base' => 'http://localhost/',
        'logout' => 'http://localhost/logout.php'
    ],
    'emulator' => [
        'ip' => '127.0.0.1',
        'port' => 30000,
    ],
    'swf' => [
        'path' => 'http://localhost/resources/swf/',
        'gordon' => [
            'base' => 'gordon/PRODUCTION-201510201205-42435347/',
            'flash' => 'Habbo.swf'
        ],
        'gamedata' => [
            'variables' => 'gamedata/external_variables.txt',
            'texts' => 'gamedata/external_flash_texts.txt',
            'overrideVariables' => 'gamedata/external_override_variables.txt',
            'overrideTexts' => 'gamedata/external_override_flash_texts.txt',
            'furnidata' => 'gamedata/furnidata.xml',
            'productdata' => 'gamedata/productdata.json'
        ]
    ],
    'custom' => [
        'loading' => 'Loading Habbo Hotel...',
        'logo' => 'http://localhost/images/logo.gif',
        'small_logo' => 'http://localhost/images/nova.gif'
    ]
]));

## Start Engine Section

@require_once "phar://Nova.phar/bootstrap.php";

HabEngine::getInstance()->prepare(API_SETTINGS, ENGINE_SETTINGS);

echo HabEngine::getInstance()->createResponse();

## HClient ends here.
