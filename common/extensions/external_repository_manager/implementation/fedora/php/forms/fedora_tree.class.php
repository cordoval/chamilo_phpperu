<?php
namespace common\extensions\external_repository_manager\implementation\fedora;


require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once dirname(__FILE__) .'/fedora_tree_menu_renderer.class.php';

use HTML_Menu;
use common\libraries\Utilities;

/**
 * Tree with items collapsed by default.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraTree extends HTML_Menu
{
    const TREE_NAME = __CLASS__;

    static function get_tree_name(){
        return Utilities :: camelcase_to_underscores(self :: TREE_NAME);
    }

    /**
     * Renders the menu as a tree
     * @return string The HTML formatted tree
     */
    function render_as_tree(){
        $renderer = new FedoraTreeMenuRenderer($this->get_tree_name());
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }

}