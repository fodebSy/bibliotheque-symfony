<?php

namespace App\Controller;

use App\Entity\Adherent;
use App\Repository\EmpruntRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdherentController extends AbstractController
{
    #[Route('/adherent/{id}/historique', name: 'app_adherent_historique')]
    public function historique(Adherent $adherent, EmpruntRepository $empruntRepository): Response
    {
        return $this->render('adherent/historique.html.twig', [
            'adherent' => $adherent,
            'emprunts' => $empruntRepository->findHistoriqueAdherent($adherent),
        ]);
    }
}