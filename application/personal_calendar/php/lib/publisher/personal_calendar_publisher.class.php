<?php

namespace application\personal_calendar;

use common\libraries\InCondition;
use repository\RepositoryDataManager;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\extensions\repo_viewer\RepoViewer;
use repository\ContentObject;
use common\libraries\Application;
/**
 * $Id: personal_calendar_publisher.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.publisher
 */
/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class PersonalCalendarPublisher
{
    private $parent;

    function PersonalCalendarPublisher($parent)
    {
        $this->parent = $parent;
    }

    function get_publications_form($ids)
    {
        if (is_null($ids))
            return '';

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        $html = array();

        if (count($ids) > 0)
        {
            $condition = new InCondition(ContentObject :: PROPERTY_ID, $ids, ContentObject :: get_table_name());
            $content_objects = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);
            //Utilities :: order_content_objects_by_title($content_objects);


            $html[] = '<div class="content_object padding_10">';
            $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects', null , Utilities :: COMMON_LIBRARIES) . '</div>';
            $html[] = '<div class="description">';
            $html[] = '<ul class="attachments_list">';

            while ($content_object = $content_objects->next_result())
            {
                $namespace =ContentObject :: get_content_object_type_namespace($content_object->get_type());
                $html[] = '<li><img src="' . Theme :: get_image_path($namespace) . 'logo/' . Theme :: ICON_MINI . '.png" alt="' . htmlentities(Translation :: get('TypeName', null, $namespace)) . '"/> ' . $content_object->get_title() . '</li>';
            }

            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $parameters = $this->parent->get_parameters();
        $parameters[RepoViewer :: PARAM_ID] = $ids;
        $parameters[RepoViewer :: PARAM_ACTION] = RepoViewer :: ACTION_PUBLISHER;

        $form = new PersonalCalendarPublicationForm(PersonalCalendarPublicationForm :: TYPE_MULTI, $ids, $this->parent->get_user(), $this->parent->get_url($parameters));
        if ($form->validate())
        {
            $publication = $form->create_content_object_publications();

            if (! $publication)
            {
                $message = Translation :: get('ObjectNotPublished', array('OBJECT' => Translation :: get('PersonalCalendar')), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get('ObjectPublished', array('OBJECT' => Translation :: get('PersonalCalendar')), Utilities :: COMMON_LIBRARIES);
            }

            $this->parent->redirect($message, (! $publication ? true : false), array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR));
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