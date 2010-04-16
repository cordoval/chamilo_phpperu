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
        return implode("\n", $html);
    }

    function publish_content_object($object)
    {
    	
    	$parameters = $this->parent->get_parameters();
        $parameters['object'] = $object;
        
    	$form = new PeerAssessmentPublicationForm(PeerAssessmentPublicationForm :: TYPE_CREATE, $object, $this->parent->get_user(), $this->parent->get_url($parameters));
        
    	if ($form->validate())
        {
        	// Inserting the data to the peer assessment publication database table
        	
	        $author = $publisher = $this->parent->get_user_id();
	        $date = mktime(date());
	        
	        $values = $this->exportValues();
	        if ($values[self :: PARAM_FOREVER] != 0)
	        {
	            $from = $to = 0;
	        }
	        else
	        {
	            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
	            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
	        }
	        $hidden = ($values[PeerAssessmentPublication :: PROPERTY_HIDDEN] ? 1 : 0);
	
	        $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
	        $groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];
	        
	        $pb = new PeerAssessmentPublication();
	        $pb->set_content_object($object);
	        $pb->set_parent_id($author);
	        $pb->set_category(0);
	        $pb->set_published($date);
	        $pb->set_modified(0);
	        
	        $pb->set_publisher($publisher);
			$pb->set_from_date(0);
			$pb->set_to_date(0);		
			$pb->set_hidden(0);
			$pb->set_target_users(0);
	        $pb->set_target_groups(0);
	        
	        if (! $pb->create())
	        {
	            $error = true;
	        }
	        
	        if ($error)
	        {
	            $message = Translation :: get('ObjectNotPublished');
	        }
	        else
	        {
	            $message = Translation :: get('ObjectPublished');
	        }
	        
	        $this->parent->redirect($message, null, array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_BROWSE_PEER_ASSESSMENT_PUBLICATIONS));       
	 
        }
    }
    
}
?>