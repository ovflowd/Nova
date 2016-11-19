<?php

namespace Hab\Core;

use Hab\Templates\Base;

/**
 * Class HabTemplate
 * @package Hab\Core
 *
 * @version 0.1
 * @author Claudio Santoro
 */
final class HabTemplate
{
    /**
     * Response from Template
     *
     * @var string
     */
    private $response = '';

    /**
     * Template constructor.
     *
     * @param string $pageName
     */
    public function __construct($pageName)
    {
        $this->response = $this->handleTemplate($pageName);
    }

    /**
     * Handle a Main Template
     *
     * @param string $templateName
     * @return string
     */
    private function handleTemplate($templateName)
    {
        /** @var Base $templateClass */
        $templateClass = 'Hab\Templates\\' . $templateName;

        if (class_exists($templateClass)) {
            $templateClass = new $templateClass();

            return $templateClass->getResponse();
        }

        return $this->NotFound();
    }

    /**
     * Not Found Message
     *
     * @return string
     */
    public function NotFound()
    {
        return (new HabMessage(404, "The Desired Template wasn't found on our Records"))->renderJson();
    }

    /**
     * Get Response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }
}
