<?php


    namespace App\Controller;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class HomeController extends AbstractController{

        /**
         * @Route("/",name="homepage")
         */

        public function Home(){
            return $this->render(
                "home.html.twig",
                [
                    "title" => "Accueil",
                    "s_p" => "Bienvenu sur ma page d'accueil !"
                ]
            );
        }
        /**
         *
         *@Route("/try/{nom}",name = "try")
         * @return void
         */
        public function Try($nom = "Inconnu"){
            return $this->render("try.html.twig",["nom" => $nom]);
        }
    }