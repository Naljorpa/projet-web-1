<?php

/**
 * Classe de l'entité Mise
 *
 */
class Mise
{

  private $mise_utilisateur_id;
  private $mise_enchere_id;
  private $mise_valeur;

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
    $setProperty = 'setMise_' . $prop;
    $this->$setProperty($val);
  }

  /**
   * Mutateur de la propriété mise_utilisateur_id 
   * @param int $mise_utilisateur_id
   * @return $this
   */
  public function setMise_utilisateur_id($mise_utilisateur_id)
  {
    unset($this->erreurs['mise_utilisateur_id']);
    $regExp = '/^[0-9]\d*$/';
    if (!preg_match($regExp, $mise_utilisateur_id)) {
      $this->erreurs['mise_utilisateur_id'] = 'Numéro utilisateur incorrect.';
    }
    $this->mise_utilisateur_id = $mise_utilisateur_id;
    return $this;
  }

  /**
   * Mutateur de la propriété mise_enchere_id 
   * @param int $mise_enchere_id
   * @return $this
   */
  public function setMise_enchere_id($mise_enchere_id)
  {
    unset($this->erreurs['mise_enchere_id']);
    $regExp = '/^[0-9]\d*$/';
    if (!preg_match($regExp, $mise_enchere_id)) {
      $this->erreurs['mise_enchere_id'] = 'Numéro d\'enchere incorrect.';
    }
    $this->mise_enchere_id = $mise_enchere_id;
    return $this;
  }

  /**
   * Mutateur de la propriété mise_valeur 
   * @param int $mise_valeur
   * @return $this
   */
  public function setMise_mise_valeur($mise_valeur)
  {
    unset($this->erreurs['mise_valeur']);
    $oRequetesSQL = new RequetesSQL;

    $derniereMise = $oRequetesSQL->getMise($this->mise_enchere_id);

    if ($derniereMise == true) {
      if (!preg_match('/^\d+$/', $mise_valeur)) {
        $this->erreurs['mise_valeur'] = 'Doit être de format valide.';
      }
      if ($mise_valeur <= $derniereMise["MAX(mise_valeur)"]) {
        $this->erreurs['mise_valeur']  = "Doit être plus élevé que la mise actuelle";
      }
     
    } else if ($derniereMise == false) {
      if (!preg_match('/^\d+$/', $mise_valeur)) {
        $this->erreurs['mise_valeur'] = 'Doit être de format valide.';
      }
    }

    $this->mise_valeur = $mise_valeur;

    return $this;
  }
}
