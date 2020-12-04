<?php

namespace App\DataFixtures;

use App\Entity\Advert;
use App\Entity\Category;
use App\Entity\Skill;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $faker;
    private $entityManager;
    private $encoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->faker = Factory::create('fr_FR');
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadDate();
        $manager->flush();
    }

    public function loadDate(): void
    {
        $users = [];
        $genres = ['male', 'female'];

        // Manage Users
        for ($i=0; $i<15; $i++){

            $user = new User();
            $genre = $this->faker->randomElement($genres);

            $user->setFirstName($this->faker->firstName($genre))
                 ->setLastName($this->faker->lastName)
                 ->setEmail(strtolower($user->getLastName().'@gmail.com'))
                 ->setPassword($this->encoder->encodePassword($user, $user->getLastName()))
            ;

            $this->entityManager->persist($user);
            $users[] = $user;
        }

        // Manage a user with a ROLE_ADMIN
        $admin = new User();

        $admin->setFirstName('Raymond')
              ->setLastName('LOUA')
              ->setEmail('admin@gmail.com')
              ->setPassword($this->encoder->encodePassword($admin, 'admin'))
              ->setRoles([User::ROLE_ADMIN]);

        $this->entityManager->persist($admin);
        $users[] = $admin;

        // Manage Categories
        $names = [
            'Développement web',
            'Développement mobile',
            'Graphisme',
            'Intégration',
            'Réseau'
        ];

        foreach ($names as $name) {

            $category = new Category();

            $category->setName($name);

            $this->entityManager->persist($category);
        }

        // Manage Skills
        // Liste des noms de compétences à ajouter
        $names = [
                    'PHP',
                    'Symfony',
                    'C++',
                    'Java',
                    'Photoshop',
                    'Blender',
                    'Bloc-note'
                ];

        foreach ($names as $name) {

            $skill = new Skill();

            $skill->setName($name);

            $this->entityManager->persist($skill);
        }


        // Manage Adverts
        for ($i=0; $i<50; $i++){
            $advert = new Advert();
            $content = '<p>' . implode('</p><p>', $this->faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0, count($users)-1)];

            $advert->setTitle($this->faker->sentence)
                   ->setContent($content)
                   ->setAuthor($user)
            ;
                $this->entityManager->persist($advert);
            }
    }
}
