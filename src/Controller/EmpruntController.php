<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Repository\AdherentRepository;
use App\Service\EmpruntManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/emprunt')]
class EmpruntController extends AbstractController
{
    #[Route('/nouveau/{id}', name: 'app_emprunt_nouveau', methods: ['POST'])]
    public function emprunter(
        Livre $livre,
        Request $request,
        AdherentRepository $adherentRepository,
        EmpruntManager $empruntManager,
    ): Response {
        $adherent = $adherentRepository->find($request->request->get('adherent_id'));

        if (!$adherent) {
            $this->addFlash('error', 'Adhérent introuvable.');
            return $this->redirectToRoute('app_home');
        }

        try {
            $empruntManager->emprunter($livre, $adherent);
            $this->addFlash('success', sprintf('"%s" emprunté avec succès !', $livre->getTitre()));
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/retour/{id}', name: 'app_emprunt_retour', methods: ['POST'])]
    public function retourner(Emprunt $emprunt, EmpruntManager $empruntManager): Response
    {
        $empruntManager->retourner($emprunt);
        $this->addFlash('success', sprintf('"%s" est de retour en rayon !', $emprunt->getLivre()->getTitre()));

        return $this->redirectToRoute('app_home');
    }
}