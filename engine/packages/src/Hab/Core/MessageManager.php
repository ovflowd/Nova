<?php

namespace Hab\Core;

/**
 * Class MessageManager
 * @package Hab\Core
 *
 * @version 0.1
 * @author Claudio Santoro
 */
class MessageManager
{
    /**
     * @var array Messages
     */
    private $engineMessages = [];

    /**
     * @var string HTML Result
     */
    private $renderMessage = null;

    /**
     * Get Message Manager Instance
     *
     * @return MessageManager
     */
    public static function getInstance()
    {
        static $instance = null;

        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Fetch all ENGINE_MESSAGES Messages
     */
    public function fetchMessages()
    {
        $messages = HabUtils::getRemoteContent('https://raw.githubusercontent.com/sant0ro/Nova/master/ENGINE_MESSAGES.json');

        foreach (json_decode($messages)->messages as $messageObject) {
            $this->engineMessages[] = $messageObject->message;
        }
    }

    /**
     * Generate the Result if doesn't exists
     * And if exists return it.
     *
     * @return string
     */
    private function generateResult()
    {
        if ($this->renderMessage == null) {
            $this->renderMessage = '<ul class="tweet_list">';

            if ($this->engineMessages == null) {
                $this->fetchMessages();
            }

            foreach ($this->engineMessages as $index => $value) {
                $this->renderMessage .= $index % 2 == 0 ? '<li class="tweet_even">' : '<li>';
                $this->renderMessage .= $value;
                $this->renderMessage .= '</li>';
            }

            $this->renderMessage .= '</ul>';
        }

        return $this->renderMessage;
    }

    /**
     * Get Rendered Message
     *
     * @return string
     */
    public function getMessages()
    {
        return $this->generateResult();
    }
}
