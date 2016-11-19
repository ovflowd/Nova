<?php

/**
 *       __          ___      ___
 * |__| /  ` |    | |__  |\ |  |
 * |  | \__, |___ | |___ | \|  |
 *
 * @version 0.1
 * @author Claudio Santoro
 */

## Build HabClient PHAR File
## ATTENTION: ONLY FOR DEVELOPERS

ini_set('phar.readonly', 0);

define('PACKAGES_DIR', dirname(__FILE__) . '/packages/');

define('SRC_DIR', PACKAGES_DIR . 'src/');

define('BUILD_DIR', PACKAGES_DIR . 'build/');

// Create Phar File
$phar = new Phar(BUILD_DIR . 'HabClient.phar', 0, 'HabClient.phar');

// Add All Files from Source Directory
$phar->buildFromDirectory(SRC_DIR);

// Compile Phar Filer
$phar->setStub($phar->createDefaultStub('bootstrap.php', 'index.php'));

// Copy the Built file
copy(BUILD_DIR . 'HabClient.phar', dirname(__FILE__) . '/HabClient.phar');
