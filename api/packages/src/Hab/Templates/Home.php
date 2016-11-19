<?php

namespace Hab\Templates;

use Hab\Core\HabTemplate;

/**
 * Class Home
 * @package Hab\Templates
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class Home extends Base
{
    /**
     * Creates a new instance of the Home Page Template
     */
    public function __construct()
    {
        $stringBuilder = HabTemplate::includeVendor('Home.php');

        //$stringBuilder = '';

        //$stringBuilder .= '<h1>Welcome to the HabClient</h1>';

        //$stringBuilder .= '<br>';

        //$externalUri = HabUtils::generateExternal();

        //$stringBuilder .= "<a href='{$externalUri}'>Enter in Client</a>";

        $this->setResponse($stringBuilder);
    }
}
