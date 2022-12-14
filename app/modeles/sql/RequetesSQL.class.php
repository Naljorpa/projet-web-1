<?php

/**
 * Classe des requêtes SQL
 *
 */
class RequetesSQL extends RequetesPDO
{


  /* GESTION DES TIMBRES 
     ======================== */

  /**
   * Récupération de tous les pays de la table pays
   * @return array tableau des lignes produites par la select
   */
  public function getPays()
  {
    $this->sql =
      "SELECT *
         FROM pays
       ";

    return $this->getLignes();
  }

  /**
   * Récupération de toutes les condition de la table condition
   * @return array tableau des lignes produites par la select
   */
  public function getCondition()
  {
    $this->sql =
      "SELECT *
         FROM `condition`
       ";

    return $this->getLignes();
  }



  /**
   * Récupération de tous les status de la table status
   * @return array tableau des lignes produites par la select
   */
  public function getStatus()
  {
    $this->sql =
      "SELECT *
         FROM status
       ";

    return $this->getLignes();
  }

  /**
   * Récupération des enchères
   * @return array tableau des lignes produites par la select   
   */
  public function getEncheres()
  {

    $this->sql =
      "SELECT *
      FROM enchere
      INNER JOIN timbre ON enchere_timbre_id = timbre_id
      INNER JOIN pays ON timbre_pays_id = pays_id
      LEFT OUTER JOIN mise ON enchere_id = mise_enchere_id and mise_valeur =(select max(mise_valeur) from mise where enchere_id = mise_enchere_id) 
      INNER JOIN `condition` ON timbre_condition_id = condition_id
      INNER JOIN image ON image_timbre_id = timbre_id
      GROUP BY timbre_id
    ";

    return $this->getLignes();
  }


  /**
   * Update le status du timbre
   * @return array tableau des lignes produites par la select   
   */
  public function updateStatusToArchive($enchere_timbre_id)
  {

    $this->sql =
      "UPDATE timbre SET timbre_status_id = 3 WHERE timbre_id = $enchere_timbre_id
    ";

    return $this->getLignes();
  }



