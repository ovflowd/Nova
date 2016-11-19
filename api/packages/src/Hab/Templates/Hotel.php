<?php

namespace Hab\Templates;

use Hab\Core\HabEngine;
use Hab\Core\HabMessage;
use Hab\Core\HabUpdater;
use Hab\Database\DatabaseQueries;

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
        // Check if Updates Exists
        if (HabUpdater::checkUpdates() && FORCE_UPDATE_ENGINE) {
            $this->setResponse(HabUpdater::renderUpdates(true));

            return;
        }

        // Get URI Query String
        $queryString = HabEngine::getInstance()->getQueryString();

        // Choose SubPage
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
        // Get Token Authentication
        $oldToken = HabEngine::getInstance()->getTokenAuth();

        // If Token is Valid Continue
        if (DatabaseQueries::checkToken($oldToken)) {

            $client = HabEngine::getInstance()->getApiSettings();

            $message = new HabMessage(200, 'Authentication OK');
            $message->addField('Client', $client);
            $message->addField('NewToken', DatabaseQueries::updateToken($oldToken));

            return $message->renderJson();
        }

        return (new HabMessage(403, "Your Token isn't valid! Authentication Failed to obtain Hotel Data"))->renderJson();
    }

    /**
     * Obtain Server (Hotel) Online Status
     * (If Emulator is turned on or not)
     *
     * @TODO: Need use SOCK? or Query the Database?
     *
     * @return string
     */
    protected function Status()
    {
        // Get Token Authentication
        $oldToken = HabEngine::getInstance()->getTokenAuth();

        // If Token is Valid Continue
        if (DatabaseQueries::checkToken($oldToken)) {

            $engine = HabEngine::getInstance()->getEngineSettings()->tables;

            $isOnline = DatabaseQueries::getHotelStatus()->{$engine->serverColumns->online};

            $message = new HabMessage(200, 'Authentication OK');
            $message->addField('ServerStatus', $isOnline);
            $message->addField('NewToken', DatabaseQueries::updateToken($oldToken));

            return $message->renderJson();
        }

        return (new HabMessage(403, "Your Token isn't valid! Authentication Failed to obtain Hotel Data"))->renderJson();
    }

    /**
     * Obtain Server (Hotel) Online User Count
     *
     * @TODO: Need use COUNT(users.is_online) or ROW(server_status.online_count) ?
     *
     * @return string
     */
    protected function OnlineCount()
    {
        // Get Token Authentication
        $oldToken = HabEngine::getInstance()->getTokenAuth();

        // If Token is Valid Continue
        if (DatabaseQueries::checkToken($oldToken)) {

            $engine = HabEngine::getInstance()->getEngineSettings()->tables;

            $onlineCount = DatabaseQueries::getHotelStatus()->{$engine->serverColumns->onlineCount};

            $message = new HabMessage(200, 'Authentication OK');
            $message->addField('UserOnlineCount', $onlineCount);
            $message->addField('NewToken', DatabaseQueries::updateToken($oldToken));

            return $message->renderJson();
        }

        return (new HabMessage(403, "Your Token isn't valid! Authentication Failed to obtain Hotel Data"))->renderJson();
    }
}
