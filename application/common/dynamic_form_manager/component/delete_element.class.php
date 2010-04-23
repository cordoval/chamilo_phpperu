<?php
/**
 * $Id: delete_element.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.dynamic_form_manager.component
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/../dynamic_form_element.class.php';

class DynamicFormManagerDeleteElementComponent extends DynamicFormManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(DynamicFormManager :: PARAM_DYNAMIC_FORM_ELEMENT_ID);
        
	    if (! $this->get_user()->is_platform_admin())
        {
            $trail = new BreadcrumbTrail(false);
            $trail->add_help('dynamic form manager general');
            $this->display_header($trail);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        if(!is_array($ids))
        {
        	$ids = array($ids);
        }
        
        if (count($ids) > 0)
        {
        	$failures = 0;
        	
			foreach($ids as $id)
			{
	            $dynamic_form_element = AdminDataManager :: get_instance()->retrieve_dynamic_form_elements(new EqualityCondition(
	            	DynamicFormElement :: PROPERTY_ID, $id))->next_result();
	            
	            if (!$dynamic_form_element->delete())
	            {
	               $failures++;
	            }
			}
            
			$message = $this->get_result($failures, count($ids), 'DynamicFormElementNotDeleted' , 'DynamicFormElementsNotDeleted', 'DynamicFormElementDeleted', 'DynamicFormElementsDeleted');
			
            $this->redirect($message, ($failures > 0), array(DynamicFormManager :: PARAM_DYNAMIC_FORM_ACTION => DynamicFormManager :: ACTION_BUILD_DYNAMIC_FORM));
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>