<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Enchere de l'application admin
 */

class AdminEnchere extends Admin
{

    protected $methodes =   [
            'a' => [
                'nom' => 'ajouterEnchere', 'droits' => [Utilisateur::PROFIL_MEMBRE]]
    ];

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
      'vProfile',
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
      'gabarit-frontend'
    );
  }
}
