<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ManagerRegistry $managerRegistry,StatsService $statsService)
    {
        $manager = $managerRegistry->getManager();

        //ces requetes permettent de recuperer les nombre d'element dans une entité


        //classement des annonces par meilleur note
        $bestAds = $statsService->getAdsStats('DESC');

        $worstAds = $statsService->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $statsService->getStats(),
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
