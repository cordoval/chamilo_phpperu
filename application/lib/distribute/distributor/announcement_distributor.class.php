<?php
/**
 * $Id: announcement_distributor.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute.distributor
 */
//require_once Path :: get_application_library_path() . 'publisher/component/multipublisher.class.php';
require_once Path :: get_application_path() . 'lib/distribute/forms/announcement_distribution_form.class.php';

//require_once Path :: get_application_library_path() . 'publisher/component/publication_candidate_table/publication_candidate_table.class.php';


/**
 * This class represents a distribute publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class AnnouncementDistributor
{
    private $parent;

    function AnnouncementDistributor($parent)
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
            $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects') . '</div>';
            $html[] = '<div class="description">';
            $html[] = '<ul class="attachments_list">';

            while ($content_object = $content_objects->next_result())
            {
                $html[] = '<li><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $content_object->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($content_object->get_type()) . 'TypeName')) . '"/> ' . $content_object->get_title() . '</li>';
            }

            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $parameters = $this->parent->get_parameters();
        $parameters[RepoViewer :: PARAM_ID] = $ids;

        $form = new AnnouncementDistributionForm(AnnouncementDistributionForm :: TYPE_MULTI, $ids, $this->parent->get_user(), $this->parent->get_url($parameters));
        if ($form->validate())
        {
            $publication = $form->create_announcement_distributions();

            if (! $publication)
            {
                $message = Translation :: get('ObjectNotPublished');
            }
            else
            {
                $message = Translation :: get('ObjectPublished');
            }

            $this->parent->redirect($message, (! $publication ? true : false), array(Application :: PARAM_ACTION => DistributeManager :: ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS));
        }
        else
        {
            $html[] = $form->toHtml();
        }

        return implode("\n", $html);
    }
}
?>