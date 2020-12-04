<?php


namespace App\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = "Vous avez déjà posté un message il y a moins de 15 secondes, merci d'attendre un peu.";

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return 'linking_platform_antiflood'; // Ici, on fait appel à l'alias du service
    }
}