  /**
   * Récupération d'une fiche timbre
   * @param int $enchere_id, clé de l'enchère 
   * @return array|false tableau associatif de la ligne produite par la select, false si aucune ligne  
   */
  public function getFiche($enchere_id)
  {
    $this->sql =
      "SELECT *
       FROM enchere
       INNER JOIN timbre ON enchere_timbre_id = timbre_id
       INNER JOIN pays ON timbre_pays_id = pays_id
       INNER JOIN `condition` ON timbre_condition_id = condition_id
       LEFT OUTER JOIN mise ON mise_enchere_id = enchere_id
       INNER JOIN utilisateur ON enchere_utilisateur_id = utilisateur_id
        WHERE enchere_id = :enchere_id ";

    return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Récupération des timbres par l'id utilisateur
   * @param array $champs tableau des champs
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function getTimbresById($champs)
  {
    $this->sql =
      "SELECT timbre_id, timbre_nom, timbre_description, timbre_status_id, timbre_tirage
      FROM timbre
      WHERE timbre_utilisateur_id = :utilisateur_id
    ";

    return $this->getLignes($champs);
  }

   /**
   * Récupération des timbres par l'id du timbre
   * @param array $champs tableau des champs
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function getTimbre($champs)
  {
    $this->sql =
      "SELECT * 
      FROM timbre
       INNER JOIN pays ON timbre_pays_id = pays_id
       INNER JOIN `condition` ON timbre_condition_id = condition_id
      WHERE timbre_id = :timbre_id
    ";

    return $this->getLignes($champs);
  }

  /**
   * Récupération de tous les timbres
   * @param array $champs tableau des champs
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function getTimbres()
  {
    $this->sql =
      "SELECT timbre_id, timbre_nom
      FROM timbre
      
    ";

    return $this->getLignes();
  }

  /**
   * Ajouter une timbre
   * @param array $champs tableau des champs du timbre
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterTimbre($champs)
  {
    $this->sql = '
      INSERT INTO timbre SET timbre_nom = :timbre_nom, timbre_annee = :timbre_annee, timbre_description = :timbre_description, timbre_histoire = :timbre_histoire, timbre_dimension = :timbre_dimension,timbre_certification = :timbre_certification, timbre_couleur = :timbre_couleur, timbre_tirage = :timbre_tirage, timbre_pays_id = :timbre_pays_id, timbre_condition_id = :timbre_condition_id, timbre_status_id = :timbre_status_id, timbre_utilisateur_id = :timbre_utilisateur_id';
    return $this->CUDLigne($champs);
  }

  /* GESTION DES IMAGES 
     ======================== */


  /**
   * Récupération des image par l'id de l'enchère
   * @param array $enchere_id tableau des champs
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function getImages($enchere_id)
  {
    $this->sql =
      "SELECT image_lien, image_nom, image_timbre_id
         FROM image
         INNER JOIN timbre ON image_timbre_id = timbre_id
         INNER JOIN enchere ON enchere_timbre_id = timbre_id
         WHERE enchere_id = :enchere_id
    
     ";

    return $this->getLignes(['enchere_id' => $enchere_id]);
  }


  /**
   * Ajouter une image
   * @param array $champs tableau des champs du image
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterImage($champs)
  {
    $this->sql = '
      INSERT INTO image SET image_nom = :image_nom, image_lien = :image_lien, image_timbre_id = :image_timbre_id';
    return $this->CUDLigne($champs);
  }


  /* GESTION DES MISES 
     ======================== */


  /**
   * Récupération des mise par l'id de l'enchère
   * @param array $enchere_id tableau des champs
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */

  public function getMise($enchere_id)
  {
    $this->sql =
      "SELECT MAX(mise_valeur), mise_utilisateur_id, mise_enchere_id
    FROM mise
    where :enchere_id = mise_enchere_id
   group by mise_enchere_id 
  ";

    return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Récupération des mise par l'id de l'utilisateur
   * @param array $utilisateur_id tableau des champs
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */

  public function getMisesById($champs)
  {
    $this->sql =
      "

       SELECT MAX(mise_valeur) as mise_max, mise_utilisateur_id, mise_enchere_id, timbre_nom, enchere_id, enchere_prix_base, enchere_date_fin, timbre_status_id, image_lien, image_nom, (select MAX(mise_valeur) from mise where mise_enchere_id = enchere_id) as mise_actuel
       FROM mise
       INNER JOIN enchere ON mise_enchere_id = enchere_id
       INNER JOIN timbre ON enchere_timbre_id = timbre_id
       INNER JOIN image ON image_timbre_id = timbre_id
       where :utilisateur_id = mise_utilisateur_id
      group by mise_enchere_id 
   ";

    return $this->getLignes($champs);
  }


  /**
   * Ajouter une mise
   * @param array $champs tableau des champs de la mise
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterMise($champs)
  {
    $this->sql = '
      INSERT INTO mise SET mise_utilisateur_id = :mise_utilisateur_id, mise_enchere_id = :mise_enchere_id, mise_valeur = :mise_valeur';
    return $this->CUDLigne($champs);
  }


  /* GESTION DES ENCHERES
     ======================== */


  /**
   * Ajouter une enchere
   * @param array $champs tableau des champs de l'enchere
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function  ajouterEnchere($champs)
  {
    $this->sql = '
      INSERT INTO enchere SET enchere_prix_base = :enchere_prix_base, enchere_date_debut = :enchere_date_debut, enchere_date_fin = :enchere_date_fin, enchere_timbre_id = :enchere_timbre_id, enchere_utilisateur_id = :enchere_utilisateur_id';
    return $this->CUDLigne($champs);
  }

  /**
   * Récupération des encheres par l'id utilisateur
   * @param array $champs tableau des champs
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function getEncheresById($champs)
  {
    $this->sql =
      "SELECT *
      FROM enchere
      INNER join timbre on timbre_id = enchere_timbre_id
      WHERE enchere_utilisateur_id = :utilisateur_id
    ";

    return $this->getLignes($champs);
  }

  /**
   * Récupération des l'enchere par l'id de la mise_enchere_id
   * @param array $mise_enchere_id tableau des champs
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */

   public function getEnchere($mise_enchere_id)
   {
     $this->sql =
       "SELECT *
     FROM enchere
     where enchere_id = :mise_enchere_id 
    group by enchere_id 
   ";
 
     return $this->getLignes(['mise_enchere_id' => $mise_enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
   }


  /* GESTION DES UTILISATEURS 
     ======================== */

  /**
   * Connecter un utilisateur
   * @param array $champs, tableau avec les champs utilisateur_courriel et utilisateur_mdp  
   * @return array|false ligne de la table, false sinon 
   */
  public function connecter($champs)
  {
    $this->sql = "
      SELECT utilisateur_id, utilisateur_nom, utilisateur_prenom, utilisateur_courriel, utilisateur_profile_id
      FROM utilisateur
      WHERE utilisateur_courriel = :utilisateur_courriel AND utilisateur_mdp = SHA2(:utilisateur_mdp, 512)";

    return $this->getLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Récupération de tous les utilisateurs de la table utilisateur
   * @return array tableau des lignes produites par la select
   */
  public function getUtilisateurs()
  {
    $this->sql = '
      SELECT utilisateur_id, utilisateur_nom, utilisateur_prenom, utilisateur_courriel, utilisateur_profile_id FROM utilisateur
      ORDER BY utilisateur_id DESC';
    return $this->getLignes();
  }

  /**
   * Récupération d'un utilisateur de la table utilisateur
   * @param int $utilisateur_id 
   * @return array|false tableau associatif de la ligne produite par la select, false si aucune ligne
   */
  public function getUtilisateur($utilisateur_id)
  {
    $this->sql = '
      SELECT utilisateur_id, utilisateur_nom, utilisateur_prenom, utilisateur_courriel, utilisateur_profile_id FROM utilisateur WHERE utilisateur_id = :utilisateur_id';
    return $this->getLignes(['utilisateur_id' => $utilisateur_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Ajouter un utilisateur
   * @param array $champs tableau des champs de l'utilisateur 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterUtilisateur($champs)
  {
    $this->sql = '
      INSERT INTO utilisateur SET utilisateur_nom = :utilisateur_nom, utilisateur_prenom = :utilisateur_prenom, utilisateur_courriel = :utilisateur_courriel, utilisateur_profile_id = :utilisateur_profile_id, utilisateur_mdp = SHA2(:utilisateur_mdp, 512)';
    return $this->CUDLigne($champs);
  }

  /**
   * Modifier un utilisateur
   * @param array $champs tableau avec les champs à modifier et la clé utilisateur_id
   * @return boolean true si modification effectuée, false sinon
   */
  public function modifierUtilisateur($champs)
  {
    $this->sql = '
      UPDATE utilisateur SET utilisateur_nom = :utilisateur_nom, utilisateur_prenom = :utilisateur_prenom, utilisateur_courriel = :utilisateur_courriel, utilisateur_profile_id = :utilisateur_profile_id,
      utilisateur_mdp = SHA2( :utilisateur_mdp, 512)
      WHERE utilisateur_id = :utilisateur_id';
    return $this->CUDLigne($champs);
  }

  /**
   * Supprimer un utilisateur
   * @param int $utilisateur_id clé primaire
   * @return boolean true si suppression effectuée, false sinon
   */
  public function supprimerUtilisateur($utilisateur_id)
  {
    $this->sql = '
      DELETE FROM utilisateur WHERE utilisateur_id = :utilisateur_id';
    return $this->CUDLigne(['utilisateur_id' => $utilisateur_id]);
  }

  /**
   * @param array $champs tableau avec les champs mdp a modifier et la clé utilisateur_id
   * @return boolean true si modification effectuée, false sinon
   */

  public function modifierMdp($champs)
  {
    $this->sql = '
    UPDATE utilisateur SET utilisateur_mdp = SHA2( :utilisateur_mdp, 512) WHERE 
    utilisateur_id = :utilisateur_id';
    return $this->CUDLigne($champs);
  }

  /**
   * Vérifie si le courriel existe
   * @param string $utilisateur_courriel
   * @return array du courriel en question ou rien sinon
   */

  public function verifieCourriel($utilisateur_courriel)
  {
    $this->sql = '
    SELECT utilisateur_courriel FROM utilisateur WHERE utilisateur_courriel = :utilisateur_courriel';
    return $this->getLignes(['utilisateur_courriel' => $utilisateur_courriel], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Vérifie si le courriel existe sauf s'il est associé avec le id passé.
   * @param int $utilisateur_id
   * @return array du courriel en question ou rien sinon
   */

  public function verifie_courrielId($utilisateur_courriel, $utilisateur_id)
  {
    $this->sql = '
    SELECT utilisateur_courriel FROM utilisateur WHERE utilisateur_courriel = :utilisateur_courriel AND utilisateur_id != :utilisateur_id';
    return $this->getLignes(['utilisateur_courriel' => $utilisateur_courriel, 'utilisateur_id' => $utilisateur_id], RequetesPDO::UNE_SEULE_LIGNE);
  }
}
