<?php
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

    function SystemAnnouncerMultipublisher($parent)
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
            $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects') . '</div>';
            $html[] = '<div class="description">';
            $html[] = '<ul class="attachments_list">';

            while ($content_object = $content_objects->next_result())
            {
                $html[] = '<li><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $content_object->get_icon_name() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($content_object->get_type()) . 'TypeName')) . '"/> ' . $content_object->get_title() . '</li>';
            }

            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $form = new SystemAnnouncementPublicationForm(SystemAnnouncementPublicationForm :: TYPE_MULTI, $ids, $this->parent->get_user(), $this->parent->get_url(array_merge($this->parent->get_parameters(), array(RepoViewer :: PARAM_ID => $ids))));
        if ($form->validate())
        {
            $publication = $form->create_content_object_publications();

            if (! $publication)
            {
                $message = Translation :: get('ObjectNotPublished');
            }
            else
            {
                $message = Translation :: get('ObjectPublished');
            }

            $this->parent->redirect($message, (! $publication ? true : false), array(Application :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS));
        }
        else
        {
            $html[] = $form->toHtml();
        }

        return implode("\n", $html);
    }
}
?>