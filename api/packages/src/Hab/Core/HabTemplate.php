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
        // Templates Directory
        /** @var Base $templateClass */
        $templateClass = 'Hab\Templates\\' . $templateName;

        // Check if class exists
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
     * Include PHP Vendor Script
     *
     * Uses PHP's Ob Method
     * Since it's a Phar Archive need to do it recursively.
     *
     * @param string $vendorFile
     * @return string
     */
    public static function includeVendor($vendorFile)
    {
        ob_start();

        include("phar://HabClient.phar/Vendor/{$vendorFile}");

        $stringBuilder = ob_get_contents();

        ob_end_clean();

        return $stringBuilder;
    }

    /**
     * Get Vendor Asset Contents
     *
     * Only for CSS, JavaScript.
     *
     * @Attention Not recommended for Images,
     * since you need obligatorily add the Content-Type Header
     *
     * @param string $vendorFile
     * @return string
     */
    public static function getVendor($vendorFile)
    {
        return file_get_contents("phar://HabClient.phar/Vendor/{$vendorFile}");
    }

    /**
     * Get Response
     *
     * @return string
     */
    public function getResponse()
    {
        error_log('[HabClient][Router] Render Results OK to visitor: ' . $_SERVER['REMOTE_ADDR']);

        return $this->response;
    }
}
