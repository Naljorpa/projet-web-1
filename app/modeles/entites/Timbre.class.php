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
  private $image_id;
  private $image_lien;
  private $image_nom;




  private $erreurs = [];

  const STATUT_ACTIF = 1;
  const STATUT_INACTIF   = 2;
  const STATUT_ARCHIVE   = 3;

  /**
   * Constructeur de la classe 
   * @param array $proprietes, tableau associatif des propriétés 
   */
  public function __construct($proprietes = [])
  {
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
  public function __get($prop)
  {
    return $this->$prop;
  }

  // Getters explicites nécessaires au moteur de templates TWIG
  public function getTimbre_id()
  {
    return $this->timbre_id;
  }
  public function getTimbre_nom()
  {
    return $this->timbre_nom;
  }
  public function getTimbre_pays_id()
  {
    return $this->timbre_pays_id;
  }
  public function getTimbre_annee()
  {
    return $this->timbre_annee;
  }
  public function getTimbre_description()
  {
    return $this->timbre_description;
  }
  public function getTimbre_histoire()
  {
    return $this->timbre_histoire;
  }
  public function getTimbre_dimension()
  {
    return $this->timbre_dimension;
  }
  public function getTimbre_certification()
  {
    return $this->timbre_certification;
  }
  public function getTimbre_couleur()
  {
    return $this->timbre_couleur;
  }
  public function getTimbre_tirage()
  {
    return $this->timbre_tirage;
  }
  public function getTimbre_condition_id()
  {
    return $this->timbre_condition_id;
  }
  public function getTimbre_status_id()
  {
    return $this->timbre_status_id;
  }
  public function getTimbre_utilisateur_id()
  {
    return $this->timbre_utilisateur_id;
  }
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
  public function getErreurs()
  {
    return $this->erreurs;
  }

  /**
   * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
   * @param string $prop, nom de la propriété
   * @param $val, contenu de la propriété à mettre à jour
   */
  public function __set($prop, $val)
  {
    $setProperty = 'set' . ucfirst($prop);
    $this->$setProperty($val);
  }

  /**
   * Mutateur de la propriété timbre_id 
   * @param int $timbre_id
   * @return $this
   */
  public function setTimbre_id($timbre_id)
  {
    unset($this->erreurs['timbre_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $timbre_id)) {
      $this->erreurs['timbre_id'] = 'Numéro de timbre incorrect.';
    }
    $this->timbre_id = $timbre_id;
  }

  /**
   * Mutateur de la propriété timbre_nom 
   * @param string $timbre_nom
   * @return $this
   */
  public function setTimbre_nom($timbre_nom)
  {
    unset($this->erreurs['timbre_nom']);
    $timbre_nom = trim($timbre_nom);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $timbre_nom)) {
      $this->erreurs['timbre_nom'] = 'Au moins un caractère.';
    }
    $this->timbre_nom = $timbre_nom;
  }

  /**
   * Mutateur de la propriété timbre_pays_id
   * @param string $timbre_pays_id
   * @return $this
   */
  public function setTimbre_pays_id($timbre_pays_id)
  {
    unset($this->erreurs['timbre_pays_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $timbre_pays_id)) {
      $this->erreurs['timbre_pays_id'] = 'Doit être un id valide';
    }
    $this->timbre_pays_id = $timbre_pays_id;
  }

  /**
   * Mutateur de la propriété timbre_annee 
   * @param int $timbre_annee
   * @return $this
   */
  public function setTimbre_annee($timbre_annee)
  {
    unset($this->erreurs['timbre_annee']);
    if (
      !preg_match('/^\d+$/', $timbre_annee) ||
      $timbre_annee > date("Y")
    ) {
      $this->erreurs['timbre_annee'] = "Doit être format valide et avant l'année en cours.";
    }
    $this->timbre_annee = $timbre_annee;
  }

  /**
   * Mutateur de la propriété timbre_description
   * @param string $timbre_description
   * @return $this
   */
  public function setTimbre_description($timbre_description)
  {
    unset($this->erreurs['timbre_description']);
    $timbre_description = trim($timbre_description);
    $regExp = '/^\S+(\s+\S+){4,}$/';
    if (!preg_match($regExp, $timbre_description)) {
      $this->erreurs['timbre_description'] = 'Au moins 5 mots.';
    }
    $this->timbre_description = $timbre_description;
  }


  /**
   * Mutateur de la propriété timbre_histoire
   * @param string $timbre_histoire
   * @return $this
   */
  public function setTimbre_histoire($timbre_histoire)
  {
    unset($this->erreurs['timbre_histoire']);
    $timbre_histoire = trim($timbre_histoire);
    $this->timbre_histoire = $timbre_histoire;
  }

  /**
   * Mutateur de la propriété timbre_dimension
   * @param string $timbre_dimension
   * @return $this
   */
  public function setTimbre_dimension($timbre_dimension)
  {
    unset($this->erreurs['timbre_dimension']);
    $regExp = '/^.+$/';
    $timbre_dimension = trim($timbre_dimension);
    if (!preg_match($regExp, $timbre_dimension)) {
      $this->erreurs['timbre_dimension'] = 'Au moins un caractère.';
    }
    $this->timbre_dimension = $timbre_dimension;
  }

  /**
   * Mutateur de la propriété timbre_certification
   * @param string $timbre_certification
   * @return $this
   */
  public function setTimbre_certification($timbre_certification)
  {
    unset($this->erreurs['timbre_certification']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $timbre_certification)) {
      $this->erreurs['timbre_certification'] = 'Numéro de certification incorrecte';
    }
    $this->timbre_certification = $timbre_certification;
  }

  /**
   * Mutateur de la propriété timbre_couleur 
   * @param string $timbre_couleur
   * @return $this
   */
  public function setTimbre_couleur($timbre_couleur)
  {
    unset($this->erreurs['timbre_couleur']);
    $timbre_couleur = trim($timbre_couleur);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $timbre_couleur)) {
      $this->erreurs['timbre_couleur'] = 'Au moins un caractère.';
    }
    $this->timbre_couleur = $timbre_couleur;
  }

  /**
   * Mutateur de la propriété timbre_tirage
   * @param string $timbre_tirage
   * @return $this
   */
  public function setTimbre_tirage($timbre_tirage)
  {
    unset($this->erreurs['timbre_tirage']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $timbre_tirage)) {
      $this->erreurs['timbre_tirage'] = 'Doit être de valeur numérique';
    }
    $this->timbre_tirage = $timbre_tirage;
  }

  /**
   * Mutateur de la propriété timbre_condition_id
   * @param string $timbre_condition_id
   * @return $this
   */
  public function setTimbre_condition_id($timbre_condition_id)
  {
    unset($this->erreurs['timbre_condition_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $timbre_condition_id)) {
      $this->erreurs['timbre_condition_id'] = 'Doit être un id valide';
    }
    $this->timbre_condition_id = $timbre_condition_id;
  }

  /**
   * Mutateur de la propriété timbre_status_id
   * @param int $timbre_status_id
   * @return $this
   */
  public function setTimbre_status_id($timbre_status_id)
  {
    unset($this->erreurs['timbre_status_id']);
    if (
      $timbre_status_id != Timbre::STATUT_ACTIF &&
      $timbre_status_id != Timbre::STATUT_INACTIF   &&
      $timbre_status_id != Timbre::STATUT_ARCHIVE
    ) {
      $this->erreurs['timbre_status_id'] = 'Status incorrect.';
    }
    $this->timbre_status_id = $timbre_status_id;
  }

  /**
   * Mutateur de la propriété timbre_utilisateur_id
   * @param string $timbre_utilisateur_id
   * @return $this
   */
  public function setTimbre_utilisateur_id($timbre_utilisateur_id)
  {
    unset($this->erreurs['timbre_utilisateur_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $timbre_utilisateur_id)) {
      $this->erreurs['timbre_utilisateur_id'] = 'Doit être un id valide';
    }
    $this->timbre_utilisateur_id = $timbre_utilisateur_id;
  }

  /**
   * Mutateur de la propriété image_id 
   * @param int $image_id
   * @return $this
   */
  public function setImage_id($image_id)
  {
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
  public function setImage_nom($image_nom)
  {
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
  public function setImage_lien($image_lien)
  {
    unset($this->erreurs['image_lien']);
    $image_lien = trim($image_lien);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $image_lien)) {
      $this->erreurs['image_lien'] = 'Svp ajouter une image';
    }
    $this->image_lien = $image_lien;
    return $this;
  }
}
