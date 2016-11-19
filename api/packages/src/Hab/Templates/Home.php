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
        $this->setResponse(HabTemplate::includeVendor('Home.php'));
    }
}
