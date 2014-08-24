<?php
// MakeInstall module
// Create MODx Evo 1.0.4 installer tpl files from current elements

$nl = "\n\n";

$stables = array(
	'cats'       => $modx->getFullTableName('categories'),
	'chunks'     => $modx->getFullTableName('site_htmlsnippets'),
	'modules'    => $modx->getFullTableName('site_modules'),
	'snippets'   => $modx->getFullTableName('site_snippets'),
	'plugins'    => $modx->getFullTableName('site_plugins'),
	'tvs'        => $modx->getFullTableName('site_tmplvars'),
	'templates'  => $modx->getFullTableName('site_templates'),
	'tv_ties'    => $modx->getFullTableName('site_tmplvar_templates'),
	'settings'   => $modx->getFullTableName('system_settings')
);

if (!file_exists($exportDir)) {
	mkdir($exportDir);
}

$subDirs = array('chunks', 'modules', 'plugins', 'snippets', 'templates', 'tvs');

foreach ($subDirs as $subDir) {
	if (!file_exists("$exportDir/$subDir")) {
		mkdir("$exportDir/$subDir");
	}
}

// get category names into an indexed array
$cat_names = $modx->db->select('*', $stables['cats']);

$cats = array(0 => 'uncategorized');

while ($cat = $modx->db->getRow($cat_names)) {
	$cats[$cat['id']] = $cat['category'];
}

// Process chunks

$element = NULL;
$chunks = $modx->db->select('name,description,category,snippet', $stables['chunks']);

while ($chunk = $modx->db->getRow($chunks)) {
	$chName = $chunk['name'];
	$chDesc = $chunk['description'];
	$chCat  = $cats[$chunk['category']];
	$chCode = $chunk['snippet'];

	$element = <<<CHUNK
/**
 * $chName
 *
 * $chDesc
 *
 * @category	chunk
 * @internal @modx_category $chCat
 */

CHUNK;

	$element .= $chCode;
	$fPath = $exportDir . 'chunks/' . preg_replace('#[^a-z_A-Z\-0-9\s\.]#',"",$chName);
	$fPath .= '.tpl';

	file_put_contents($fPath, $element);
	echo "Saved chunk: $chName <br />";
}

// Process modules
$element = NULL;
$modules = $modx->db->select('name,description,category,modulecode', $stables['modules']);

while ($module = $modx->db->getRow($modules)) {
	$mdName = $module['name'];
	$mdDesc = $module['description'];
	$mdCat  = $cats[$module['category']];
	$mdCode = $module['modulecode'];

	$element = <<<MODULE
/**
 * $mdName
 *
 * $mdDesc
 *
 * @category	module
 * @internal	@modx_category $mdCat
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 */

MODULE;

	$element .= $mdCode;
	$fPath = $exportDir . 'modules/' . preg_replace('#[^a-z_A-Z\-0-9\s\.]#',"",$mdName);
	$fPath .= '.tpl';

	file_put_contents($fPath, $element);
	echo "Saved module: $mdName <br />";
}

// Process snippets
$element = NULL;
$snippets = $modx->db->select('name,description,category,snippet', $stables['snippets']);

while ($snippet = $modx->db->getRow($snippets)) {
	$snName = $snippet['name'];
	$snDesc = $snippet['description'];
	$snCat  = $cats[$snippet['category']];
	$snCode = $snippet['snippet'];

	$element = <<<SNIPPET
/**
 * $snName
 *
 * $snDesc
 *
 * @category	snippet
 * @internal	@modx_category $snCat
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 */

SNIPPET;

	$element .= $snCode;
	$fPath = $exportDir . 'snippets/' . preg_replace('#[^a-z_A-Z\-0-9\s\.]#',"",$snName);
	$fPath .= '.tpl';

	file_put_contents($fPath, $element);
	echo "Saved snippet: $snName <br />";
}

// Process plugins
$element = NULL;
$plugins = $modx->db->select('name,description,category,plugincode,properties,disabled', $stables['plugins']);

while ($plugins = $modx->db->getRow($plugins)) {
	$plName = $plugins['name'];
	$plDesc = $plugins['description'];
	$plCat  = $cats[$plugins['category']];
	$plCode = $plugins['plugincode'];
    $plProp = $plugins['properties'];
    $plDis = $plugins['disabled'];

	$element = <<<PLUGINS
/**
 * $plName
 *
 * $plDesc
 *
 * @category	plugins
 * @internal	@modx_category $snCat
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal @events 
 * @internal @installset base
 * @internal @modx_category $plCat
 * @internal @properties $plProp
 */

PLUGINS;

	$element .= $plCode;
	$fPath = $exportDir . 'plugins/' . preg_replace('#[^a-z_A-Z\-0-9\s\.]#',"",$plName);
	$fPath .= '.tpl';

	file_put_contents($fPath, $element);
	echo "Saved plugins: $plName <br />";
}

// Process templates
$element = NULL;
$templates = $modx->db->select('id,templatename,description,category,content', $stables['templates']);
$templateNames = array();

while ($template = $modx->db->getRow($templates)) {
	$tpName = $template['templatename'];
	$tpDesc = $template['description'];
	$tpCat  = $cats[$template['category']];
	$tpCode = $template['content'];

// save template ID => name pairs for TV assignments
	$templateNames[$template['id']] = $tpName;

	$element = <<<TEMPLATE
/**
 * $tpName
 *
 * $tpDesc
 *
 * @category	template
 * @internal	@modx_category $tpCat
 */

TEMPLATE;

	$element .= $tpCode;

	$fPath = $exportDir . 'templates/' . preg_replace('#[^a-z_A-Z\-0-9\s\.]#',"",$tpName);
	$fPath .= '.tpl';


	file_put_contents($fPath, $element);
	echo "Saved template: $tpName <br />";
}

// Process template variables
$element = NULL;
$tvs = $modx->db->select('id, type, name, caption, description, category, elements, display, display_params, default_text', $stables['tvs']);

while ($tv = $modx->db->getRow($tvs)) {
	$tvName = $tv['name'];
	$tvType = $tv['type'];
	$tvCaption  = $tv['caption'];
	$tvDesc = $tv['description'];
	$tvCat  = $cats[$tv['category']];
	$tvOptions = $tv['elements'];
	$tvDefault = $tv['default_text'];
	$tvWidget = $tv['display'];
	$tvParams = $tv['display_params'];

	$qString = 'tmplvarid = ' . $tv['id'];
	$assignments = $modx->db->select('templateid', $stables['tv_ties'], $qString);
	$assign = NULL;
	while ($templateId = $modx->db->getValue($assignments)) {
		$assign .= $templateNames[$templateId] . ',';
	}
	$assign = (!empty($assign)) ? substr($assign,0,-1) : NULL;

	$element = <<<TV
/**
 * $tvName
 *
 * $tvDesc
 *
 * @category	tv
 * @internal	@modx_category $tvCat
 * @internal    @caption $tvCaption
 * @internal    @input_type $tvType
 * @internal    @input_options $tvOptions
 * @internal    @input_default $tvDefault
 * @internal	@output_widget $tvWidget
 * @internal	@output_widget_params $tvParams
 * @internal    @template_assignments $assign
 */

TV;

	$element .= $tvCode;

	$fPath = $exportDir . 'tvs/' . preg_replace('#[^a-z_A-Z\-0-9\s\.]#',"",$tvName);
	$fPath .= '.tpl';


	file_put_contents($fPath, $element);
	echo "Saved TV: $tvName <br />";
}

echo $nl . 'Done!';

?>