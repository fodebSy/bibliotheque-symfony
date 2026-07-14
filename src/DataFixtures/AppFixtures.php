<?php

namespace App\DataFixtures;

use App\Entity\Adherent;
use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
    private UserPasswordHasherInterface $passwordHasher,
) {
}

    public function load(ObjectManager $manager): void
    {
        // --- Les livres 📚 ---
        $livresData = [
            ['1984', 'George Orwell', '9780451524935', 1949, 'Science-fiction'],
            ['Le Seigneur des Anneaux', 'J.R.R. Tolkien', '9782266154116', 1954, 'Fantasy'],
            ['L\'Étranger', 'Albert Camus', '9782070360024', 1942, 'Roman'],
            ['Dune', 'Frank Herbert', '9782266320481', 1965, 'Science-fiction'],
            ['Le Petit Prince', 'Antoine de Saint-Exupéry', '9782070612758', 1943, 'Jeunesse'],
        ];

        foreach ($livresData as [$titre, $auteur, $isbn, $annee, $categorie]) {
            $livre = new Livre();
            $livre->setTitre($titre);
            $livre->setAuteur($auteur);
            $livre->setIsbn($isbn);
            $livre->setAnneePublication($annee);
            $livre->setCategorie($categorie);
            $livre->setDisponible(true);

            $manager->persist($livre);
        }

        // --- Les adhérents 📇 ---
        for ($i = 1; $i <= 3; $i++) {
            $adherent = new Adherent();
            $adherent->setNom('Dupont' . $i);
            $adherent->setPrenom('Jean' . $i);
            $adherent->setEmail('jean.dupont' . $i . '@example.com');
            $adherent->setNumeroAdherent('AD-000' . $i);
            $adherent->setDateInscription(new \DateTime());

            $manager->persist($adherent);
        }

        $admin = new User();
        $admin->setEmail('admin@bibliotheque.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
             $this->passwordHasher->hashPassword($admin, 'admin123')
);

$manager->persist($admin);

        $manager->flush();
    }
}