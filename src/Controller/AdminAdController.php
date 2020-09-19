<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads", name="admin_ads_index")
     */
    public function index(AdRepository $repo)
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads' => $repo->findAll()
        ]);
    }

    /**
     * permet d'afficher le formulaire d'edition d'une annonce par l'admin
     *@Route("/admin/ads/{id}/edit",name="admin_ads_edit")
     * @param Ad $ad
     * @return void
     */
    public function edit(Ad $ad,Request $request,ManagerRegistry $managerRegistry){
        $form = $this->createForm(AdType::class,$ad);
        $manager = $managerRegistry->getManager();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Modications effectuées !"
            );
        }

        return $this->render('admin/ad/edit.html.twig',[
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * Undocumented function
     *@Route("/admin/ads/{id}/delete",name="admin_ads_delete")
     * @param Ad $ad
     * @param ManagerRegistry $managerRegistry
     * 
     */
    public function delete(Ad $ad,ManagerRegistry $managerRegistry){
        $manager = $managerRegistry->getManager();
        if(count($ad->getBookings()) > 0){
            $this->addFlash(
                'warning',
                "l'annonce possede des reservations vous ne pouvez donc pas la supprimm !"
            );
            return $this->redirectToRoute('admin_ads_index');
        } else {
            $manager->remove($ad);
            $manager->flush();
            $this->addFlash(
                'success',
                "l'annonce selectionnnée a été supprimée avec succès !"
            );
            return $this->redirectToRoute('admin_ads_index');
        }
        
    }
}
