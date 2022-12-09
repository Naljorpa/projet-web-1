<?php 

/**
 * Classe de l'entité Image
 *
 */
class Image
{

    private $image_id;
    private $image_nom;
    private $image_lien;
    private $image_timbre_id;

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
  public function getImage_id()
  {
    return $this->image_id;
  }
  public function getImage_nom()
  {
    return $this->image_nom;
  }
  public function getImage_lien()
  {
    return $this->image_lien;
  }
  public function getImage_timbre_id()
  {
    return $this->image_timbre_id;
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
   * Mutateur de la propriété image_id 
   * @param int $image_id
   * @return $this
   */    
  public function setImage_id($image_id) {
    unset($this->erreurs['image_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $image_id)) {
      $this->erreurs['image_id'] = 'Numéro d\'image incorrect.';
    }
    $this->image_id = $image_id;
  }    

  /**
   * Mutateur de la propriété image_nom
   * @param string $image_nom
   * @return $this
   */    
  public function setImage_nom($image_nom) {
    unset($this->erreurs['image_nom']);
    $image_nom = trim($image_nom);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $image_nom)) {
      $this->erreurs['image_nom'] = 'Au moins un caractère.';
    }
    $this->image_nom = $image_nom;
  }   

   /**
   * Mutateur de la propriété image_lien 
   * @param int $image_lien
   * @return $this
   */    
  public function setImage_lien($image_lien) {
    unset($this->erreurs['image_lien']);
    $image_lien = trim($image_lien);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $image_lien)) {
      $this->erreurs['image_lien'] = 'Svp ajouter une image';
    }
    $this->image_lien = $image_lien;
    return $this;
  }   

  /**
   * Mutateur de la propriété image_timbre_id 
   * @param int $image_timbre_id
   * @return $this
   */    
  public function setImage_timbre_id($image_timbre_id) {
    unset($this->erreurs['image_timbre_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $image_timbre_id)) {
      $this->erreurs['image_timbre_id'] = 'Numéro d\'image incorrect.';
    }
    $this->image_timbre_id = $image_timbre_id;
  }    
  



}


?>