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
        'nom' => 'profileUtilisateur'
      ],
      'a' => [
        'nom' => 'ajouterUtilisateur'
      ],
      'c' => [
        'nom' => 'connecter'
      ],
      'm' =>  [
        'nom' => 'modifierUtilisateur'
      ],
      's' => [
        'nom' => 'supprimerUtilisateur',
        'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR]
      ],
      'd' => [
        'nom' => 'deconnecter'
      ],
      'genererMdp' =>  [
        'nom' => 'genererMdp',
        // 'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR]
      ]
    ],

    'mise' => [
      'a' => [
        'nom' => 'ajouterMise'
      ],
      'af' => [
        'nom' => 'ajouterMiseFiche'
      ],
      'l' => [
        'nom' => 'listerMise'
      ]
    ],

    'timbre' => [
      'a' => [
        'nom' => 'ajouterTimbre'
      ]
    ],
    'enchere' => [
      'a' => [
        'nom' => 'ajouterEnchere'
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
            //possiblement enlev/ la session  dans methode utiliser pour debug
            $this->$methode($_SESSION['oUtilisateur']);
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
   * Affiche le profile de l'utilisateur
   */
  public function profileUtilisateur($oUtilisateur)
  {
    $pays = $this->oRequetesSQL->getPays();
    $condition =  $this->oRequetesSQL->getCondition();
    $status =  $this->oRequetesSQL->getStatus();


    if (isset($this->oUtilisateur)) {
      $oUtilisateur = $this->oUtilisateur;
    } else {
      $oUtilisateur = $oUtilisateur;
    }


    $listeTimbreById = $this->oRequetesSQL->getTimbres([
      "utilisateur_id" => $oUtilisateur->utilisateur_id
    ]);

    $listeTimbre = $this->oRequetesSQL->getTimbres();

    (new Vue)->generer(
      'vProfile',
      array(
        'oUtilisateur'        => $oUtilisateur,
        'titre'               => 'Profile d\'utilisateur',
        'pays'                => $pays,
        'condition'           => $condition,
        'status'              => $status,
        'listeTimbreById'     => $listeTimbreById,
        'listeTimbre'         => $listeTimbre,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-frontend'
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
   * Modifier un auteur identifié par sa clé dans la propriété utilisateur_id
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
      'gabarit-frontend'
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

  /**
   * Ajouter un timbre
   */
  public function ajouterTimbre()
  {

    $pays = $this->oRequetesSQL->getPays();
    $condition =  $this->oRequetesSQL->getCondition();
    $status =  $this->oRequetesSQL->getStatus();

    $timbre  = [];
    $erreurs = [];
    $succesTimbre = "";

    if (isset($_SESSION['oUtilisateur'])) {
      $session = $_SESSION['oUtilisateur'];
    } else {
      $session = null;
    }

    $timbre = $_POST;

    $listeTimbreById = $this->oRequetesSQL->getTimbresById([
      "utilisateur_id" => $session->utilisateur_id
    ]);

    $listeTimbre = $this->oRequetesSQL->getTimbres();

    if (count($_POST) !== 0) {

      $oTimbre = new Timbre($timbre);

      $erreurs = $oTimbre->erreurs;

      if (count($erreurs) === 0) {

        $timbre_id = $this->oRequetesSQL->ajouterTimbre([
          'timbre_nom'    => $oTimbre->timbre_nom,
          'timbre_annee' => $oTimbre->timbre_annee,
          'timbre_description' => $oTimbre->timbre_description,
          'timbre_histoire' => $oTimbre->timbre_histoire,
          'timbre_dimension' => $oTimbre->timbre_dimension,
          'timbre_certification' => $oTimbre->timbre_certification,
          'timbre_couleur' => $oTimbre->timbre_couleur,
          'timbre_tirage' => $oTimbre->timbre_tirage,
          'timbre_pays_id' => $oTimbre->timbre_pays_id,
          'timbre_condition_id' => $oTimbre->timbre_condition_id,
          'timbre_status_id' => $oTimbre->timbre_status_id,
          'timbre_utilisateur_id' => $oTimbre->timbre_utilisateur_id
        ]);
        $succesTimbre = "Ajout de timbre réalisée avec succès";
      }
      $listeTimbreById = $this->oRequetesSQL->getTimbresById([
        "utilisateur_id" => $session->utilisateur_id
      ]);
      if($succesTimbre){
        $timbre = "";
      }
      (new Vue)->generer(
        'vProfile',
        array(
          'oUtilisateur'        =>  $session,
          'titre'               => 'Profile d\'utilisateur',
          'pays'                => $pays,
          'condition'           => $condition,
          'status'              => $status,
          'succes'              => $succesTimbre,
          'timbre'              => $timbre,
          'listeTimbre'         => $listeTimbre,
          'listeTimbreById'     => $listeTimbreById,
          'erreurs'             => $erreurs,
          'classRetour'         => $this->classRetour,
          'messageRetourAction' => $this->messageRetourAction
        ),
        'gabarit-frontend'
      );
    }
    (new Vue)->generer(
      'vProfile',
      array(
        'oUtilisateur'        =>  $session,
        'titre'               => 'Profile d\'utilisateur',
        'pays'                => $pays,
        'condition'           => $condition,
        'status'              => $status,
        'timbre'              => $timbre,
        'listeTimbre'         => $listeTimbre,
        'listeTimbreById'     => $listeTimbreById,
        'succes'              => $succesTimbre,
        'erreurs'             => $erreurs,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-frontend'
    );
  }

  /**
   * Ajouter un image sur le server et le lien dans la banque de données
   */
  public function traitementImage()
  {
    $ajouterImage = "";
    $erreurImage = "";

    if (count($_POST) !== 0) {


      $date = new DateTimeImmutable();
      $nom_fichier = $_FILES['userfile']['name'];
      $fichier = $_FILES['userfile']['tmp_name'];
      $taille = $_FILES['userfile']['size'];
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

      // $image_nom = $_POST["image_nom"];
      // $image_timbre_id = $_POST["image_timbre_id"];
      $image_lien = "assets/images/timbres/" . $date->getTimestamp() . "_" . $nom_fichier;

      $image_lienArray = ["image_lien" => $image_lien];

      $image = array_merge($image_lienArray, $_POST);

      $oImage = new Image($image);

      $erreurs = $oImage->erreurs;


      if (count($erreurs) === 0) {

        if (move_uploaded_file($fichier, "assets/images/timbres/" . $date->getTimestamp() . "_" . $nom_fichier)) {
          $nouvelleImage = $this->oRequetesSQL->ajouterImage([
            'image_nom'    => $oImage->image_nom,
            'image_lien' => $oImage->image_lien,
            'image_timbre_id' => $oImage->image_timbre_id
          ]);

          if ($nouvelleImage > 0) { // test de la clé de l'utilisateur ajouté
            $ajouterImage = "Image ajouté avec success";
          }
        } else {
          $erreurImage = "Vous devez inclure une image";
        }
      }

      (new Vue)->generer(
        'vProfile',
        array(
          'oUtilisateur'        =>  $session,
          'titre'               => 'Profile d\'utilisateur',
          'image'               => $image,
          'pays'                => $pays,
          'condition'           => $condition,
          'status'              => $status,
          'listeTimbre'         => $listeTimbre,
          'listeTimbreById'     => $listeTimbreById,
          'erreurs'             => $erreurs,
          'erreurImage'         => $erreurImage,
          'ajouterImage'        => $ajouterImage
        ),
        'gabarit-frontend'
      );
    }
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
