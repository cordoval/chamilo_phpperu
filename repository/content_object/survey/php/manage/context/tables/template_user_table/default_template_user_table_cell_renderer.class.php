<?php namespace repository\content_object\survey;
namespace repository\content_object\survey;

use user\UserDataManager;

use common\libraries\ObjectTableCellRenderer;

class DefaultSurveyTemplateUserTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct()
    {
    }


    function render_cell($column, $context_template)
    {

    	$property_name = $column->get_name();
    	
    	if($property_name == SurveyTemplateUser::PROPERTY_USER_ID){
    		$user_id = $context_template->get_default_property($property_name);
    		$user = UserDataManager::get_instance()->retrieve_user($user_id);
    		return $user_id.' : '.$user->get_fullname();
    	}else{
    		$context_id = $context_template->get_additional_property($property_name);
    		$context = SurveyContextDataManager::get_instance()->retrieve_survey_context_by_id($context_id);
    		return $context_id.' : '.$context->get_name();
    	}

    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>