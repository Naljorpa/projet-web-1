<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend
 * 
 */

class Frontend extends Routeur
{

  // private $film_id;

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
    if(isset($_SESSION['oUtilisateur'])){
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
   * Lister les enchères
   * 
   */
  public function listerEncheres()
  {
    $encheres = $this->oRequetesSQL->getEncheres();

    if(isset($_SESSION['oUtilisateur'])){
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

  public function afficherFiche()
  {
    $fiche = false;
    if (!is_null($this->enchere_id)) {
      $fiche = $this->oRequetesSQL->getFiche($this->enchere_id);
    }
    if (!$fiche) throw new Exception("Fiche timbre inexistante.");

    if(isset($_SESSION['oUtilisateur'])){
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    echo '<pre>',print_r($fiche),'</pre>';

    (new Vue)->generer(
      "vFiche",
      array(
        'titre'  => "Fiche",
        'oUtilisateur'        =>  $session,
        'fiche' => $fiche
      ),
      "gabarit-frontend"
    );
  }

  /**
   * Voir les informations d'un film
   * 
   */
  public function voirFilm()
  {
    $film = false;
    if (!is_null($this->film_id)) {
      $film = $this->oRequetesSQL->getFilm($this->film_id);
      $realisateurs = $this->oRequetesSQL->getRealisateursFilm($this->film_id);
      $pays         = $this->oRequetesSQL->getPaysFilm($this->film_id);
      $acteurs      = $this->oRequetesSQL->getActeursFilm($this->film_id);

      // si affichage avec vFilm2.twig
      // =============================
      // $seances      = $this->oRequetesSQL->getSeancesFilm($this->film_id); 

      // si affichage avec vFilm.twig
      // ============================
      $seancesTemp  = $this->oRequetesSQL->getSeancesFilm($this->film_id);
      $seances = [];
      foreach ($seancesTemp as $seance) {
        $seances[$seance['seance_date']]['jour']     = $seance['seance_jour'];
        $seances[$seance['seance_date']]['heures'][] = $seance['seance_heure'];
      }
    }
    if (!$film) throw new Exception("Film inexistant.");

    (new Vue)->generer(
      "vFilm",
      array(
        'titre'        => $film['film_titre'],
        'film'         => $film,
        'realisateurs' => $realisateurs,
        'pays'         => $pays,
        'acteurs'      => $acteurs,
        'seances'      => $seances
      ),
      "gabarit-frontend"
    );
  }
}
