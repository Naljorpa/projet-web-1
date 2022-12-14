<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Mise de l'application admin
 */

class AdminMise extends Admin
{

    protected $methodes =   [
        'a' => [
            'nom' => 'ajouterMise', 'droits' => [Utilisateur::PROFIL_MEMBRE]
        ],
        'af' => [
            'nom' => 'ajouterMiseFiche', 'droits' => [Utilisateur::PROFIL_MEMBRE]
        ],
        'l' => [
            'nom' => 'listerMise', 'droits' => [Utilisateur::PROFIL_MEMBRE]
        ]
    ];

    /**
   * Ajouter une mise à partir de la page enchères
   */
  public function ajouterMise()
  {

    if (isset($_SESSION['oUtilisateur'])) {
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    $enchere_id = $_POST["enchere_id"];

    $encheres = $this->oRequetesSQL->getEncheres();

    $miseActuelle = $this->oRequetesSQL->getMise($enchere_id);

    $mise = [];
    $erreurs = [];
    $nouvelleMise = [];
    $succes = "";


    if (count($_POST) !== 0) {
      $mise = $_POST;
      $oMise = new Mise($mise);
      $erreurs = $oMise->erreurs;


      if (count($erreurs) === 0) {
        $nouvelleMise = $this->oRequetesSQL->ajouterMise([
          'mise_utilisateur_id'    => $oMise->mise_utilisateur_id,
          'mise_enchere_id' => $oMise->mise_enchere_id,
          'mise_valeur' => $oMise->mise_valeur
        ]);
        $succes = "Mise réalisée avec succès";
        if ($nouvelleMise > 0) { // test de la clé de l'utilisateur ajouté
          $succes = "Mise réalisée avec succès";
        }
      }
      $encheres = $this->oRequetesSQL->getEncheres();
    }
    (new Vue)->generer(
      'vEncheres',
      array(
        'titre'  => "Enchères",
        'mise'   => $mise,
        'oUtilisateur'        =>  $session,
        'encheres' => $encheres,
        'succes' => $succes,
        'erreurs'      => $erreurs
      ),
      "gabarit-frontend"
    );
  }


  /**
   * Ajouter une mise à partir de la fiche
   */
  public function ajouterMiseFiche()
  {


    $enchere_id = $_POST["enchere_id"];
    $fiche = false;
    if (!is_null($enchere_id)) {
      $fiche = $this->oRequetesSQL->getFiche($enchere_id);
      $images = $this->oRequetesSQL->getImages($enchere_id);
    }
    if (!$fiche) throw new Exception("Fiche timbre inexistante.");


    if (isset($_SESSION['oUtilisateur'])) {
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    $encheres = $this->oRequetesSQL->getEncheres();

    $miseActuelle = $this->oRequetesSQL->getMise($enchere_id);

    $mise = [];
    $erreurs = [];
    $nouvelleMise = [];
    $succes = "";


    if (count($_POST) !== 0) {
      $mise = $_POST;
      $oMise = new Mise($mise);
      $erreurs = $oMise->erreurs;

      if (count($erreurs) === 0) {
        $nouvelleMise = $this->oRequetesSQL->ajouterMise([
          'mise_utilisateur_id'    => $oMise->mise_utilisateur_id,
          'mise_enchere_id' => $oMise->mise_enchere_id,
          'mise_valeur' => $oMise->mise_valeur
        ]);
        if ($nouvelleMise > 0) { // test de la clé de l'utilisateur ajouté
          $succes = "Mise réalisée avec succès";
        }
      }
      $miseActuelle = $this->oRequetesSQL->getMise($enchere_id);
    }

    (new Vue)->generer(
      'vFiche',
      array(
        'titre'  => "Fiche",
        'mise'   => $mise,
        'oUtilisateur'        =>  $session,
        'fiche' => $fiche,
        'miseActuelle' => $miseActuelle["MAX(mise_valeur)"],
        'images' => $images,
        'succes' => $succes,
        'erreurs'      => $erreurs
      ),
      "gabarit-frontend"
    );
  }



}
