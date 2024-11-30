<?php
// MakeInstall module for Evolution CMS

$exportDir = MODX_BASE_PATH . 'assets/export/';

if (!file_exists($exportDir)) {
    mkdir($exportDir, 0777, true);
}

$subDirs = ['chunks', 'modules', 'plugins', 'snippets', 'templates', 'tvs'];
foreach ($subDirs as $subDir) {
    $subPath = $exportDir . $subDir;
    if (!file_exists($subPath)) {
        mkdir($subPath, 0777, true);
    }
}

// Database table definitions
$stables = [
    'cats' => $modx->getFullTableName('categories'),
    'chunks' => $modx->getFullTableName('site_htmlsnippets'),
    'modules' => $modx->getFullTableName('site_modules'),
    'snippets' => $modx->getFullTableName('site_snippets'),
    'plugins' => $modx->getFullTableName('site_plugins'),
    'tvs' => $modx->getFullTableName('site_tmplvars'),
    'templates' => $modx->getFullTableName('site_templates'),
    'tv_ties' => $modx->getFullTableName('site_tmplvar_templates'),
    'settings' => $modx->getFullTableName('system_settings')
];

// Get categories
$cat_names = $modx->db->select('*', $stables['cats']);
$cats = [0 => 'uncategorized'];
while ($cat = $modx->db->getRow($cat_names)) {
    $cats[$cat['id']] = $cat['category'];
}
// design

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset='.$modx_manager_charset.'" />
    <title>' . $moduleName . '</title>
    <link type="text/css" rel="stylesheet" href="media/style/' . $modx->config['manager_theme'] . '/style.css">
</head>
<body>
<div class="sectionBody">
<div class="tab-pane" id="settingsMakeInstallPanes">
<div class="tab-page" id="tabmake">
    <h2 class="tab"><span><i class="fa fa-file-text" aria-hidden="true"></i> Make Install</span></h2>
    <div class="container">
        <h3><i class="fa fa-file-text" aria-hidden="true"></i>  Export tpl files from chunks, modules, snippets, plugins, templates and tvs</h3>
';
// Process chunks
$chunks = $modx->db->select('name,description,category,snippet', $stables['chunks']);
if ($modx->db->getRecordCount($chunks) == 0) {
    echo "No chunk founds!<br />";
}
while ($chunk = $modx->db->getRow($chunks)) {
    $chName = trim($chunk['name']);
    $chDesc = trim($chunk['description']);
    $chCat = $cats[$chunk['category']] ?? 'Uncategorized';
    $chCode = $chunk['snippet'];

    if (empty($chName) || empty($chCode)) {
        echo "Chunk not valid: Name or code empty. <br />";
        continue;
    }

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
    $fPath = $exportDir . 'chunks/' . preg_replace('#[^a-z_A-Z\-0-9\s\.]#', "", $chName) . '.tpl';

    if (file_put_contents($fPath, $element) === false) {
        echo "Error saving chunk: $chName <br />";
    } else {
        echo "Saved Chunk: $chName <br />";
    }
}
// Process modules
$element = NULL;
$modules = $modx->db->select('name,description,category,modulecode', $stables['modules']);
if ($modx->db->getRecordCount($modules) == 0) {
    echo "No modules founds!<br />";
}

while ($module = $modx->db->getRow($modules)) {
	$mdName = $module['name'];
	$mdDesc = $module['description'];
	$mdCat  = $cats[$module['category']];
	$mdCode = $module['modulecode'];
    
    if (empty($mdName) || empty($mdCode)) {
        echo "Module not valid: Name or code empty. <br />";
        continue;
    }    

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

    if (file_put_contents($fPath, $element) === false) {
        echo "Error saving Module: $mdName <br />";
    } else {
        echo "Saved Module: $mdName <br />";
    }
}

// Process snippets
$element = NULL;
$snippets = $modx->db->select('name,description,category,snippet', $stables['snippets']);
if ($modx->db->getRecordCount($modules) == 0) {
    echo "No snippets founds!<br />";
}

