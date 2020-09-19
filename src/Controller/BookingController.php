<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad,Request $request,ManagerRegistry $managerRegistry)
    {
        $manager = $managerRegistry->getManager();

        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();

            $booking->setBooker($user)
                    ->setAd($ad);
            
            //si les date sont pas dispo message erreur
            if(!$booking->isBookableDate()){
                $this->addFlash(
                    'warning',
                    "les date que vous avez choisies ne peuvent être reservée: elle sont déjé prises"
                );
            }else{
                //sinon enregistrement et redirection
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute('booking_show',['id' => $booking->getId(),'withAlert' => true]);
            }

        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * montre une reservation effectuée avec succes
     *@Route("/booking/{id}",name="booking_show")
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking,Request $request,ManagerRegistry $managerRegistry){

        $comment = new Comment();

        $manager = $managerRegistry->getManager();

        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commentaire a bien été pris en compte merci !"
            );
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }
}
