<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Timbre de l'application admin
 */

class AdminTimbre extends Admin
{
    protected $methodes = [
        'a'           => ['nom'    => 'ajouterTimbre',  'droits' => [Utilisateur::PROFIL_MEMBRE]],
        'm'           => ['nom'    => 'modifierTimbre',  'droits' => [Utilisateur::PROFIL_MEMBRE]]
    ];

    /**
     * Constructeur qui initialise des propriétés à partir du query string
     * et la propriété oRequetesSQL déclarée dans la classe Routeur
     * 
     */
    public function __construct()
    {
        $this->timbre_id = $_GET['timbre_id'] ?? null;
        $this->oRequetesSQL = new RequetesSQL;
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
        $erreurImage = "";
        $nom_fichier = "";

        if (isset($_SESSION['oUtilisateur'])) {
            $session = $_SESSION['oUtilisateur'];
        } else {
            $session = null;
        }

        // $timbre = $_POST;

        $listeTimbreById = $this->oRequetesSQL->getTimbresById([
            "utilisateur_id" => $session->utilisateur_id
        ]);

        $listeTimbre = $this->oRequetesSQL->getTimbres();

        if (count($_POST) !== 0) {

            $date = new DateTimeImmutable();

            if ($_FILES) {
                $nom_fichier = $_FILES['userfile']['name'];
                $fichier = $_FILES['userfile']['tmp_name'];
                $taille = $_FILES['userfile']['size'];
            }
            $ajouter = "";
            $image_lien = "assets/images/timbres/" . $date->getTimestamp() . "_" . $nom_fichier;

            $image_lienArray = ["image_lien" => $image_lien];

            $timbre = array_merge($image_lienArray, $_POST);

            unset($timbre["userfile"]);

            $oTimbre = new Timbre($timbre);

            $erreurs = $oTimbre->erreurs;

            if (count($erreurs) === 0) {
                if (move_uploaded_file($fichier, "assets/images/timbres/" . $date->getTimestamp() . "_" . $nom_fichier)) {
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

                    $listeTimbreById = $this->oRequetesSQL->getTimbresById([
                        "utilisateur_id" => $session->utilisateur_id
                    ]);

                    $timbre_id = end($listeTimbreById)["timbre_id"];


                    $nouvelleImage = $this->oRequetesSQL->ajouterImage([
                        'image_nom'    => $oTimbre->image_nom,
                        'image_lien' => $oTimbre->image_lien,
                        'image_timbre_id' => $timbre_id
                    ]);

                    $succesTimbre = "Ajout de timbre réalisée avec succès";


                    if ($nouvelleImage > 0) { // test de la clé de l'utilisateur ajouté

                    }
                } else {
                    $erreurImage = "Vous devez inclure une image";
                }
            }
            $listeTimbreById = $this->oRequetesSQL->getTimbresById([
                "utilisateur_id" => $session->utilisateur_id
            ]);
            if ($succesTimbre) {
                $timbre = "";
            }
            (new Vue)->generer(
                'admin/vProfile',
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
                    'erreurImage'         => $erreurImage
                ),
                'gabarits/gabarit-frontend'
            );
        }
        (new Vue)->generer(
            'admin/vProfile',
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
                'erreurImage'         => $erreurImage
            ),
            'gabarits/gabarit-frontend'
        );
    }

    /**
     * Modifier un timbre
     */
    public function modifierTimbre()
    {
        if (isset($_SESSION['oUtilisateur'])) {
            $session = $_SESSION['oUtilisateur'];
        } else {
            $session = null;
        }

        $pays = $this->oRequetesSQL->getPays();
        $condition =  $this->oRequetesSQL->getCondition();
        $status =  $this->oRequetesSQL->getStatus();
        $this->messageRetourAction = "";

        $erreurs = [];

        $timbre = $this->oRequetesSQL->getTimbre(
            $this->timbre_id
        );

        if (count($_POST) !== 0) {
            $timbre = $_POST;

            $oTimbre = new Timbre($timbre);

              $erreurs = $oTimbre->erreurs;
              if (count($erreurs) === 0) {
                if ($this->oRequetesSQL->modifierTimbre([
                    'timbre_id'     => $oTimbre->timbre_id,
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
                    'timbre_utilisateur_id' => $oTimbre->timbre_utilisateur_id
                ])) {
                    $this->messageRetourAction = "Modification du timbre numéro $oTimbre->timbre_id effectuée.";
                } else {
                    $this->classRetour = "erreur";
                    $this->messageRetourAction = "modification du timbre numéro $oTimbre->timbre_id non effectuée.";
                }

            }
        }

        (new Vue)->generer(
            'admin/vModifierTimbre',
            [
                'oUtilisateur'        =>  $session,
                'titre'       => "Modifier le timbre numéro $this->timbre_id",
                'timbre' => $timbre,
                'pays'                => $pays,
                'condition'           => $condition,
                'status'              => $status,
                'erreurs'     => $erreurs,
                'messageRetourAction' => $this->messageRetourAction
            ],
            'gabarits/gabarit-frontend'
        );
    }
}
