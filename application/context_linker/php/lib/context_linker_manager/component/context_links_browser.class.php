<?php
namespace application\context_linker;

use common\libraries\EqualityCondition;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Request;
use common\libraries\ActionBarRenderer;
use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\ResourceManager;

use \Freemind;
use \FreemindNode;

require_once dirname(__FILE__) . '/context_link_browser/context_link_browser_table.class.php';
require_once Path :: get_plugin_path() . 'mindmap/freemind_node.class.php';
require_once Path :: get_plugin_path() . 'mindmap/freemind.class.php';
/**
 * context_linker component which allows the user to browse his context_links
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManagerContextLinksBrowserComponent extends ContextLinkerManager
{
    const PARAM_VIEW = 'view';
    const VIEW_TABLE = 'table';
    const VIEW_GRAPHIC = 'graphic';

    function run()
    {
        $this->display_header();

        $html = array();

        $html[]= $this->get_action_bar();
        if(Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID))
        {
            $html[] = $this->get_table();
        }
        else
        {
            $html[] = '<p>' . Translation :: get('NoContentObjectSelected', null, 'repository') . '</p>';
        }

        echo implode("\n", $html);

        $this->display_footer();
    }

    function get_table()
    {
        if(Request :: get(self :: PARAM_VIEW) == self :: VIEW_GRAPHIC)
        {
            
            $cdm = ContextLinkerDataManager :: get_instance();
            $content_object_id = Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID);
            $result = $cdm->retrieve_full_context_links_recursive($content_object_id, null, null, null, null, parent :: ARRAY_TYPE_RECURSIVE);

            $mindmap = new Freemind();

            $base_node = new FreemindNode($result[$content_object_id][ContextLinkerManager :: PROPERTY_ORIG_ID], $result[$content_object_id][ContextLinkerManager :: PROPERTY_ORIG_TITLE]);
            $mindmap->set_base_node($base_node);

            $this->get_children(&$base_node, $result[$content_object_id]['children'], ContextLinkerManager :: RECURSIVE_DIRECTION_DOWN, FreemindNode :: POSITION_RIGHT);
            $this->get_children(&$base_node, $result[$content_object_id]['parents'], ContextLinkerManager :: RECURSIVE_DIRECTION_UP);
            //var_dump($mindmap);

            $xml = $mindmap->to_xml();;

            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'plugin/mindmap/flashobject.js');
            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/context_linker/resources/javascript/mindmap_visualisation.js');
            $html[] = '<div id="flashcontent" onmouseover="giveFocus();" onLoad="giveFocus();">
                         Flash plugin or Javascript are turned off.
                         Activate both  and reload to view the mindmap
                       </div>';
            return implode("\n", $html);
        }
        else
        {
            $table = new ContextLinkBrowserTable($this, array(Application :: PARAM_APPLICATION => 'context_linker', Application :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTEXT_LINKS), Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID));
            return $table->as_html();
            //$cdm = ContextLinkerDataManager :: get_instance();
            //$result = $cdm->retrieve_full_context_links_recursive(Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID), null, null, null, null, parent :: ARRAY_TYPE_FLAT);
        }
    }

    /*
     * recursively assign child nodes
     * @param FreemindNode parent_node to assign children to
     * @param array the child context links 
     */
    function get_children(FreemindNode $parent_node, array $context_link_children, $mode ,$position = FreemindNode :: POSITION_LEFT)
    {
        foreach($context_link_children as $id => $content_object)
        {
            if($mode == ContextLinkerManager :: RECURSIVE_DIRECTION_DOWN)
            {
                $node = new FreemindNode($id, $content_object[ContextLinkerManager :: PROPERTY_ALT_TITLE]);
                $node->set_position($position);
                $parent_node->add_child(&$node);
                if(isset($content_object['children']))$this->get_children(&$node, $content_object['children'], $mode, FreemindNode :: POSITION_RIGHT);
            }
            if($mode == ContextLinkerManager :: RECURSIVE_DIRECTION_UP)
            {
                $node = new FreemindNode($id, $content_object[ContextLinkerManager :: PROPERTY_ORIG_TITLE]);
                $node->set_position($position);
                $parent_node->add_child(&$node);
                if(isset($content_object['parents']))$this->get_children(&$node, $content_object['parents'], $mode);
            }
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $actions = array();
        $actions[] = new ToolbarItem(Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES) , Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID))));

        $action_bar->set_common_actions($actions);
        $action_bar->set_search_url($this->get_url());

        return $action_bar->as_html();
    }

    

    
}
?>