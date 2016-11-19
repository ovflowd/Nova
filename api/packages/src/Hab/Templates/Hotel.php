<?php

namespace Hab\Templates;

use Hab\Core\HabEngine;
use Hab\Core\HabMessage;
use Hab\Core\HabUtils;

/**
 * Class Hotel
 * @package Hab\Templates
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class Hotel extends Base
{
    /**
     * Creates a new Instance of The Hotel Template
     * Ableing the System to choose one of it's sub Templates
     */
    public function __construct()
    {
        $queryString = HabEngine::getInstance()->getQueryString();

        if (array_key_exists('SubPage', $queryString)) {
            $this->setResponse($this->checkMethod($queryString['SubPage']) ? $this->{$queryString['SubPage']}() : $this->NotFound());
        }
    }

    /**
     * Obtain Hotel Client Settings
     *
     * @return string
     */
    protected function Client()
    {
        $oldToken = HabEngine::getInstance()->getTokenAuth();

        if (HabUtils::checkToken($oldToken)) {

            $client = HabEngine::getInstance()->getApiSettings();

            $message = new HabMessage(200, 'Authentication OK');
            $message->addField('Client', $client);
            $message->addField('NewToken', HabUtils::updateToken($oldToken));

            return $message->renderJson();
        }

        return (new HabMessage(403, "Your Token isn't valid! Authentication Failed to obtain Hotel Data"))->renderJson();
    }
}
