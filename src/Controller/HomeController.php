<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AdherentRepository;
use App\Repository\EmpruntRepository;

class HomeController extends AbstractController
{
   #[Route('/', name: 'app_home')]
public function index(LivreRepository $livreRepository,
    AdherentRepository $adherentRepository,
    EmpruntRepository $empruntRepository,
    Request $request): Response
{
    $categorie = $request->query->get('categorie');
    $recherche = $request->query->get('q');

    if ($recherche) {
        $livres = $livreRepository->rechercher($recherche);
    } else {
        $criteres = ['disponible' => true];
        if ($categorie) {
            $criteres['categorie'] = $categorie;
        }
        $livres = $livreRepository->findBy($criteres);
    }

    $categories = $livreRepository->createQueryBuilder('l')
        ->select('DISTINCT l.categorie')
        ->orderBy('l.categorie', 'ASC')
        ->getQuery()
        ->getSingleColumnResult();

    return $this->render('home/index.html.twig', [
        'livres' => $livres,
        'categories' => $categories,
        'categorieActive' => $categorie,
        'recherche' => $recherche,
        'adherents' => $adherentRepository->findAll(),
        'empruntsEnCours' => $empruntRepository->findBy(['dateRetourEffective' => null])
    ]);
}

}