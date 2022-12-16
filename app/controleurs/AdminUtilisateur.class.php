<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Utilisateur de l'application admin
 */

class AdminUtilisateur extends Admin
{

    protected $methodes = [
        'l'           => ['nom'    => 'profileUtilisateur',  'droits' => [Utilisateur::PROFIL_MEMBRE]],
        'a'           => ['nom'    => 'ajouterUtilisateur',   'droits' => [Utilisateur::PROFIL_MEMBRE]],
        'm'           => ['nom'    => 'modifierUtilisateur',  'droits' => [Utilisateur::PROFIL_MEMBRE]],
        'vt'           => ['nom'    => 'voirTimbres',  'droits' => [Utilisateur::PROFIL_MEMBRE]],
        've'           => ['nom'    => 'voirEncheres',  'droits' => [Utilisateur::PROFIL_MEMBRE]],
        'sm'           => ['nom'    => 'suivreMises',  'droits' => [Utilisateur::PROFIL_MEMBRE]],
        'se'           => ['nom'    => 'suivreEnchere',  'droits' => [Utilisateur::PROFIL_MEMBRE]],
        'ves'           => ['nom'    => 'voirEncheresSuivies',  'droits' => [Utilisateur::PROFIL_MEMBRE]],
        'nse'           => ['nom'    => 'plusSuivreEnchere',  'droits' => [Utilisateur::PROFIL_MEMBRE]],
        'c'           => ['nom'    => 'connecter',            'droits' => [Utilisateur::PROFIL_MEMBRE]],
        's'           => ['nom'    => 'supprimerUtilisateur', 'droits' => [Utilisateur::PROFIL_MEMBRE]],
        'd'           => ['nom'    => 'deconnecter'],
        'generer_mdp' => ['nom'    => 'genererMdp',           'droits' => [Utilisateur::PROFIL_MEMBRE]]
    ];

    /**
     * Constructeur qui initialise des propriétés à partir du query string
     * et la propriété oRequetesSQL déclarée dans la classe Routeur
     * 
     */
    public function __construct()
    {
        $this->utilisateur_id = $_GET['utilisateur_id'] ?? null;
        $this->enchere_id = $_GET['enchere_id'] ?? null;
        $this->oRequetesSQL = new RequetesSQL;
    }

    private $classRetour = "fait";
    private $messageRetourAction = "";
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


        $listeTimbreById = $this->oRequetesSQL->getTimbresById([
            "utilisateur_id" => $oUtilisateur->utilisateur_id
        ]);

        $listeTimbre = $this->oRequetesSQL->getTimbres();

