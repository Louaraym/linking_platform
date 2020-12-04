<?php

namespace App\Form;

use App\Entity\Advert;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Arbitrairement, on récupère toutes les catégories qui commencent par "D"
        $pattern = 'D%';
//        $pattern = ['D%', 'R%'];

        $builder
//            ->add('createdAt', DateTimeType::class)
            ->add('title', TextType::class, [
                'label' => "Titre de l'annonce",
            ])
            ->add('content', TextareaType::class, [
                'label' => "Contenu de l'annonce",
                'attr' => [
                    'rows' => 10
                ]
            ])
            ->add('categories', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                  /* 'query_builder' => static function(CategoryRepository $repository) use($pattern) {
                        return $repository->getLikeQueryBuilder($pattern);
                    },*/
                   /* 'query_builder' => static function(CategoryRepository $repository) use($pattern) {
                        return $repository->getCategoriesQueryBuilder($pattern);
                    }*/
            ])
/*            ->add('categories', CollectionType::class, [
                    'entry_type' => Category1Type::class,
                    'allow_add' => true,
                    'allow_delete' => true,
            ])*/
//            ->add('advertSkills')
            ->add('image', ImageType::class, [
                'required' => false,
            ])
          /*  ->add('published', CheckboxType::class, [
                'required' => false,
            ])*/
        ;

        // On ajoute une fonction qui va écouter un évènement
       /* La fonction qui est exécutée par l'évènement prend en argument l'évènement lui-même, la variable$event.
        Depuis cet objet évènement, vous pouvez récupérer d'une part l'objet sous-jacent, via$event->getData(),
        et d'autre part le formulaire, via$event->getForm()*/
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,    // 1er argument : L'évènement qui nous intéresse : ici, PRE_SET_DATA
            static function(FormEvent $event) { // 2e argument : La fonction à exécuter lorsque l'évènement est déclenché
                // On récupère notre objet Advert sous-jacent
                $advert = $event->getData();

                // Cette condition est importante, voir le commentaire en bas
                if (null === $advert) {
                    return; // On sort de la fonction sans rien faire lorsque $advert vaut null
                }

                // Si l'annonce n'est pas publiée, ou si elle n'existe pas encore en base (id est null)
                if (!$advert->getPublished() || null === $advert->getId()) {
                    // Alors on ajoute le champ published
                    $event->getForm()->add('published', CheckboxType::class, array('required' => false));
                } else {
                    // Sinon, on le supprime
                    $event->getForm()->remove('published');
                }
            }
        );

    }

    /*Je reviens sur la conditionif (null == $advert) dans la fonction. En fait, à la première création du formulaire,
    celui-ci exécute sa méthode setData() avec null en argument.
    Cette occurrence de l'évènementPRE_SET_DATAne nous intéresse pas, d'où la condition pour sortir de la fonction
    lorsque$event->getData()vautnull. Ensuite, lorsque le formulaire récupère l'objet ($advert dans notre cas)
    sur lequel se construire, il réexécute sa méthodesetData()avec l'objet en argument.
    C'est cette occurrence-là qui nous intéresse.*/

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
