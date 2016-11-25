<?php

namespace Hab\Templates;

use Hab\Core\HabEngine;
use Hab\Core\HabMessage;
use Hab\Core\HabUpdater;
use Hab\Database\DatabaseQueries;

/**
 * Class User
 * @package Hab\Templates
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class User extends Base
{
    /**
     * Creates a new Instance of The User Template
     * Ableing the System to choose one of it's sub Templates
     */
    public function __construct()
    {
        // Check if Updates Exists
        if (HabUpdater::checkUpdates(true)) {
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
     * User Auth Message
     *
     * @return string
     */
    protected function Login()
    {
        // Get Token Authentication
        $oldToken = HabEngine::getInstance()->getTokenAuth();

        // If Token is Valid Continue
        if (DatabaseQueries::checkToken($oldToken)) {

            $user = DatabaseQueries::getUserData($oldToken);

            $message = new HabMessage(200, 'Authentication OK');
            $message->addField('User', $user);
            $message->addField('NewToken', DatabaseQueries::updateToken($oldToken));

            return $message->renderJson();
        }

        return (new HabMessage(403, "Your Token isn't valid! Authentication Failed to obtain User Data."))->renderJson();
    }
}
