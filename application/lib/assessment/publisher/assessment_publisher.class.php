<?php
/**
 * $Id: assessment_publisher.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.publisher
 */
require_once dirname(__FILE__) . '/../forms/assessment_publication_form.class.php';

/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class AssessmentPublisher
{
    private $parent;

    function AssessmentPublisher($parent)
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
        $parameters[RepoViewer::PARAM_ID] = $ids;
        $parameters[RepoViewer::PARAM_ACTION] = RepoViewer :: ACTION_PUBLISHER;

        $form = new AssessmentPublicationForm(AssessmentPublicationForm :: TYPE_MULTI, $ids, $this->parent->get_user(), $this->parent->get_url($parameters));
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
            
            if(count($ids) == 1 && !is_null(Request :: post('publish_and_build')))
            {
            	$object = RepositoryDataManager :: get_instance()->retrieve_content_object($ids[0]);
            	if($object->get_type() == Assessment :: get_type_name() || $object->get_type() == Survey :: get_type_name())
            		$this->parent->redirect($message, (! $publication ? true : false), array(Application :: PARAM_ACTION => AssessmentManager :: ACTION_BUILD_ASSESSMENT, AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION => $form->get_publication()->get_id()));
            }
            	
            $this->parent->redirect($message, (! $publication ? true : false), array(Application :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
        }
        else
        {
            $html[] = $form->toHtml();
        }

        return implode("\n", $html);
    }
}
?>