<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CkeditorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'ckeditor'] // On ajoute la classe CSS
        ]);
    }

    public function getParent(): string // On utilise l'héritage de formulaire
    {
        return TextareaType::class;
    }

    /* Ce type de champ hérite de toutes les fonctionnalités d'un textarea ( grâce à la méthode getParent() )
    tout en disposant de la classe CSS ckeditor ( définie dans la méthode setDefaultOptions() ) vous permettant,
    en ajoutant ckeditor à votre site, de transformer vos <textarea> en éditeur WYSIWYG.

    Puis, déclarons cette classe en tant que service, en lui ajoutant le tag form.type */

}