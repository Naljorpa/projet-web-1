<?php 

/**
 * Classe de l'entité Enchère
 *
 */
class Enchere
{

    private $enchere_id;
    private $enchere_prix_base;
    private $enchere_date_debut;
    private $enchere_date_fin;
    private $enchere_timbre_id;
    private $enchere_utilisateur_id;
    

    private $erreurs = [];

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

  // Getters explicites nécessaires au moteur de templates TWIG
  public function getEnchere_id()
  {
    return $this->enchere_id;
  }
  public function getEnchere_prix_base()
  {
    return $this->enchere_prix_base;
  }
  public function getEnchere_date_debut()
  {
    return $this->enchere_date_debut;
  }
  public function getEnchere_date_fin()
  {
    return $this->enchere_date_fin;
  }
  public function getEnchere_timbre_id()
  {
    return $this->enchere_timbre_id;
  }
  public function getEnchere_utilisateur_id()
  {
    return $this->enchere_utilisateur_id;
  }
  
  public function getErreurs()
  {
    return $this->erreurs;
  }
  
  /**
   * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
   * @param string $prop, nom de la propriété
   * @param $val, contenu de la propriété à mettre à jour
   */   
  public function __set($prop, $val) {
    $setProperty = 'set'.$prop;
    $this->$setProperty($val);
  }

/**
   * Mutateur de la propriété enchere_id
   * @param int $enchere_id
   * @return $this
   */    
  public function setEnchere_id($enchere_id) {
    unset($this->erreurs['enchere_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $enchere_id)) {
      $this->erreurs['enchere_id'] = 'Numéro utilisateur incorrect.';
    }
    $this->enchere_id= $enchere_id;
    return $this;
  }    

  /**
   * Mutateur de la propriété enchere_prix_base 
   * @param int $enchere_prix_base
   * @return $this
   */    
  public function setEnchere_prix_base($enchere_prix_base) {
    unset($this->erreurs['enchere_prix_base']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $enchere_prix_base)) {
      $this->erreurs['enchere_prix_base'] = 'Doit être un nombre';
    }
    $this->enchere_prix_base = $enchere_prix_base;
    return $this;
  }   

   /**
   * Mutateur de la propriété enchere_date_debut 
   * @param string $enchere_date_debut
   * @return $this
   */    
  public function setEnchere_date_debut($enchere_date_debut) {
    unset($this->erreurs['enchere_date_debut']);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $enchere_date_debut)){
      $this->erreurs['enchere_date_debut'] = 'Doit être de format valide.';
    }
    $this->enchere_date_debut = $enchere_date_debut;
    return $this;
  }   

   /**
   * Mutateur de la propriété enchere_date_fin 
   * @param string $enchere_date_fin
   * @return $this
   */    
  public function setEnchere_date_fin($enchere_date_fin) {
    unset($this->erreurs['enchere_date_fin']);
    $enchere_date_debut = new DateTime("now", new DateTimeZone('America/New_York'));
    $newEnchere_date_fin = new DateTime($enchere_date_fin);
    if($newEnchere_date_fin <= $enchere_date_debut){
      $this->erreurs['enchere_date_fin'] = "Doit être supérieur à la date d'aujourd'hui";
    }
    $this->enchere_date_fin = $enchere_date_fin;
    return $this;
  }   
  
/**
   * Mutateur de la propriété enchere_timbre_id
   * @param int $enchere_timbre_id
   * @return $this
   */    
  public function setEnchere_timbre_id($enchere_timbre_id) {
    unset($this->erreurs['enchere_timbre_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $enchere_timbre_id)) {
      $this->erreurs['enchere_timbre_id'] = 'Numéro de timbre incorrect.';
    }
    $this->enchere_timbre_id= $enchere_timbre_id;
    return $this;
  }  
/**
   * Mutateur de la propriété enchere_utilisateur_id
   * @param int $enchere_utilisateur_id
   * @return $this
   */    
  public function setEnchere_utilisateur_id($enchere_utilisateur_id) {
    unset($this->erreurs['enchere_utilisateur_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $enchere_utilisateur_id)) {
      $this->erreurs['enchere_utilisateur_id'] = 'Numéro de utilisateur incorrect.';
    }
    $this->enchere_utilisateur_id= $enchere_utilisateur_id;
    return $this;
  }  


}
