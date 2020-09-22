<?php

/**
 * Dans ce fichier les regle nous permettant de de bien configurer une application symfony.
 */

 /**
  * 1)la creation d'un formulaire d'inscription se basse sur une entité deja representé sur notre database par exemple
  * User qui peut egalement avoir des champs suplementaire qui ne sont pas dans la data base comme par ex un champ de 
  * de confirmation de password pour lui juste faire apparaitre sur le formulaire dans la vue
  */

  /**
   * 2) pour creer un formulaire d'inscrition  pour que les utilisateur ne s'incrscrive pas 2 fois, on utilise
   *  l'annotation UniqueEntity(
   *    fields = {'email',..} pour dire que l'email et les autre devront etre unique sur le site
   *    message = "l'emaile que vous avez choisi existe deja" le message d'erreur si cet email existe deja
   * ) sur l'entité User
   */