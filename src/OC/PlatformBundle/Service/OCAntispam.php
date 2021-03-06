<?php

namespace OC\PlatformBundle\Service;

class OCAntispam
{
    private $mailer;
    private $locale;
    private $minLength;

    public function __construct(\Swift_Mailer $mailer, $locale, $minLength)
    {
        $this->mailer = $mailer;
        $this->locale = $locale;
        $this->minLength = (int) $minLength;
    }

    /**
     * Antispam function
     *
     * @param string $text
     * @return boolean
     */
    public function isSpam($text)
    {
        return strlen($text) < $this->minLength;
    }
}