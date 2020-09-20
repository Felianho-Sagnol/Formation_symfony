<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\PaginationService;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_booking_index")
     */
    public function index(BookingRepository $repo,$page,PaginationService $pagination)
    {
        $pagination->setEntityClass(Booking::class)
                   ->setPage($page);

        return $this->render('admin/bookings/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * permet d'editer une reservation
     *@Route("/admin/bookings/{id}/edit",name = "admin_booking_edit")
     * @return void
     */
    public function edit(Booking $booking,Request $request,ManagerRegistry $managerRegistry){
        $booking->setAmount(0);
        $manager = $managerRegistry->getManager();
        $form = $this->createForm(AdminBookingType::class,$booking,[
            'validation_groups' => ['Default'] //le group de validation par defaut est default
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce a bien été modifiée !"
            );

            return $this->redirectToRoute("admin_booking_index");
        }
        return $this->render('admin/bookings/edit.html.twig',[
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * permet de supprimer une reservation
     * @Route("/admin/booking/{id}/delete",name="admin_booking_delete")
     *
     * @param Booking $booking
     * @param ManagerRegistry $managerRegistry
     * @return Response
     */
    public function delete(Booking $booking,ManagerRegistry $managerRegistry){
        $manager = $managerRegistry->getManager();
        $manager->remove($booking);
        $manager->flush();
        $this->addFlash(
            'success',
            "L'annonce a bien été supprimée !"
        );

        return $this->redirectToRoute("admin_booking_index");
    }
}
