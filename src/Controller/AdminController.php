<?php

namespace App\Controller;

use App\Repository\AdherentRepository;
use App\Repository\EmpruntRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function dashboard(
        LivreRepository $livreRepository,
        AdherentRepository $adherentRepository,
        EmpruntRepository $empruntRepository,
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'nbLivres' => $livreRepository->count([]),
            'nbLivresDisponibles' => $livreRepository->count(['disponible' => true]),
            'nbAdherents' => $adherentRepository->count([]),
            'nbEmpruntsEnCours' => $empruntRepository->count(['dateRetourEffective' => null]),
            'nbEmpruntsTotal' => $empruntRepository->count([]),
        ]);
    }
}