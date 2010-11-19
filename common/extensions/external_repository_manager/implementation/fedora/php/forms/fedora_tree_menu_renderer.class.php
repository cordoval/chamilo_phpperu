<?php
namespace common\extensions\external_repository_manager\implementation\fedora;
/**
 * $Id: tree_menu_renderer.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html.menu
 */
use common\libraries\TreeMenuRenderer;

/**
 * Tree renderer with items collapsed by default.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraTreeMenuRenderer extends TreeMenuRenderer
{
    protected function get_javascript(){
    	return ResourceManager::get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.simple_tree_menu.js');
    }
}
?>