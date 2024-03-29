<?php
namespace repository\content_object\forum_topic;

use common\libraries\Translation;
use common\libraries\Utilities;
use repository\ComplexDisplay;
use repository\RepositoryDataManager;
use repository\ContentObjectForm;

class ForumTopicDisplayEditorComponent extends ForumTopicDisplay
{

    function run()
    {
        if ($this->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $url = $this->get_url(array(
                    ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumTopicDisplay :: ACTION_EDIT_FORUM_POST,
                    ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                    ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_selected_complex_content_object_item_id()));

            $datamanager = RepositoryDataManager :: get_instance();
            $selected_complex_content_object_item = $this->get_selected_complex_content_object_item();
            $content_object = $datamanager->retrieve_content_object($selected_complex_content_object_item->get_ref());

            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $url);

            if ($form->validate())
            {
                $form->update_content_object();
                if ($form->is_version())
                {
                    $selected_complex_content_object_item->set_ref($content_object->get_latest_version()->get_id());
                    $selected_complex_content_object_item->update();
                }

                if ($selected_complex_content_object_item->get_display_order() == 1)
                {
                    $parent = $datamanager->retrieve_content_object($selected_complex_content_object_item->get_parent());
                    $parent->set_title($content_object->get_title());
                    $parent->update();
                }

                $message = htmlentities(Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('ForumTopic')) , Utilities :: COMMON_LIBRARIES));

                $params = array();
                $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
                $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

                $this->redirect($message, '', $params);

            }
            else
            {
                $this->display_header($this->get_complex_content_object_breadcrumbs());
                $form->display();
                $this->display_footer();
            }

        }
    }

}
?>