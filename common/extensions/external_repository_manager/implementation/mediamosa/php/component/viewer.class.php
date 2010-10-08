<?php
/**
 * Description of viewerclass
 *
 * @author jevdheyd
 */

require_once dirname(__FILE__) . '/../../../external_repository_object_display.class.php';

class MediamosaExternalRepositoryManagerViewerComponent extends MediamosaExternalRepositoryManager
{
    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }

//    function run()
//    {
//        
//        $viewer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: VIEWER_COMPONENT, $this);
//        /*$viewer->run();*/
//        $viewer->display_header();
//        
//        $id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
//        $object = $this->retrieve_external_repository_object($id);
//        $display = ExternalRepositoryObjectDisplay :: factory($object);
//        
//        $html = array();
//        $html[] = $display->as_html($viewer);
//        
//        $toolbar = new Toolbar();
//        $toolbar_item = new ToolbarItem(Translation :: get('Back'), Theme :: get_common_image_path() . 'action_prev.png', 'javascript:history.back();');
//        $toolbar->add_item($toolbar_item);
//        
//        if ($object->is_editable())
//        {
//            $toolbar_item_edit = new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $viewer->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_EDIT_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)));
//            $toolbar->add_item($toolbar_item_edit);
//            
//            $toolbar_item_delete = new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $viewer->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_DELETE_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)));
//            $toolbar->add_item($toolbar_item_delete);
//        }
//        
//        if ($object->is_usable($id))
//        {
//            
//            if ($viewer->get_parent()->is_stand_alone())
//            {
//                $toolbar_item_select = new ToolbarItem(Translation :: get('Select'), Theme :: get_common_image_path() . 'action_publish.png', $viewer->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_SELECT_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)));
//                $toolbar->add_item($toolbar_item_select);
//            }
//            else
//            {
//                $toolbar_item_select = new ToolbarItem(Translation :: get('Import'), Theme :: get_common_image_path() . 'action_import.png', $viewer->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_IMPORT_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)));
//                $toolbar->add_item($toolbar_item_select);
//            }
//        }
//        
//        $html[] = '<br/>' . $toolbar->as_html();
//        echo (implode("\n", $html));
//        
//        $viewer->display_footer();
//    }
}
?>
