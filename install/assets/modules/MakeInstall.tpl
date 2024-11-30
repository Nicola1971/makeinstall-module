/**
 * MakeInstall
 *
 * Create tpl files for Evolution CMS installer
 *
 * @category	module
 * @version     1.3
 * @internal	@modx_category PubKit
 * @internal	@properties &exportDir=Export to;string;
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU Public License (GPL)
 */
if (!isset($exportDir)) {
    echo "Set export destination in the module configuration: &exportDir=Export to;string;" . $modx->config['base_path'] . "assets/export/";
    return false;
}
include_once $modx->config['base_path'] . 'assets/modules/makeinstall/makeInstall.php';