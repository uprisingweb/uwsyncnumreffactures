<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2023 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    uwsyncnumreffactures/admin/setup.php
 * \ingroup uwsyncnumreffactures
 * \brief   UwSyncNumRefFactures setup page.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) { $i--; $j--; }
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) $res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) $res = @include "../../main.inc.php";
if (!$res && file_exists("../../../main.inc.php")) $res = @include "../../../main.inc.php";
if (!$res) die("Include of main fails");

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT . "/societe/class/societe.class.php";
require_once DOL_DOCUMENT_ROOT . "/compta/facture/class/facture.class.php";
require_once '../lib/uwsyncnumreffactures.lib.php';
require_once "../class/uwapiutils.class.php";
require_once DOL_DOCUMENT_ROOT .'/custom/uwsyncnumreffactures/class/uwapiutils.class.php';

// Translations
$langs->loadLangs(array("admin", "uwsyncnumreffactures@uwsyncnumreffactures"));

// Access control
if (!$user->admin) accessforbidden();

// Parameters
$action = GETPOST('action', 'alpha');
$backtopage = GETPOST('backtopage', 'alpha');

$arrayofparameters = array(
	'FACTURE_SYNCHRONE_SYNCHRO_API_SERVERNAME'=>array('css'=>'minwidth500', 'enabled'=>1),
	'FACTURE_SYNCHRONE_SYNCHRO_API_KEY'=>array('css'=>'minwidth500', 'enabled'=>1),
	'FACTURE_SYNCHRONE_SYNCHRO_LOG'=>array('css'=>'minwidth500', 'enabled'=>1)

);
// if($conf->global->FACTURE_SYNCHRONE_SYNCHRO_API_SERVERNAME) {
// 	$arrayofparameters['FACTURE_SYNCHRONE_SYNCHRO_API_KEY']['enabled'] = 1;
// }




/*
 * Actions
 */

if ((float) DOL_VERSION >= 6)
{
	include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';
}




/*
 * View
 */

$page_name = "UwSyncNumRefFacturesSetup";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'object_uwsyncnumreffactures@uwsyncnumreffactures');

// Configuration header
$head = uwsyncnumreffacturesAdminPrepareHead();
dol_fiche_head($head, 'settings', '', -1, "uwsyncnumreffactures@uwsyncnumreffactures");

// Setup page goes here
echo '<span class="opacitymedium">'.$langs->trans("UwSyncNumRefFacturesSetupPage").'</span><br><br>';


echo "<pre style='width:100%;max-with:100%;overflow-wrap: break-word;'>";
echo "<div><b>Pour confirgurer le module vous devez (sur les 2 dolibarr à synchroniser) : </b></div>";
echo "<div><b> 1. </b> Activer le module dans la liste des modules</div>";
echo "<div><b> 2. </b> Activer le module API Rest</div>";
echo "<div><b> 3. </b> Générer une clé API sur un de vos utilisateurs et la reporter la page de configuration du module du 2nd dolibarr (utiliser/créer un utilisateur qui a les droits sur les factures)</div>";
echo "<div><b> 4. </b> Définir sur chaque dolibarr le SERVER NAME du 2nd Dolibarr (sans https:// ou / à la fin . (Ex: dolibarr.nomdedomaine.com)</div>";
echo "<div><b> 5. </b> Dans la liste des modules > Factures et Avoir > confirgurer : il faut activer la numérotation synchrone et definir les mêmes masques de numérotation</div>";
echo "<div><b> 6. </b> Une fois la numérotation synchrone activée sur les 2 dolibarr, les informations API renseignées, vous pourrez voir sur la page ci-dessous l'information <b>Prochain numéro de facture calculé</b>. Si elle est à 0 c'est que vous avez une erreur.<br/>Si elle est différente c'est que les API sont mal configurées ou les masques de numérotation ne sont pas les mêmes. Si elles est identique c'est que la configuration est bonne.</div>";

