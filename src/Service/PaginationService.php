<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService {
    private $entityClass;
    private $limit = 10;
    private $currentPage;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(ManagerRegistry $managerRegistry,Environment $twig,RequestStack $request,$templatePath) {
        $manager = $managerRegistry->getManager();
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    public function getRoute(){
        return $route;
    }

    public function setRoute($route){
        $this->route = $route;

        return $this;
    }

    public function getTemplatePath(){
        return $this->templatePath;
    }

    public function setTemplatePath($templatePath){
        $this->templatePath = $templatePath;

        return $this;
    }

    public function display() {
        $this->twig->display($this->templatePath,[
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    public function getPages() {
        if(empty($this->getEntityClass)){
            throw new \Exception("Vous n'avez pas donner la classe sur laquel la pagination doit se faire
                utiliser la methode setEntityClass() de votre objet Pagination.
            ");
        }
        //1)connaitre le nombre total d'enregistrement de la table
        $repo = $this->manager->getRepository($this->getEntityClass);
        $total = count($repo->findAll());
        $pages = ceil($total / $this->limit);
        //2)return le nombre total de pages
        return $pages;
    }

    public function getData() {
        if(empty($this->getEntityClass)){
            throw new \Exception("Vous n'avez pas donner la classe sur laquel la pagination doit se faire
                utiliser la methode setEntityClass() de votre objet Pagination.
            ");
        }
        //1) calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        //2)demander au repository de trouver les données
        $repo = $this->manager->getRepository($this->getEntityClass);
        $data = $repo->findBy([],[],$this->limit,$offset);
        //3)Renvoyer le données en question
        return $data;
    }

    public function setEntityClass($entityClass){
        $this->getEntityClass = $entityClass;
        return $this;
    }

    public function getEntityClass(){
        return $this->getEntityClass;
    }

    public function setLimit($limit){
        $this->limit = $limit;
        return $this;
    }

    public function getLimit() {
        return $this->limit;
    }

    public function setPage($page){
        $this->currentPage = $page;
        return $this;
    }

    public function getPage(){
        return $this->currentPage;
    }
}