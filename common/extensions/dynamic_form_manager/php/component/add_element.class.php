<?php
/**
 * $Id: add_element.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.dynamic_form_manager.component
 * @author Sven Vanpoucke
 */

require_once (dirname(__FILE__) . '/../dynamic_form_element_builder_form.class.php');

class DynamicFormManagerAddElementComponent extends DynamicFormManager
{
	function run()
    {
        $type = Request :: get(DynamicFormManager :: PARAM_DYNAMIC_FORM_ELEMENT_TYPE);
        $parameters = array(DynamicFormManager :: PARAM_DYNAMIC_FORM_ELEMENT_TYPE => $type);
        
    	$trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('AddElement')));
        $trail->add_help('dynamic form general');

        $element = new DynamicFormElement();
        $element->set_type($type);
        $element->set_dynamic_form_id($this->get_form()->get_id());

        $form = new DynamicFormElementBuilderForm(DynamicFormElementBuilderForm :: TYPE_CREATE, $element, $this->get_url($parameters), $this->get_user());

        if ($form->validate())
        {
            $success = $form->create_dynamic_form_element();
            $this->redirect(Translation :: get($success ? 'DynamicFormElementAdded' : 'DynamicFormElementNotAdded'), ($success ? false : true), 
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