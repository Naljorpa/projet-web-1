<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend
 * 
 */

class Frontend extends Routeur
{


  /**
   * Constructeur qui initialise des propriétés à partir du query string
   * et la propriété oRequetesSQL déclarée dans la classe Routeur
   * 
   */
  public function __construct()
  {
    $this->enchere_id = $_GET['enchere_id'] ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }


  /**
   * Mene vers la page d'accueil
   * 
   */
  public function accueil()
  {
    if (isset($_SESSION['oUtilisateur'])) {
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    (new Vue)->generer(
      "vAccueil",
      array(
        'titre'  => "Accueil",
        'oUtilisateur'        =>  $session
      ),
      "gabarit-frontend"
    );
  }

  /**
   * Affiche la page d'inscription
   * 
   */

  public function inscription()
  {
    (new Vue)->generer(
      "vInscription",
      array(
        'titre'  => "Inscription"
      ),
      "gabarit-frontend"
    );
  }

  /**
   * Affiche la page de connection
   * 
   */

  public function connexion()
  {
    (new Vue)->generer(
      "vConnection",
      array(
        'titre'  => "Connexion"
      ),
      "gabarit-frontend"
    );
  }

   /**
   * Affiche la page de connection
   * 
   */

  public function afficherProfile()
  {

    $pays = $this->oRequetesSQL->getPays();
    $condition =  $this->oRequetesSQL->getCondition();
    $status =  $this->oRequetesSQL->getStatus();

    if (isset($_SESSION['oUtilisateur'])) {
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    (new Vue)->generer(
      'vProfile',
      array(
        'oUtilisateur'        =>  $session,
        'titre'               => 'Profile d\'utilisateur',
        'pays'                => $pays,
        'condition'           => $condition,
        'status'           => $status
      ),
      'gabarit-frontend'
    );
  }
  /**
   * Lister les enchères
   * 
   */
  public function listerEncheres()
  {
    $encheres = $this->oRequetesSQL->getEncheres();

    if (isset($_SESSION['oUtilisateur'])) {
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    (new Vue)->generer(
      "vEncheres",
      array(
        'titre'  => "Enchères",
        'oUtilisateur'        =>  $session,
        'encheres' => $encheres
      ),
      "gabarit-frontend"
    );
  }


  /**
   *  Affiche la fiche d'un timbre
   * 
   */
  public function afficherFiche()
  {
    $fiche = false;
    if (!is_null($this->enchere_id)) {
      $fiche = $this->oRequetesSQL->getFiche($this->enchere_id);
      $images = $this->oRequetesSQL->getImages($this->enchere_id);
    }
    if (!$fiche) throw new Exception("Fiche timbre inexistante.");

    if (isset($_SESSION['oUtilisateur'])) {
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    (new Vue)->generer(
      "vFiche",
      array(
        'titre'  => "Fiche",
        'oUtilisateur'        =>  $session,
        'fiche' => $fiche,
        'images' => $images
      ),
      "gabarit-frontend"
    );
  }
}
