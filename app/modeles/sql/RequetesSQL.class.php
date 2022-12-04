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
   * Récupération des enchères
   * @param  string $critere
   * @return array tableau des lignes produites par la select   
   */
  public function getEncheres()
  {
    $this->sql =
      "SELECT *
    FROM enchere
    INNER JOIN timbre ON enchere_timbre_id = timbre_id
    INNER JOIN pays ON timbre_pays_id = pays_id
    INNER JOIN `condition` ON timbre_condition_id = condition_id
    INNER JOIN mise ON mise_enchere_id = enchere_id
    INNER JOIN image ON image_timbre_id = timbre_id
    GROUP BY timbre_id";

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
       INNER JOIN mise ON mise_enchere_id = enchere_id
       INNER JOIN utilisateur ON enchere_utilisateur_id = utilisateur_id
        WHERE enchere_id = :enchere_id AND timbre_status_id = " . Timbre::STATUT_ACTIF;

    return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
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
      UPDATE utilisateur SET utilisateur_nom = :utilisateur_nom, utilisateur_prenom = :utilisateur_prenom, utilisateur_courriel = :utilisateur_courriel, utilisateur_profile_id = :utilisateur_profile_id
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
