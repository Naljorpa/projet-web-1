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
    $messageRetourAction = "";

    if (isset($_SESSION['oUtilisateur'])) {
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    (new Vue)->generer(
      "frontend/vAccueil",
      array(
        'titre'  => "Accueil",
        'oUtilisateur'        =>  $session,
        'messageRetourAction' => $messageRetourAction
      ),
      "gabarits/gabarit-frontend"
    );
  }

  /**
   * Affiche la page d'inscription
   * 
   */

  public function inscription()
  {
    (new Vue)->generer(
      "frontend/vInscription",
      array(
        'titre'  => "Inscription"
      ),
      "gabarits/gabarit-frontend"
    );
  }

  /**
   * Affiche la page de connection
   * 
   */

  public function connexion()
  {
    (new Vue)->generer(
      "frontend/vConnection",
      array(
        'titre'  => "Connexion"
      ),
      "gabarits/gabarit-frontend"
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

    $listeTimbreById = $this->oRequetesSQL->getTimbresById([
      "utilisateur_id" => $session->utilisateur_id
    ]);

    $listeTimbre = $this->oRequetesSQL->getTimbres();

    (new Vue)->generer(
      'admin/vProfile',
      array(
        'oUtilisateur'        =>  $session,
        'titre'               => 'Profile d\'utilisateur',
        'pays'                => $pays,
        'condition'           => $condition,
        'status'              => $status,
        'listeTimbre'         => $listeTimbre,
        'listeTimbreById'     => $listeTimbreById
      ),
      'gabarits/gabarit-frontend'
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

    foreach ($encheres as $enchere) {
      if ($enchere["enchere_date_fin"] <= date('Y-m-d H:i:s')) {
        $this->oRequetesSQL->updateStatusToArchive($enchere["enchere_timbre_id"]);
      }
    }

    if ($_POST) {
      // echo '<pre>', print_r($_POST), '</pre>';

      if ($_POST["recherche"] != "") {
        $motRecherche = $_POST["recherche"];
      } else {
        $motRecherche = " ";
      }
      $anneeMin = $_POST["annee-min"];
      $anneeMax = $_POST["annee-max"];
      $prixMin = $_POST["prix-min"];
      $prixMax = $_POST["prix-max"];

      $newEncheresArray = [];
      $arrayAnneeMinMax = [];
      $arrayPrixMinMax = [];

      foreach ($encheres as $enchere) {

        $implodedArray = implode(" ", $enchere);

        if (strpos(strtolower($implodedArray), strtolower($motRecherche))) {

          array_push($newEncheresArray, $enchere);
        };
      }
      if ($anneeMin !== "" && $anneeMax !== "") {
        foreach ($newEncheresArray as $newEnchere) {
          $anneeDuTimbre = $newEnchere['timbre_annee'];
          if ($anneeDuTimbre >= $anneeMin && $anneeDuTimbre <= $anneeMax) {

            array_push($arrayAnneeMinMax, $newEnchere);
          }
        }
        $newEncheresArray = $arrayAnneeMinMax;
      }
      
      if ($prixMin !== "" && $prixMax !== "") {
        foreach ($newEncheresArray as $newEnchere) {
          $miseActuelle = $newEnchere['mise_valeur'];
          if ($miseActuelle >= $prixMin && $miseActuelle <= $prixMax) {

            array_push($arrayPrixMinMax, $newEnchere);
          }
        }
        $newEncheresArray = $arrayPrixMinMax;
      }


      (new Vue)->generer(
        "frontend/vEncheres",
        array(
          'titre'  => "Enchères",
          'oUtilisateur'        =>  $session,
          'encheres' => $newEncheresArray,
          'inputRecherche' => $motRecherche,
          'anneeMin' => $anneeMin,
          'anneeMax' => $anneeMax,
          'prixMin' => $prixMin,
          'prixMax' => $prixMax
        ),
        "gabarits/gabarit-frontend"
      );
    } else {

      $encheres = $this->oRequetesSQL->getEncheres();


      (new Vue)->generer(
        "frontend/vEncheres",
        array(
          'titre'  => "Enchères",
          'oUtilisateur'        =>  $session,
          'encheres' => $encheres
        ),
        "gabarits/gabarit-frontend"
      );
    }
  }


  /**
   *  Affiche la fiche d'un timbre
   * 
   */
  public function afficherFiche()
  {
    $fiche = false;
    $miseMax = "";

    $listeFavoris = $this->oRequetesSQL->getFavoris();

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
    $miseActuelle = $this->oRequetesSQL->getMise($this->enchere_id);
    if ($miseActuelle) {
      $miseMax = $miseActuelle["MAX(mise_valeur)"];
    }

    (new Vue)->generer(
      "frontend/vFiche",
      array(
        'titre'  => "Fiche",
        'oUtilisateur'        =>  $session,
        'fiche' => $fiche,
        'images' => $images,
        'miseActuelle' =>  $miseMax,
        'listeFavoris' => $listeFavoris
      ),
      "gabarits/gabarit-frontend"
    );
  }
}
