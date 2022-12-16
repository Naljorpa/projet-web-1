<?php

/**
 * Classe de l'entité Favoris
 *
 */
class Favoris
{

  private $favoris_utilisateur_id;
  private $favoris_enchere_id;

  private $erreurs = [];

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

  /**
   * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
   * @param string $prop, nom de la propriété
   * @param $val, contenu de la propriété à mettre à jour
   */

  public function __set($prop, $val)
  {
    $setProperty = 'set' . $prop;
    $this->$setProperty($val);
  }

  /**
   * Mutateur de la propriété favoris_utilisateur_id 
   * @param int $favoris_utilisateur_id
   * @return $this
   */
  public function setFavoris_utilisateur_id($favoris_utilisateur_id)
  {
    unset($this->erreurs['mise_utilisateur_id']);
    $regExp = '/^[0-9]\d*$/';
    if (!preg_match($regExp, $favoris_utilisateur_id)) {
      $this->erreurs['favoris_utilisateur_id'] = 'Numéro utilisateur incorrect.';
    }
    $this->favoris_utilisateur_id = $favoris_utilisateur_id;
    return $this;
  }

  /**
   * Mutateur de la propriété favoris_enchere_id 
   * @param int $favoris_enchere_id
   * @return $this
   */
  public function setFavoris_enchere_id($favoris_enchere_id)
  {
    unset($this->erreurs['favoris_enchere_id']);
    $regExp = '/^[0-9]\d*$/';
    if (!preg_match($regExp, $favoris_enchere_id)) {
      $this->erreurs['favoris_enchere_id'] = 'Numéro d\'enchere incorrect.';
    }
    $this->favoris_enchere_id = $favoris_enchere_id;
    return $this;
  }
}
