<?php
/**
 * $Id: update_element.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.dynamic_form_manager.component
 * @author Sven Vanpoucke
 */

require_once (dirname(__FILE__) . '/../dynamic_form_element.class.php');
require_once (dirname(__FILE__) . '/../dynamic_form_element_builder_form.class.php');

class DynamicFormManagerUpdateElementComponent extends DynamicFormManagerComponent
{
    function run()
    {
    	$element_id = Request :: get(DynamicFormManager :: PARAM_DYNAMIC_FORM_ELEMENT_ID);
        $parameters = array(DynamicFormManager :: PARAM_DYNAMIC_FORM_ELEMENT_ID => $element_id);
        
    	$trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('UpdateElement')));
        $trail->add_help('dynamic form general');

        $condition = new EqualityCondition(DynamicFormElement :: PROPERTY_ID, $element_id);
        $element = AdminDataManager :: get_instance()->retrieve_dynamic_form_elements($condition)->next_result();

        $form = new DynamicFormElementBuilderForm(DynamicFormElementBuilderForm :: TYPE_EDIT, $element, $this->get_url($parameters), $this->get_user());

        if ($form->validate())
        {
            $success = $form->update_dynamic_form_element();
            $this->redirect(Translation :: get($success ? 'DynamicFormElementUpdated' : 'DynamicFormElementNotUpdated'), ($success ? false : true), 
            	array(DynamicFormManager :: PARAM_DYNAMIC_FORM_ACTION => DynamicFormManager :: ACTION_BUILD_DYNAMIC_FORM));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>