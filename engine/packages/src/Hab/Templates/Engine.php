<?php

namespace Hab\Templates;

use Hab\Core\HabEngine;
use Hab\Core\HabMessage;
use Hab\Core\HabUpdater;

/**
 * Class Version
 * @package Hab\Templates
 *
 * @version 0.1
 * @author Claudio Santoro
 */
class Engine extends Base
{
    /**
     * Creates a new Instance of The Engine Template
     * Ableing the System to choose one of it's sub Templates
     */
    public function __construct()
    {
        // Get URI Query String
        $queryString = HabEngine::getInstance()->getQueryString();

        // Choose SubPage
        if (array_key_exists('SubPage', $queryString)) {
            $this->setResponse($this->checkMethod($queryString['SubPage']) ? $this->{$queryString['SubPage']}() : $this->NotFound());
        }
    }

    /**
     * Check Engine Version vs Java App Version
     *
     * @return string
     */
    protected function VersionCheck()
    {
        // Get URI Query String
        $queryString = HabEngine::getInstance()->getQueryString();

        // Version need be in the QueryString
        if (!array_key_exists('Version', $queryString)) {
            return (new HabMessage(400, "You need assign your Java App Version to be checked the compatibility."))->renderJson();
        }

        // Check if the Version it's compatible
        return HabUpdater::checkEngineJava($queryString['Version']) ?
            (new HabMessage(200, "All right, Your HabClient JavaApp version it's compatible with the Engine."))->renderJson()
            : (new HabMessage(500, "Sorry but your HabClient Java App version it's outdated and not compatible with this Engine."))->renderJson();
    }
}
