<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
 */

class Admin extends Routeur
{

  private $entite;
  private $action;
  private $utilisateur_id;

  private $oUtilisateur;

  private $methodes = [
    'utilisateur' => [
      'l' => [
        'nom' => 'profileUtilisateur',
        // 'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR]
      ],
      'a' => [
        'nom' => 'ajouterUtilisateur'
      ],
      'c' => [
        'nom' => 'connecter'
      ],
      'm' =>  [
        'nom' => 'modifierUtilisateur',
        //  $this->livre_id = $_GET['livre_id'] ?? null; 
      ],
      's' => [
        'nom' => 'supprimerUtilisateur',
        // 'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR]
      ],
      'd' => [
        'nom' => 'deconnecter'
      ],
      'genererMdp' =>  [
        'nom' => 'genererMdp',
        // 'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR]
      ]
    ],

    'film' => [
      'l' => [
        'nom' => 'listerFilms',
        // 'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR]
      ],
      'a' => [
        'nom' => 'ajouterFilm',
        'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR]
      ],
      'm' =>  [
        'nom' => 'modifierFilm',
        // 'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR]
      ],
      's' => [
        'nom' => 'supprimerFilm',
        // 'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR]
      ]
    ]
  ];







  private $classRetour = "fait";
  private $messageRetourAction = "";

  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */
  public function __construct()
  {
    $this->entite    = $_GET['entite']    ?? 'utilisateur';
    $this->action    = $_GET['action']    ?? 'l';
    $this->utilisateur_id = $_GET['utilisateur_id'] ?? null;
    // $this->film_id          = $_GET['film_id']  ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }

  /**
   * Gérer l'interface d'administration 
   */
  public function gererAdmin()
  {
    echo '<pre>', print_r($_POST), '</pre>';
    if (isset($_SESSION['oUtilisateur'])) {
      $this->oUtilisateur = $_SESSION['oUtilisateur'];
      if (isset($this->methodes[$this->entite])) {
        if (isset($this->methodes[$this->entite][$this->action])) {
          $methode = $this->methodes[$this->entite][$this->action]['nom'];
          if (isset($this->methodes[$this->entite][$this->action]['droits'])) {
            $droits = $this->methodes[$this->entite][$this->action]['droits'];
            foreach ($droits as $value) {
              if ($value == $this->oUtilisateur->utilisateur_profile_id) {
                $this->$methode();
                exit;
              }
            }
            throw new Exception(self::FORBIDDEN);
          } else {
            $this->$methode();
          }
        } else {
          throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
        }
      } else {
        throw new Exception("L'entité $this->entite n'existe pas.");
      }
    } else if ($this->methodes[$this->entite][$this->action]['nom'] == "ajouterUtilisateur") {
      $this->ajouterUtilisateur();
    } else {
      $this->connecter();
    }
  }

  /**
   * Connecter un utilisateur
   */
  public function connecter()
  {
    $messageErreurConnexion = "";
    if (count($_POST) !== 0) {
      $utilisateur = $this->oRequetesSQL->connecter($_POST);
      if ($utilisateur !== false) {

        $_SESSION['oUtilisateur'] = new Utilisateur($utilisateur);
        $this->oUtilisateur = $_SESSION['oUtilisateur'];

        // if ($this->oUtilisateur->utilisateur_profile_id == Utilisateur::PROFIL_MEMBRE) throw new Exception(self::FORBIDDEN);
        // if ($this->oUtilisateur->utilisateur_profil == Utilisateur::PROFIL_EDITEUR) $this->gestionFilms();
        // if ($this->oUtilisateur->utilisateur_profile_id == Utilisateur::PROFIL_ADMINISTRATEUR)
        $this->profileUtilisateur($this->oUtilisateur);
        exit;
      } else {
        $messageErreurConnexion = "Courriel ou mot de passe incorrect.";
      }
    }

    (new Vue)->generer(
      'vConnection',
      array(
        'titre'                  => 'Connexion',
        'messageErreurConnexion' => $messageErreurConnexion
      ),
      'gabarit-frontend'
    );
  }

  /**
   * Déconnecter un utilisateur
   */
  public function deconnecter()
  {
    unset($_SESSION['oUtilisateur']);
    $this->connecter();
  }

  /**
   * Genere Mdp et envoi de courriel
   */
  public function genererMdp()
  {

    $oUtilisateur = new Utilisateur(["utilisateur_id" => $this->utilisateur_id]);

    $oUtilisateur->genererMdp();
    $this->oRequetesSQL->modifierMdp([
      "utilisateur_id" => $oUtilisateur->utilisateur_id,
      "utilisateur_mdp" => $oUtilisateur->utilisateur_mdp
    ]);

    $mdp = $oUtilisateur->utilisateur_mdp;
    $oUtilisateur = $this->oRequetesSQL->getUtilisateur($this->utilisateur_id);
    $oUtilisateur["utilisateur_mdp"] = $mdp;


    $retour = (new GestionCourriel)->envoyerMdp($oUtilisateur);
    if ($retour) echo "Courriel envoyé<br>.";
    if (ENV === "DEV") echo "<a href=\"$retour\">Message dans le fichier $retour</a>";
  }

  /**
   * Function pour génère la une vue pour l'éditeur qui n'affiche que le gabarit latéral.
   */
  public function gestionFilms()
  {

    (new Vue)->generer(
      'vGestionFilms',
      array(
        'oUtilisateur'        => $this->oUtilisateur,
        'titre'               => 'Gestion des films',
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }


  /**
   * Lister les utilisateurs
   */
  public function profileUtilisateur($oUtilisateur)
  {

    echo '<pre>', print_r($oUtilisateur), '</pre>';

    if(isset($this->oUtilisateur)){
      $oUtilisateur = $this->oUtilisateur;
    } else {
      $oUtilisateur = $oUtilisateur;
    }

    (new Vue)->generer(
      'vProfile',
      array(
        'oUtilisateur'        => $oUtilisateur,
        'titre'               => 'Profile d\'utilisateur',
        // 'utilisateur'        => $utilisateur,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }

  /**
   * Ajouter un utilisateur
   */
  public function ajouterUtilisateur()
  {
    $utilisateur  = [];
    $erreurs = [];
    if (count($_POST) !== 0) {
      // retour de saisie du formulaire
      $utilisateur = $_POST;
      $oUtilisateur = new Utilisateur($utilisateur); // création d'un objet Utilisateur pour contrôler la saisie
      $erreurs = $oUtilisateur->erreurs;
      if (count($erreurs) === 0) {
        $oUtilisateur->verifie_courriel($oUtilisateur);
      }
      $erreurs = $oUtilisateur->erreurs;
      if (count($erreurs) === 0) { // aucune erreur de saisie -> requête SQL d'ajout
        $oUtilisateur->genererMdp();
        $utilisateur_id = $this->oRequetesSQL->ajouterUtilisateur([
          'utilisateur_nom'    => $oUtilisateur->utilisateur_nom,
          'utilisateur_prenom' => $oUtilisateur->utilisateur_prenom,
          'utilisateur_courriel' => $oUtilisateur->utilisateur_courriel,
          'utilisateur_mdp' => $oUtilisateur->utilisateur_mdp,
          'utilisateur_profile_id' => $oUtilisateur->utilisateur_profile_id
        ]);
        if ($utilisateur_id > 0) { // test de la clé de l'utilisateur ajouté
          $retour = (new GestionCourriel)->envoyerMdp($oUtilisateur);
          $this->messageRetourAction = "Ajout de l'utilisateur numéro $utilisateur_id effectué.<br>";
          if ($retour) {
            $this->messageRetourAction .= "Courriel envoyé<br>.";
            if (ENV === "DEV") {
              $this->messageRetourAction .= "<a href=\"$retour\">Message dans le fichier $retour</a>";
            }
          }
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "Ajout de l'utilisateur non effectué.";
        }
        $this->profileUtilisateur($oUtilisateur); // retour sur la page de liste des utilisateurs
        exit;
      }
    }

    (new Vue)->generer(
      'vInscription',
      array(
        'oUtilisateur' => $this->oUtilisateur,
        'titre'        => 'Inscription',
        'utilisateur'       => $utilisateur,
        'erreurs'      => $erreurs
      ),
      'gabarit-frontend'
    );
  }

  /**
   * Modifier un auteur identifié par sa clé dans la propriété auteur_id
   */
  public function modifierUtilisateur()
  {
    if (count($_POST) !== 0) {
      $utilisateur = $_POST;
      $oUtilisateur = new Utilisateur($utilisateur);

      $erreurs = $oUtilisateur->erreurs;
      if (count($erreurs) === 0) {
        $oUtilisateur->verifie_courrielId($oUtilisateur);
      }

      $erreurs = $oUtilisateur->erreurs;
      if (count($erreurs) === 0) {
        if ($this->oRequetesSQL->modifierUtilisateur([
          'utilisateur_id'    => $oUtilisateur->utilisateur_id,
          'utilisateur_nom'    => $oUtilisateur->utilisateur_nom,
          'utilisateur_prenom' => $oUtilisateur->utilisateur_prenom,
          'utilisateur_courriel' => $oUtilisateur->utilisateur_courriel,
          'utilisateur_profile_id' => $oUtilisateur->utilisateur_profile_id
        ])) {
          $this->messageRetourAction = "Modification de l'utilisateur numéro $this->utilisateur_id effectuée.";
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "modification de l'utilisateur numéro $this->utilisateur_id non effectuée.";
        }
        $this->profileUtilisateur($oUtilisateur);
        exit;
      }
    } else {
      // chargement initial du formulaire  
      // initialisation des champs dans la vue formulaire avec les données SQL de cet utilisateur  
      $utilisateur  = $this->oRequetesSQL->getUtilisateur($this->utilisateur_id);
      $erreurs = [];
    }

    (new Vue)->generer(
      'vAdminUtilisateurModifier',
      array(
        'oUtilisateur' => $this->oUtilisateur,
        'titre'        => "Modifier l'utilisateur numéro $this->utilisateur_id",
        'utilisateur'       => $utilisateur,
        'erreurs'      => $erreurs
      ),
      'gabarit-admin'
    );
  }

  /**
   * Supprimer un utilisateur identifié par sa clé dans la propriété utilisateur_id
   */

  public function supprimerUtilisateur()
  {
    if ($this->oRequetesSQL->supprimerUtilisateur($this->utilisateur_id)) {
      $this->messageRetourAction = "Suppression de l'utilisateur numéro $this->utilisateur_id effectuée.";
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Suppression de l'utilisateur numéro $this->utilisateur_id non effectuée.";
    }
    //a modifier
    $this->profileUtilisateur($this->utilisateur_id);
  }

  public function listerFilms()
  {
    $utilisateurs = $this->oRequetesSQL->getUtilisateurs();

    (new Vue)->generer(
      'vAdminDev',
      array(
        'oUtilisateur'        => $this->oUtilisateur,
        'titre'               => 'Gestion des films',
        'utilisateurs'        => $utilisateurs,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }

  public function listerGenres()
  {
    $utilisateurs = $this->oRequetesSQL->getUtilisateurs();

    (new Vue)->generer(
      'vAdminDev',
      array(
        'oUtilisateur'        => $this->oUtilisateur,
        'titre'               => 'Gestion des genres',
        'utilisateurs'        => $utilisateurs,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }
  public function listerRealisateurs()
  {
    $utilisateurs = $this->oRequetesSQL->getUtilisateurs();

    (new Vue)->generer(
      'vAdminDev',
      array(
        'oUtilisateur'        => $this->oUtilisateur,
        'titre'               => 'Gestion des realisateurs',
        'utilisateurs'        => $utilisateurs,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }
  public function listerActeurs()
  {
    $utilisateurs = $this->oRequetesSQL->getUtilisateurs();

    (new Vue)->generer(
      'vAdminDev',
      array(
        'oUtilisateur'        => $this->oUtilisateur,
        'titre'               => 'Gestion des acteurs',
        'utilisateurs'        => $utilisateurs,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }
  public function listerPays()
  {
    $utilisateurs = $this->oRequetesSQL->getUtilisateurs();

    (new Vue)->generer(
      'vAdminDev',
      array(
        'oUtilisateur'        => $this->oUtilisateur,
        'titre'               => 'Gestion des pays',
        'utilisateurs'        => $utilisateurs,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }
  public function listerSeances()
  {
    $utilisateurs = $this->oRequetesSQL->getUtilisateurs();

    (new Vue)->generer(
      'vAdminDev',
      array(
        'oUtilisateur'        => $this->oUtilisateur,
        'titre'               => 'Gestion des seances',
        'utilisateurs'        => $utilisateurs,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }
  public function listerSalles()
  {
    $utilisateurs = $this->oRequetesSQL->getUtilisateurs();

    (new Vue)->generer(
      'vAdminDev',
      array(
        'oUtilisateur'        => $this->oUtilisateur,
        'titre'               => 'Gestion des salles',
        'utilisateurs'        => $utilisateurs,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }
}