echo "<div><br/><br/></div>";
echo "<div><b>En cas de problème : </b></div>";
echo "<div>- consulter : <a href='". DOL_URL_ROOT ."/api/index.php/explorer/' target='_blank'>". DOL_URL_ROOT ."/api/index.php/explorer/</a> et saisr la clé API générée sur cette instance de dolibarr dans le champs DOLAPIKEY</div>";
echo "<div>&nbsp;&nbsp;et Vérifier qu'existe 'uwsyncnumreffactures' > 'List Operations' > GET /uwsyncnumreffactures/nextnumreffacture (A verifier sur les 2 dolibarr)</div>";
echo "<div><br/></div>";
echo "<div>- Vous pouvez aussi activer les log en mettant FACTURE_SYNCHRONE_SYNCHRO_LOG à 1 et consulter les log dans custom/uwsyncnumreffactures/logs (le faire sur les 2 dolibarr pour bien voir les interactions de chaque côté)</div>";
echo "<div><br/></div>";
echo "<div>- Recharger cette page suffit pour tester l'api et consulter les logs. En effet pour afficher le <b>Prochain numéro de facture calculé</b> ci-dessous les appels inter-dolibarr sont fait </div>";
echo "</pre>";


if ($action == 'edit')
{
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="token" value="'.newToken().'">';
	print '<input type="hidden" name="action" value="update">';

	print '<table class="noborder centpercent">';
	print '<tr class="liste_titre"><td class="titlefield">'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td></tr>';

	foreach ($arrayofparameters as $key => $val)
	{
		print '<tr class="oddeven"><td>';
		$tooltiphelp = (($langs->trans($key.'Tooltip') != $key.'Tooltip') ? $langs->trans($key.'Tooltip') : '');
		print $form->textwithpicto($langs->trans($key), $tooltiphelp);
		print '</td><td><input name="'.$key.'"  class="flat '.(empty($val['css']) ? 'minwidth200' : $val['css']).'" value="'.$conf->global->$key.'"></td></tr>';
	}
	print '</table>';

	print '<br><div class="center">';
	print '<input class="button" type="submit" value="'.$langs->trans("Save").'">';
	print '</div>';

	print '</form>';
	print '<br>';
}
else
{
	if (!empty($arrayofparameters))
	{
		print '<table class="noborder centpercent">';
		print '<tr class="liste_titre"><td class="titlefield">'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td></tr>';

		foreach ($arrayofparameters as $key => $val)
		{
			print '<tr class="oddeven"><td>';
			$tooltiphelp = (($langs->trans($key.'Tooltip') != $key.'Tooltip') ? $langs->trans($key.'Tooltip') : '');
			print $form->textwithpicto($langs->trans($key), $tooltiphelp);
			print '</td><td>'.$conf->global->$key.'</td></tr>';
		}

		print '</table>';

		print '<div class="tabsAction">';
		print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit">'.$langs->trans("Modify").'</a>';
		print '</div>';
	}
	else
	{
		print '<br>'.$langs->trans("NothingToSetup");
	}
}

// TEST 
if(	$conf->global->FACTURE_SYNCHRONE_SYNCHRO_API_SERVERNAME && 	$conf->global->FACTURE_SYNCHRONE_SYNCHRO_API_KEY) {
	
	require_once "../core/modules/facture/mod_facture_synchrone.php";
	$module = new mod_facture_synchrone($db);
	            
	$facture = new Facture($db);
	$facture->initAsSpecimen();
	$facture->type = 0;

	$module = new mod_facture_synchrone($db);

	print '<table class="noborder centpercent">';
	print '<tr class="liste_titre"><td class="titlefield">'.$langs->trans("Parameter").'</td><td>'.$langs->trans("Value").'</td></tr>';
	print '<tr class="oddeven"><td>Prochain nuiméro de facture calculé</td>';

	print '<td>' . $module->getNextValue($mysoc, $facture, 'next') . '</td></tr>';

	print '</table>';

	echo "<pre>";
	echo "</pre>";

}




// Page end
dol_fiche_end();

llxFooter();
$db->close();
