<?php

/**
 * Classe de l'entité Timbre
 *
 */
class Timbre
{
  private $timbre_id;
  private $timbre_nom;
  private $timbre_annee;
  private $timbre_description;
  private $timbre_histoire;
  private $timbre_dimension;
  private $timbre_certification;
  private $timbre_couleur;
  private $timbre_tirage;
  private $timbre_pays_id;
  private $timbre_condition_id;
  private $timbre_status_id;
  private $timbre_utilisateur_id;

  
  private $erreurs = [];

  const STATUT_ACTIF = 1;
  const STATUT_INACTIF   = 2;
  const STATUT_ARCHIVE   = 3;

  /**
   * Constructeur de la classe 
   * @param array $proprietes, tableau associatif des propriétés 
   */ 
  public function __construct($proprietes = []) {
    $t = array_keys($proprietes);
    foreach ($t as $nom_propriete) {
      $this->__set($nom_propriete, $proprietes[$nom_propriete]);
    } 
  }
  
  /**
   * Accesseur magique d'une propriété de l'objet
   * @param string $prop, nom de la propriété
   * @return property value
   */     
  public function __get($prop) {
    return $this->$prop;
  }
  
  /**
   * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
   * @param string $prop, nom de la propriété
   * @param $val, contenu de la propriété à mettre à jour
   */   
  public function __set($prop, $val) {
    $setProperty = 'set'.ucfirst($prop);
    $this->$setProperty($val);
  }

 /**
   * Mutateur de la propriété timbre_id 
   * @param int $timbre_id
   * @return $this
   */    
  public function setTimbre_id($timbre_id) {
    unset($this->erreurs['timbre_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $timbre_id)) {
      $this->erreurs['timbre_id'] = 'Numéro de timbre incorrect.';
    }
    $this->timbre_id = $timbre_id;
    return $this;
  }    

  /**
   * Mutateur de la propriété timbre_nom 
   * @param string $timbre_nom
   * @return $this
   */    
  public function setTimbre_nom($timbre_nom) {
    unset($this->erreurs['timbre_nom']);
    $timbre_nom = trim($timbre_nom);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $timbre_nom)) {
      $this->erreurs['timbre_nom'] = 'Au moins un caractère.';
    }
    $this->timbre_nom = mb_strtoupper($timbre_nom);
    return $this;
  }

  /**
   * Mutateur de la propriété timbre_annee 
   * @param int $timbre_annee
   * @return $this
   */        
  public function setTimbre_annee($timbre_annee) {
    unset($this->erreurs['timbre_annee']);
    if (!preg_match('/^\d+$/', $timbre_annee) ||
        $timbre_annee > date("Y")) {
      $this->erreurs['timbre_annee'] = "Doit être format valide et avant l'année en cours.";
    }
    $this->timbre_annee = $timbre_annee;
    return $this;
  }

  /**
   * Mutateur de la propriété timbre_description
   * @param string $timbre_description
   * @return $this
   */    
  public function setTimbre_description($timbre_description) {
    unset($this->erreurs['timbre_description']);
    $timbre_description = trim($timbre_description);
    $regExp = '/^\S+(\s+\S+){4,}$/';
    if (!preg_match($regExp, $timbre_description)) {
      $this->erreurs['timbre_description'] = 'Au moins 5 mots.';
    }
    $this->timbre_description = $timbre_description;
    return $this;
  }

  /**
   * Mutateur de la propriété film_affiche
   * @param string $film_affiche
   * @return $this
   */    
  // public function setFilm_affiche($film_affiche) {
  //   unset($this->erreurs['film_affiche']);
  //   $film_affiche = trim($film_affiche);
  //   $regExp = '/^.+\.jpg$/';
  //   if (!preg_match($regExp, $film_affiche)) {
  //     $this->erreurs['film_affiche'] = "Vous devez téléverser un fichier de type jpg.";
  //   }
  //   $this->film_affiche = $film_affiche;
  //   return $this;
  // }

  /**
   * Mutateur de la propriété timbre_status_id
   * @param int $timbre_status_id
   * @return $this
   */        
  public function setTimbre_status_id($timbre_status_id) {
    unset($this->erreurs['timbre_status_id']);
    if ($timbre_status_id != Timbre::STATUT_ACTIF &&
        $timbre_status_id != Timbre::STATUT_INACTIF   && 
        $timbre_status_id != Timbre::STATUT_ARCHIVE) {
      $this->erreurs['timbre_status_id'] = 'Status incorrect.';
    }
    $this->timbre_status_id = $timbre_status_id;
    return $this;
  }

  /**
   * Mutateur de la propriété timbre_certification
   * @param int $timbre_certification
   * @return $this
   */    
  public function setTimbre_certification($timbre_certification) {
    unset($this->erreurs['timbre_certification']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $timbre_certification)) {
      $this->erreurs['timbre_certification'] = 'Numéro de genre incorrect.';
    }
    $this->timbre_certification= $timbre_certification;
    return $this;
  }    
}