<?php
require_once dirname(__FILE__) . '/../external_repository_object_display.class.php';

class ExternalRepositoryViewerComponent extends ExternalRepositoryComponent
{

    function run()
    {
        $id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);

        if ($id)
        {
            $this->display_header();
            $object = $this->retrieve_external_repository_object($id);
            $display = ExternalRepositoryObjectDisplay :: factory($object);

            $html = array();
            $html[] = $display->as_html();

            $toolbar = new Toolbar();
            $toolbar_item = new ToolbarItem(Translation :: get('Back'), Theme :: get_common_image_path() . 'action_prev.png', 'javascript:history.back();');
            $toolbar->add_item($toolbar_item);

            if ($this->get_parent()->is_editable($id))
            {
                $toolbar_item_edit = new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_EDIT_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)));
                $toolbar->add_item($toolbar_item_edit);

                $toolbar_item_delete = new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_DELETE_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)));
                $toolbar->add_item($toolbar_item_delete);
            }

//            if ($object->is_usable($id))
//            {
//                if ($this->get_parent()->is_stand_alone())
//                {
//                    $toolbar_item_select = new ToolbarItem(Translation :: get('Select'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_SELECT_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)));
//                    $toolbar->add_item($toolbar_item_select);
//                }
//                else
//                {
//                    $toolbar_item_select = new ToolbarItem(Translation :: get('Import'), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => ExternalRepositoryManager :: ACTION_IMPORT_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $id)));
//                    $toolbar->add_item($toolbar_item_select);
//                }
//            }

            $html[] = '<br/>' . $toolbar->as_html();
            echo (implode("\n", $html));

            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoExternalObjectSelected')));
        }

    }
}
?>