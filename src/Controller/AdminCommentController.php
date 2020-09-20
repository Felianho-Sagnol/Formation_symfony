<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comment_index")
     */
    public function index(CommentRepository $repo,$page,PaginationService $pagination)
    {
        $pagination->setEntityClass(Comment::class)
                    ->setLimit(5)
                   ->setPage($page);
        return $this->render('admin/comment/index_comment.html.twig', [
            'pagination' => $pagination

        ]);
    }

    /**
     * permet d'editer un commentaire
     *@Route("/admin/comments/{id}/edit",name="admin_comment_edit")
     * @param Comment $comment
     * @return void
     */
    public function edit(Comment $comment,Request $request,ManagerRegistry $managerRegistry){
        $form = $this->createForm(AdminCommentType::class,$comment);
        $form->handleRequest($request);
        $manager = $managerRegistry->getManager();

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash(
                'success',
                "Modications effectuées  avec succès!"
            );
        }
        return $this->render('admin/comment/edit.html.twig',[
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de supprimet un commentaire
     *@Route("/admin/comments/{id}",name="admin_comment_delete")
     * @param Comment $comment
     * @return void
     */
    public function delete(Comment $comment,ManagerRegistry $managerRegistry){
        $manager = $managerRegistry->getManager();
        $manager->remove($comment);
        $manager->flush();
        $this->addFlash(
            'success',
            "Commentaire supprimé avec succès!"
        );
        return $this->redirectToRoute('admin_comment_index');
    }
}
