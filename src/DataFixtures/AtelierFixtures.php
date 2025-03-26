<?php

namespace App\DataFixtures;

use App\Entity\AtelierSatisfaction;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Atelier;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AtelierFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Admin et instructeur avec des ID fixes
        $admin = new User();
        $admin->setNom('Goujon')
            ->setPrenom('Maximilien')
            ->setUsername('admin')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'secret'))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $instructeur = new User();
        $instructeur->setNom('Nicolessi')
            ->setPrenom('Nathan')
            ->setUsername('instructeur')
            ->setPassword($this->passwordHasher->hashPassword($instructeur, 'secret'))
            ->setRoles(['ROLE_INSTRUCTEUR']);
        $manager->persist($instructeur);

        // Flush pour générer les IDs des premiers utilisateurs
        $manager->flush();

        // Création des apprentis
        $apprentis = [];
        for ($i = 1; $i <= 5; $i++) {
            $apprenti = new User();
            $apprenti->setUsername("apprenti{$i}")
                ->setPrenom("Prenom{$i}")
                ->setNom("Nom{$i}")
                ->setPassword($this->passwordHasher->hashPassword($apprenti, 'secret'))
                ->setRoles(['ROLE_APPRENTI']);
            $manager->persist($apprenti);
            $apprentis[] = $apprenti;
        }

        // Flush pour générer les IDs des apprentis
        $manager->flush();

        // Création des ateliers
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 1; $i <= 15; $i++) {
            $atelier = new Atelier();
            $atelier->setNom("Atelier {$i} : " . $faker->words(3, true))
                ->setDescription($faker->text)
                ->setUser($instructeur);

            $manager->persist($atelier);

            // Flush pour avoir l'ID de l'atelier
            $manager->flush();

            // Ajout des inscriptions et notes
            foreach ($apprentis as $apprenti) {
                if ($faker->boolean(70)) {
                    $atelier->addApprenti($apprenti);

                    $satisfaction = new AtelierSatisfaction();
                    $satisfaction->setAtelier($atelier)
                        ->setApprenti($apprenti)
                        ->setNote($faker->numberBetween(0, 5));
                    $manager->persist($satisfaction);
                }
            }

            $manager->flush();
        }
    }
}