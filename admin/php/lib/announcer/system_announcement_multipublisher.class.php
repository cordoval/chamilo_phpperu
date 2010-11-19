<?php
namespace admin;
use common\libraries\Utilities;
use common\libraries\Application;
use common\libraries\Translation;
/**
 * $Id: system_announcement_multipublisher.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.announcer
 */

/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class SystemAnnouncerMultipublisher
{
    private $parent;

    function __construct($parent)
    {
        $this->parent = $parent;
    }

    function get_publications_form($ids)
    {
        $html = array();

        if (! $ids)
        {
            return;
        }

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        if (count($ids) > 0)
        {
            $condition = new InCondition(ContentObject :: PROPERTY_ID, $ids, ContentObject :: get_table_name());
            $content_objects = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);
            //Utilities :: order_content_objects_by_title($content_objects);


            $html[] = '<div class="content_object padding_10">';
            $html[] = '<div class="title">' . Translation :: get('SelectedObjects', array('OBJECTS' => Translation :: get('ContentObjects')), Utilities :: COMMON_LIBRARIES) . '</div>';
            $html[] = '<div class="description">';
            $html[] = '<ul class="attachments_list">';

            while ($content_object = $content_objects->next_result())
            {
                $html[] = '<li><img src="' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($content_object->get_type())) . 'logo/' . $content_object->get_icon_name(Theme :: ICON_MINI) . '.png" alt="' . htmlentities(Translation :: get('TypeName', array(), ContentObject :: get_content_object_namespace($content_object->get_type()))) . '"/> ' . $content_object->get_title() . '</li>';
            }

            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $parameters = $this->parent->get_parameters();
        $parameters[RepoViewer :: PARAM_ID] = $ids;
        $parameters[RepoViewer :: PARAM_ACTION] = RepoViewer :: ACTION_PUBLISHER;

        $form = new SystemAnnouncementPublicationForm(SystemAnnouncementPublicationForm :: TYPE_MULTI, $ids, $this->parent->get_user(), $this->parent->get_url($parameters));
        if ($form->validate())
        {
            $publication = $form->create_content_object_publications();

            if (! $publication)
            {
                $message = Translation :: get('ObjectNotPublished', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get('ObjectPublished', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES);
            }

            $this->parent->redirect($message, (! $publication ? true : false), array(
                    Application :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS));
        }
        else
        {
            $html[] = $form->toHtml();
            $html[] = '<div style="clear: both;"></div>';

            $this->parent->display_header();
            echo implode("\n", $html);
            $this->parent->display_footer();
        }
    }
}
?>