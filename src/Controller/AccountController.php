<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * fonction permettant de se connecter sur le site
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig',[
            "hasError" => $error !== null,
            "username" => $username
        ]);
    }


    /**
     * fonction permettant de se deconnecter du site
     *@Route("/logout",name="account_logout")
     * @return Response
     */
    public function logout(){

    }


    /**
     * permet d'afficher le formulaire d'inscription
     *@Route("/register",name = "account_register")
     * @return Response
     */
    public function register(Request $request,ManagerRegistry $managerRegistry,UserPasswordEncoderInterface $encoder){
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        $manager = $managerRegistry->getManager();

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user,$user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success','Votre compte a bien été crée avec success vous pouvez maintenant vous connectez !');

            return $this->redirectToRoute('account_login');
        }

        return  $this->render('account/registration.html.twig',[
            'form' => $form->createView() 
        ]);
    }

    /**
     * function permettant de modifier son profile
     *
     * @Route("/account/profile",name="account_profile")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function profile(Request $request,ManagerRegistry $managerRegistry){

        $user = $this->getUser();//pour obtenir l'utilisateur connectee actuellement
        $form = $this->createForm(AccountType::class,$user);

        $form->handleRequest($request);
        $manager = $managerRegistry->getManager();

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success','Les données du profile ont été modifiée avec succès !');

        }

            

        return  $this->render('account/profile.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de changer le mot de passe d'un user
     *@Route("/account/update-password",name="account_password")
     *@IsGranted("ROLE_USER")
     * @return Response
     */
    public function updataPassword(Request $request,ManagerRegistry $managerRegistry,UserPasswordEncoderInterface $encoder){

        $passwordUpdate = new PasswordUpdate();

        $manager = $managerRegistry->getManager();

        $user = $this->getUser();//pour obtenir l'utilisateur connectee actuellement

        $form = $this->createForm(PasswordUpdateType::class,$passwordUpdate);
         
        $form->handleRequest($request);
       
        

       if($form->isSubmitted() && $form->isValid()){
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash())){
                //gerer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user,$newPassword);
                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success','Le mot de passe a été modifié avec succès !');

                return $this->redirectToRoute('homepage');
            }

            

        }
        
        return  $this->render('account/password.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * permert d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account",name="account_index")
     *@IsGranted("ROLE_USER")
     * @return Response
     */
    public function myAccount(){
        return $this->render('user/index.html.twig',[
            'user'=> $this->getUser()
        ]);
    }
}
