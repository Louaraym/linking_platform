<?php


namespace App\Service;


class Antispam
{
    private $minLength;
//    private $locale;

    public function __construct(int $minLength)
    {
        $this->minLength = (int) $minLength;
    }

    /**
     * Verify if the text is a spam or not
     * @param string $text
     * @return bool
     */
    public function isSpam(string $text): bool
    {
        return strlen($text) < $this->minLength;
    }


    /*public function setLocale($locale): void
    {
        $this->locale = $locale;
    }*/

}