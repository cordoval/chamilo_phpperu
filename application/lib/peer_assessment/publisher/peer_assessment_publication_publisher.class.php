<?php
/**
 * author: Nick Van Loocke
 */
require_once dirname(__FILE__) . '/../forms/peer_assessment_publication_form.class.php';

class PeerAssessmentPublicationPublisher
{
    private $parent;

    function PeerAssessmentPublicationPublisher($parent)
    {
        $this->parent = $parent;
    }

    // Prints of the title of the object above the multiple properties
    // (publish for, from date, to date and hidden)


    function get_content_object_title($object)
    {
        if (is_null($object))
            return '';

        if (! is_array($object))
        {
            $ids = array($object);
        }

        $html = array();

        if (count($object) > 0)
        {
            $condition = new InCondition(ContentObject :: PROPERTY_ID, $ids, ContentObject :: get_table_name());
            $content_objects = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);

            $html[] = '<div class="content_object padding_10">';
            $html[] = '<div class="title">' . Translation :: get('SelectedContentObject') . '</div>';
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
        return implode("\n", $html);
    }

    // Publish the object


    function publish_content_object($object)
    {
        $published = false;
        $parameters = $this->parent->get_parameters();
        $parameters[RepoViewer :: PARAM_ID] = $object;
        $parameters[RepoViewer :: PARAM_ACTION] = RepoViewer :: ACTION_PUBLISHER;

        $form = new PeerAssessmentPublicationForm(PeerAssessmentPublicationForm :: TYPE_CREATE, $object, $this->parent->get_user(), $this->parent->get_url($parameters));

        if ($form->validate())
        {
            $publication = $form->create_content_object_publication();
            $published = true;
        }
        return $published;
    }

}
?>