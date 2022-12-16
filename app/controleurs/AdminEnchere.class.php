<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Enchere de l'application admin
 */

class AdminEnchere extends Admin
{

  protected $methodes =   [
    'a' => [
      'nom' => 'ajouterEnchere', 'droits' => [Utilisateur::PROFIL_MEMBRE]
    ],
    's' => [
      'nom' => 'supprimerEnchere', 'droits' => [Utilisateur::PROFIL_MEMBRE]
    ]
  ];

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
   * Ajouter/activer une enchère
   */
  public function ajouterEnchere()
  {
    $pays = $this->oRequetesSQL->getPays();
    $condition =  $this->oRequetesSQL->getCondition();
    $status =  $this->oRequetesSQL->getStatus();
    $ajouter = "";

    if (isset($_SESSION['oUtilisateur'])) {
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    $listeTimbreById = $this->oRequetesSQL->getTimbresById([
      "utilisateur_id" => $session->utilisateur_id
    ]);

    $listeTimbre = $this->oRequetesSQL->getTimbres();

    $enchere_date_fin = strtotime($_POST["enchere_date_fin"]);
    $enchere_date_fin = date('Y-m-d H:i:s', $enchere_date_fin);

    $enchere_date_debut = new DateTime("now", new DateTimeZone('America/New_York'));
    $enchere_date_debut = $enchere_date_debut->format('Y-m-d H:i:s');

    unset($_POST['enchere_date_fin']);

    $enchere_date_debut_array = ["enchere_date_debut" => $enchere_date_debut];
    $enchere_date_fin_array = ["enchere_date_fin" => $enchere_date_fin];
    $enchere = array_merge($enchere_date_debut_array, $_POST);
    $enchere = array_merge($enchere_date_fin_array, $enchere);

    $oEnchere = new Enchere($enchere);


    $erreurs = $oEnchere->erreurs;

    if (count($erreurs) === 0) {
      $nouvelleEnchere = $this->oRequetesSQL->ajouterEnchere([
        'enchere_prix_base'    => $oEnchere->enchere_prix_base,
        'enchere_date_debut' => $oEnchere->enchere_date_debut,
        'enchere_date_fin' => $oEnchere->enchere_date_fin,
        'enchere_timbre_id' => $oEnchere->enchere_timbre_id,
        'enchere_utilisateur_id' => $oEnchere->enchere_utilisateur_id
      ]);

      if ($nouvelleEnchere > 0) { // test de la clé de l'utilisateur ajouté
        $ajouter = "Enchère ajouté avec success";
      }
    }

    (new Vue)->generer(
      '/admin/vProfile',
      array(
        'oUtilisateur'        =>  $session,
        'titre'               => 'Profile d\'utilisateur',
        'pays'                => $pays,
        'condition'           => $condition,
        'status'              => $status,
        'listeTimbre'         => $listeTimbre,
        'listeTimbreById'     => $listeTimbreById,
        'erreurs'             => $erreurs,
        'ajouter'             => $ajouter
      ),
      'gabarits/gabarit-frontend'
    );
  }

  /**
   * Supprimer une enchère
   */
  public function supprimerEnchere()
  {
    $this->messageRetourAction = "";
    $this->classRetour = "";

    if (isset($_SESSION['oUtilisateur'])) {
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    
    if ($this->oRequetesSQL->supprimerEnchere($this->enchere_id)) {
      $this->messageRetourAction = "Suppression de l'enchère effectuée";
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Suppression du timbre non effectuée.";
    }
    
    $encheres = $this->oRequetesSQL->getEncheresById([
      "utilisateur_id" => $session->utilisateur_id
    ]);
    (new Vue)->generer(
      "admin/vEncheresUtil",
      array(
        'titre'  => "Mes enchères",
        'oUtilisateur'        =>  $session,
        'encheres' => $encheres,
        'messageRetourAction' => $this->messageRetourAction,
        'classRetour' => $this->classRetour
      ), 
      "gabarits/gabarit-frontend"
    );
  }
}
