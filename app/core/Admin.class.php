<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
 */

class Admin extends Routeur
{

  protected $utilisateur_id;
  protected static $entite;
  protected static $action;
  protected static $oUtilisateur;


  /**
   * Constructeur qui initialise des propriétés à partir du query string
   * 
   */
  public function __construct()
  {
    self::$entite = $_GET['entite'] ?? 'utilisateur';
    self::$action = $_GET['action'] ?? 'l';
    $this->oRequetesSQL = new RequetesSQL;
  }

  /**
   * Gérer l'interface d'administration 
   */
  public function gererEntite()
  {
    if (isset($_SESSION['oUtilisateur'])) {
      self::$oUtilisateur = $_SESSION['oUtilisateur'];
      $entite = ucwords(self::$entite);
      $classe = "Admin$entite";
      if (class_exists($classe)) {
        (new $classe())->gererAction();
      } else {
        throw new Exception("L'entité " . self::$entite . " n'existe pas.");
      }
    } else if (self::$entite == "utilisateur" && self::$action == "a") {
      $entite = ucwords(self::$entite);
      $classe = "Admin$entite";
      if (class_exists($classe)) {
        (new $classe())->gererAction();
      } else {
        throw new Exception("L'entité " . self::$entite . " n'existe pas.");
      }
    } else {
      (new AdminUtilisateur)->connecter();
    }
  }

  /**
   * Gérer l'interface d'administration d'une entité
   */
  public function gererAction()
  {

    if (isset($this->methodes[self::$action])) {
      $methode = $this->methodes[self::$action]['nom'];
      if (isset($this->methodes[self::$action]['droits'])) {
        $droits = $this->methodes[self::$action]['droits'];

        if (isset(self::$oUtilisateur)) {
          foreach ($droits as $droit) {
            if ($droit == self::$oUtilisateur->utilisateur_profile_id) {
              $this->$methode($_SESSION['oUtilisateur']);
              exit;
            }
          }
          throw new Exception(self::FORBIDDEN);
        }
      }
      $this->$methode();
    } else {
      throw new Exception("L'action " . self::$action . " de l'entité " . self::$entite . " n'existe pas.");
    }
  }
}
