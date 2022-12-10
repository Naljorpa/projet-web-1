
document.querySelectorAll('.confirmer').forEach(e => e.onclick = afficherFenetreModale);

/**
 * Affichage d'une fenêtre modale
 */
function afficherFenetreModale(e) {
  e.preventDefault();
  let locationHref = () => { location.href = this.href };
  let annuler = () => { document.getElementById('modaleSuppression').close() };
  document.querySelector('#modaleSuppression .OK').onclick = locationHref;
  document.querySelector('#modaleSuppression .KO').onclick = annuler;
  document.getElementById('modaleSuppression').showModal();
  document.querySelector('#modaleSuppression .focus').focus();
}