<?php

namespace Hab\Templates;

use Hab\Core\HabEngine;
use Hab\Core\HabMessage;
use Hab\Core\HabUtils;

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
        $queryString = HabEngine::getInstance()->getQueryString();

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
        $oldToken = HabEngine::getInstance()->getTokenAuth();

        if (HabUtils::checkToken($oldToken)) {

            $user = HabUtils::getUserData($oldToken);

            $message = new HabMessage(200, 'Authentication OK');
            $message->addField('User', $user);
            $message->addField('NewToken', HabUtils::updateToken($oldToken));

            return $message->renderJson();
        }

        return (new HabMessage(403, "Your Token isn't valid! Authentication Failed to obtain User Data."))->renderJson();
    }
}
