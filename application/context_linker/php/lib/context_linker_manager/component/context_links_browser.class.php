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
use common\libraries\Session;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;

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


    function run()
    {
        $trail = new BreadcrumbTrail;
        $trail->add(new Breadcrumb($this->get_url(array(ContextLinkerManager :: PARAM_ACTION => null)), Translation :: get('ContextLinker')));
        $trail->add(new Breadcrumb($this->get_url(array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTEXT_LINKS, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID))), Translation :: get('BrowseObjects', array('OBJECT' => Translation::get('ContextLinks')), Utilities::COMMON_LIBRARIES)));
        $trail->add_help('ContextLinkBrowser');

        $this->display_header($trail);

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
            $result = $cdm->retrieve_full_context_links_recursive($content_object_id, null, null, null,  parent :: ARRAY_TYPE_RECURSIVE);

            $mindmap = new Freemind();

            $base_node = new FreemindNode($result[$content_object_id][ContextLinkerManager :: PROPERTY_ORIG_ID], $result[$content_object_id][ContextLinkerManager :: PROPERTY_ORIG_TITLE]);
            $mindmap->set_base_node($base_node);

            $this->get_children(&$base_node, $result[$content_object_id]['children'], ContextLinkerManager :: RECURSIVE_DIRECTION_DOWN, FreemindNode :: POSITION_RIGHT);
            $this->get_children(&$base_node, $result[$content_object_id]['parents'], ContextLinkerManager :: RECURSIVE_DIRECTION_UP);
            //var_dump($mindmap);

            $xml = $mindmap->to_xml();

            $filename = 'mm_' . Session :: get_user_id() . '.mm';
            $sys_file = Path :: get(SYS_FILE_PATH) . 'temp/tmp_' . $filename;
            $rel_file = Path :: get(REL_FILE_PATH) . 'temp/tmp_' . $filename;

            $mindmap_file = fopen($sys_file, 'w') or die ('can\'t create file');

            fwrite($mindmap_file, $xml);
            fclose($mindmap_file);

            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'plugin/mindmap/freemind_flash_browser/flashobject.js');

            $html[] = '<div id="flashcontent">Flash plugin or Javascript are turned off.Activate both  and reload to view the mindmap</div>';
            $html[] = '<script type="text/javascript">var fo = new FlashObject("' . Path :: get(WEB_PATH) . 'plugin/mindmap/freemind_flash_browser/visorFreemind.swf", "visorFreeMind", "100%", "100%", 6, "#9999ff");
		fo.addParam("quality", "high");
		fo.addParam("bgcolor", "#ffffff");
		fo.addVariable("openUrl", "_blank");
		fo.addVariable("initLoadFile", "' . $rel_file . '");
		fo.addVariable("startCollapsedToLevel","5");
		fo.write("flashcontent");</script>';

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

            $node->set_link($this->get_node_link($id));
        }
    }

    function get_node_link($id)
    {
        $params = array();
        $params[ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID] = $id;
        $params[ContextLinkerManager :: PARAM_VIEW] = ContextLinkerManager :: VIEW_GRAPHIC;

        return $this->get_url($params);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $actions = array();
        $actions[] = new ToolbarItem(Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES) , Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID))));

        $action_bar->set_common_actions($actions);

        $actions = array();
        $actions[] = new ToolbarItem(Translation :: get('TableView', null, Utilities :: COMMON_LIBRARIES) , Theme :: get_common_image_path() . 'view_table.png', $this->get_url(array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTEXT_LINKS, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID), ContextLinkerManager :: PARAM_VIEW => ContextLinkerManager :: VIEW_TABLE)));
        $actions[] = new ToolbarItem(Translation :: get('GraphicView', null, Utilities :: COMMON_LIBRARIES) , Theme :: get_common_image_path() . 'view_table.png', $this->get_url(array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTEXT_LINKS, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID), ContextLinkerManager :: PARAM_VIEW => ContextLinkerManager :: VIEW_GRAPHIC)));
        $action_bar->set_tool_actions($actions);
        
        $action_bar->set_search_url($this->get_url());

        return $action_bar->as_html();
    }

    

    
}
?>