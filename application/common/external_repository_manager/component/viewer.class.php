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

            if (!$object->is_importable())
            {
                $this->display_warning_message(Translation :: get('ExternalObjectAlreadyImported'));
            }

            $html = array();
            $html[] = $display->as_html();

            $toolbar = new Toolbar();
            $toolbar_item = new ToolbarItem(Translation :: get('Back'), Theme :: get_common_image_path() . 'action_prev.png', 'javascript:history.back();');
            $toolbar->add_item($toolbar_item);

            $type_actions = $this->get_external_repository_object_actions($object);
            foreach ($type_actions as $type_action)
            {
                $type_action->set_display(ToolbarItem :: DISPLAY_ICON_AND_LABEL);
                $toolbar->add_item($type_action);
            }

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