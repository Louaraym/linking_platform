<?php


namespace App\TwigExtension;


use App\Service\Antispam;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AntispamExtension extends AbstractExtension
{
    private $antispam;

    public function __construct(Antispam $antispam)
    {
        $this->antispam = $antispam;
    }

    /**
     * @param string $text
     * @return bool
     */
    public function checkIfArgumentIsSpam(string $text): bool
    {
        return $this->antispam->isSpam($text);
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
       return [
            new TwigFunction('checkIfSpam',[$this, 'checkIfArgumentIsSpam']),
        ];

       /* return [
            new TwigFunction('checkIfSpam',[$this->antispam, 'isSpam']),
        ];*/
    }

    /**
     * Identify our Twig extension
     * @return string
     */
    public function getName(): string
    {
        return 'Antispam';
    }
}