<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdvertEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('published')
        ;
    }

    /**
     * @return string
     */
    public function getParent(): string
    {
        return AdvertType::class;
    }
}
