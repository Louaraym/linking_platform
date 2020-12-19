<?php


namespace App\Validator;


use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AntifloodValidator extends ConstraintValidator
{
    private $requestStack;
    private $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        /* Pour récupérer l'objet Request tel qu'on le connait,
         il faut utiliser getCurrentRequest du service request_stack */
        $request = $this->requestStack->getCurrentRequest();

        // On récupère l'IP de celui qui poste
        $ip = $request->getClientIp();

        // On vérifie si cette IP a déjà posté une candidature il y a moins de 15 secondes
        $isFlood = $this->entityManager
            ->getRepository(Application::class)
            ->isFlood($ip, 15)
        ;

        if ($isFlood) {
            // C'est cette ligne qui déclenche l'erreur pour le formulaire, avec en argument le message
            $this->context->addViolation($constraint->message);
        }
    }

    /*public function validate($value, Constraint $constraint)
    {
        // Pour l'instant, on considère comme flood tout message de moins de 3 caractères
        if (strlen($value) < 3) {
            // C'est cette ligne qui déclenche l'erreur pour le formulaire, avec en argument le message de la contrainte
            $this->context
                    ->buildViolation($constraint->message)
                    ->setParameters('%message%' => $value)
                    ->addViolation()
            ;
        }
    }*/

}