while ($snippet = $modx->db->getRow($snippets)) {
	$snName = $snippet['name'];
	$snDesc = $snippet['description'];
	$snCat  = $cats[$snippet['category']];
	$snCode = $snippet['snippet'];
    
    if (empty($snName) || empty($snCode)) {
        echo "Snippet not valid: Name or code empty.<br />";
        continue;
    }  

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

    if (file_put_contents($fPath, $element) === false) {
        echo "Error saving Snippet: $snName <br />";
    } else {
        echo "Saved Snippet: $snName <br />";
    }
}

// Process plugins
$element = NULL;
$plugins = $modx->db->select('name,description,category,plugincode,properties,disabled', $stables['plugins']);
if ($modx->db->getRecordCount($modules) == 0) {
    echo "No plugins founds!<br />";
}

while ($plugin = $modx->db->getRow($plugins)) {
	$plName = $plugin['name'];
	$plDesc = $plugin['description'];
	$plCat  = $cats[$plugin['category']];
	$plCode = $plugin['plugincode'];
    $plProp = $plugin['properties'];
    $plDis = $plugin['disabled'];
    
    if (empty($plName) || empty($plCode)) {
        echo "Plugin not valid: Name or code empty. <br />";
        continue;
    } 

	$element = <<<PLUGIN
/**
 * $plName
 *
 * $plDesc
 *
 * @category	plugins
 * @internal	@modx_category $plCat
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal @events 
 * @internal @installset base
 * @internal @properties $plProp
 */

PLUGIN;

	$element .= $plCode;
	$fPath = $exportDir . 'plugins/' . preg_replace('#[^a-z_A-Z\-0-9\s\.]#',"",$plName);
	$fPath .= '.tpl';

    if (file_put_contents($fPath, $element) === false) {
        echo "Error saving Plugin: $plName <br />";
    } else {
        echo "Saved Plugin: $plName <br />";
    }
}

// Process templates
$element = NULL;
$templates = $modx->db->select('id,templatename,description,category,content', $stables['templates']);
$templateNames = array();
if ($modx->db->getRecordCount($templates) == 0) {
    echo "No templates founds!<br />";
}
while ($template = $modx->db->getRow($templates)) {
	$tpName = $template['templatename'];
	$tpDesc = $template['description'];
	$tpCat  = $cats[$template['category']];
	$tpCode = $template['content'];

// save template ID => name pairs for TV assignments
	$templateNames[$template['id']] = $tpName;

    if (empty($tpName) || empty($tpCode)) {
        echo "Template not valid: Name or code empty. <br />";
        continue;
    } 

	$element = <<<TEMPLATE
/**
 * $tpName
 *
 * $tpDesc
 *
 * @category	template
 * @internal	@modx_category $tpCat
 * @version 	1.0
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 * @internal	@lock_template 0
 * @internal    @installset base
 * @internal    @overwrite false
 */

TEMPLATE;

	$element .= $tpCode;

	$fPath = $exportDir . 'templates/' . preg_replace('#[^a-z_A-Z\-0-9\s\.]#',"",$tpName);
	$fPath .= '.tpl';

    if (file_put_contents($fPath, $element) === false) {
        echo "Error saving Template: $tpName <br />";
    } else {
        echo "Saved Template: $tpName <br />";
    }
}

// Process template variables
$element = NULL;
$tvs = $modx->db->select('id, type, name, caption, description, category, elements, display, display_params, default_text', $stables['tvs']);
if ($modx->db->getRecordCount($tvs) == 0) {
    echo "No tvs founds!<br />";
}
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
    
    if (empty($tvName)) {
        echo "Tv not valid: Name empty. <br />";
        continue;
    } 

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
 * @internal    @lock_tv 0
 * @internal    @template_assignments $assign
 */

TV;

	$element .= $tvCode;

	$fPath = $exportDir . 'tvs/' . preg_replace('#[^a-z_A-Z\-0-9\s\.]#',"",$tvName);
	$fPath .= '.tpl';


    if (file_put_contents($fPath, $element) === false) {
        echo "Error saving Tv: $tvName <br />";
    } else {
        echo "Saved Tv: $tvName <br />";
    }
}

echo $nl . 'Done!';
echo ' </div>      
    </div>
</div>
</div>
';
?>
