<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Image de l'application admin
 */

class AdminImage extends Admin
{
  protected $methodes = [
    'a'           => ['nom'    => 'ajouterImage',  'droits' => [Utilisateur::PROFIL_MEMBRE]]
  ];

  /**
   * Ajouter un image sur le server et le lien dans la banque de données
   */
  public function ajouterImage()
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
        'admin/vProfile',
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
        'gabarits/gabarit-frontend'
      );
    }
  }
}
