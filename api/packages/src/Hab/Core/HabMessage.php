<?php

namespace Hab\Core;

/**
 * Class HabMessage
 * @package Hab\Core
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class HabMessage
{
    /**
     * Message Code
     *
     * @var int
     */
    public $Code = 404;

    /**
     * Message Content
     *
     * @var string
     */
    public $Message = 'Not Found';

    /**
     * Message constructor.
     *
     * @param int $code
     * @param string $message
     */
    public function __construct($code, $message)
    {
        $this->Code = $code;
        $this->Message = $message;
    }

    /**
     * Add Custom Field to Message
     *
     * @param string $fieldName
     * @param mixed $fieldValue
     */
    public function addField($fieldName, $fieldValue)
    {
        $this->{$fieldName} = $fieldValue;
    }

    /**
     * Create the Message in jSON String
     *
     * @return string
     */
    public function renderJson()
    {
        header('Content-Type: application/json');

        // Uses the Method to remove the escaping from the slashes
        return json_encode($this, JSON_UNESCAPED_SLASHES);
    }
}
