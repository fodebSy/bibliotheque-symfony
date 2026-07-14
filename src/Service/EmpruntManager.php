<?php

namespace App\Service;

use App\Entity\Adherent;
use App\Entity\Emprunt;
use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;

class EmpruntManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function emprunter(Livre $livre, Adherent $adherent): Emprunt
    {
        if (!$livre->isDisponible()) {
            throw new \DomainException(sprintf(
                'Le livre "%s" n\'est pas disponible.',
                $livre->getTitre()
            ));
        }

        $emprunt = new Emprunt();
        $emprunt->setLivre($livre);
        $emprunt->setAdherent($adherent);
        $emprunt->setDateEmprunt(new \DateTime());
        $emprunt->setDateRetourPrevue(new \DateTime('+14 days'));

        $livre->setDisponible(false);

        $this->entityManager->persist($emprunt);
        $this->entityManager->flush();

        return $emprunt;
    }

    public function retourner(Emprunt $emprunt): void
    {
        $emprunt->setDateRetourEffective(new \DateTime());
        $emprunt->getLivre()->setDisponible(true);

        $this->entityManager->flush();
    }
}