        (new Vue)->generer(
            'admin/vProfile',
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
            'gabarits/gabarit-frontend'
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
            'frontend/vInscription',
            array(
                'oUtilisateur' => $utilisateur,
                'titre'        => 'Inscription',
                'utilisateur'       => $utilisateur,
                'erreurs'      => $erreurs
            ),
            'gabarits/gabarit-frontend'
        );
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
                $oUtilisateur = $_SESSION['oUtilisateur'];
                $profile = new AdminUtilisateur;
                $profile->profileUtilisateur($oUtilisateur);
                exit;
            } else {
                $messageErreurConnexion = "Courriel ou mot de passe incorrect.";
            }
        }

        (new Vue)->generer(
            'frontend/vConnection',
            array(
                'titre'                  => 'Connexion',
                'messageErreurConnexion' => $messageErreurConnexion
            ),
            'gabarits/gabarit-frontend'
        );
    }

    /**
     * Modifier un auteur identifié par sa clé dans la propriété utilisateur_id
     */
    public function modifierUtilisateur()
    {

        $session = $_SESSION['oUtilisateur'];

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
                    'utilisateur_id'    => $session->utilisateur_id,
                    'utilisateur_nom'    => $oUtilisateur->utilisateur_nom,
                    'utilisateur_prenom' => $oUtilisateur->utilisateur_prenom,
                    'utilisateur_courriel' => $oUtilisateur->utilisateur_courriel,
                    'utilisateur_profile_id' => $oUtilisateur->utilisateur_profile_id,
                    'utilisateur_mdp' => $oUtilisateur->utilisateur_mdp
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
            'admin/vModifierUtilisateur',
            array(
                'oUtilisateur' => $session,
                'titre'        => "Modifier l'utilisateur $session->utilisateur_prenom" . " " . "$session->utilisateur_nom",
                'erreurs'      => $erreurs
            ),
            'gabarits/gabarit-frontend'
        );
    }

    /**
     * Supprimer un utilisateur identifié par sa clé dans la propriété utilisateur_id
     */

    public function supprimerUtilisateur()
    {
        $session = $_SESSION['oUtilisateur'];

        if ($this->oRequetesSQL->supprimerUtilisateur($session->utilisateur_id)) {
            $messageRetourAction = "Suppression de l'utilisateur $session->utilisateur_prenom $session->utilisateur_nom effectuée.";
            $this->simpleDeconnecter();
        } else {
            $this->classRetour = "erreur";
            $messageRetourAction = "Suppression de l'utilisateur $session->utilisateur_prenom $session->utilisateur_nom  non effectuée.";
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
     * Déconnecter un utilisateur
     */
    public function deconnecter()
    {
        unset($_SESSION['oUtilisateur']);
        $this->connecter();
    }

    /**
     * Déconnecter un utilisateur sans retour à la page de connection
     */
    public function simpleDeconnecter()
    {
        unset($_SESSION['oUtilisateur']);
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
     * Lister les timbres de l'utilisateur
     * 
     */
    public function voirTimbres()
    {

        if (isset($_SESSION['oUtilisateur'])) {
            $session = $_SESSION['oUtilisateur'];
        } else {
            $session = null;
        }

        $timbres = $this->oRequetesSQL->getTimbresById([
            "utilisateur_id" => $session->utilisateur_id
        ]);

        (new Vue)->generer(
            "admin/vTimbresUtil",
            array(
                'titre'  => "Mes timbres",
                'oUtilisateur'        =>  $session,
                'timbres' => $timbres,
            ),
            "gabarits/gabarit-frontend"
        );
    }

    /**
     * Voir les encheres de l'utilisateur
     * 
     */
    public function voirEncheres()
    {

        if (isset($_SESSION['oUtilisateur'])) {
            $session = $_SESSION['oUtilisateur'];
        } else {
            $session = null;
        }

        $encheres = $this->oRequetesSQL->getEncheresById([
            "utilisateur_id" => $session->utilisateur_id
        ]);

        foreach ($encheres as $enchere) {
            if ($enchere["enchere_date_fin"] <= date('Y-m-d H:i:s')) {
                $this->oRequetesSQL->updateStatusToArchive($enchere["enchere_timbre_id"]);
            }
        }

        $encheres = $this->oRequetesSQL->getEncheresById([
            "utilisateur_id" => $session->utilisateur_id
        ]);

        (new Vue)->generer(
            "admin/vEncheresUtil",
            array(
                'titre'  => "Mes enchères",
                'oUtilisateur'        =>  $session,
                'encheres' => $encheres
            ),
            "gabarits/gabarit-frontend"
        );
    }

    /**
     * Suivre les mises de l'utilisateur
     * 
     */
    public function suivreMises()
    {

        if (isset($_SESSION['oUtilisateur'])) {
            $session = $_SESSION['oUtilisateur'];
        } else {
            $session = null;
        }

        $mises = $this->oRequetesSQL->getMisesById([
            "utilisateur_id" => $session->utilisateur_id
        ]);


        (new Vue)->generer(
            "admin/vMisesUtil",
            array(
                'titre'  => "Mes mises",
                'oUtilisateur'        =>  $session,
                'mises' => $mises,
            ),
            "gabarits/gabarit-frontend"
        );
    }

    /**
     * Voir les encheres suivies
     * 
     */
    public function voirEncheresSuivies()
    {

        if (isset($_SESSION['oUtilisateur'])) {
            $session = $_SESSION['oUtilisateur'];
        } else {
            $session = null;
        }

        $favoris = $this->oRequetesSQL->getFavorisById([
            "utilisateur_id" => $session->utilisateur_id
        ]);


        (new Vue)->generer(
            "admin/vEncheresSuiviesUtil",
            array(
                'titre'  => "Les enchères que je suis",
                'oUtilisateur'        =>  $session,
                'favoris' => $favoris,
            ),
            "gabarits/gabarit-frontend"
        );
    }



    /**
     * Suivre un enchere(favoris) par l'utilisateur
     * 
     */
    public function suivreEnchere()
    {

        if (isset($_SESSION['oUtilisateur'])) {
            $session = $_SESSION['oUtilisateur'];
        } else {
            $session = null;
        }

        $ids = ["favoris_utilisateur_id" => $session->utilisateur_id, "favoris_enchere_id" =>  $this->enchere_id];

        $oFavoris = new Favoris($ids);

        $listeFavoris = $this->oRequetesSQL->getFavoris();

        $exist = false;

        $erreurs = $oFavoris->erreurs;
        if (count($erreurs) === 0) {
            foreach ($listeFavoris as $favoris) {
                if ($favoris['favoris_utilisateur_id'] == $session->utilisateur_id && $favoris['favoris_enchere_id'] == $this->enchere_id) {
                    $exist = true;
                    exit;
                };
            }
            if ($exist == false) {
                $this->oRequetesSQL->ajouterFavoris([
                    "favoris_utilisateur_id" => $oFavoris->favoris_utilisateur_id,
                    "favoris_enchere_id" => $oFavoris->favoris_enchere_id
                ]);
            }
        }

        $retourFiche = new Frontend;
        $retourFiche->afficherFiche();
    }


    /**
     *  Arreter de suivre un enchere (favoris) par l'utilisateur
     * 
     */
    public function plusSuivreEnchere()
    {

        if (isset($_SESSION['oUtilisateur'])) {
            $session = $_SESSION['oUtilisateur'];
        } else {
            $session = null;
        }

        $ids = ["favoris_utilisateur_id" => $session->utilisateur_id, "favoris_enchere_id" =>  $this->enchere_id];

        $oFavoris = new Favoris($ids);

        $erreurs = $oFavoris->erreurs;
        if (count($erreurs) === 0) {
            $this->oRequetesSQL->supprimerFavoris([
                "favoris_utilisateur_id" => $oFavoris->favoris_utilisateur_id,
                "favoris_enchere_id" => $oFavoris->favoris_enchere_id
            ]);
        }

        $retourFiche = new Frontend;
        $retourFiche->afficherFiche();
    }
}
