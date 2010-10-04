<?php
/**
 * $Id: viewer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.dynamic_form_manager.component
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/../dynamic_form_execute_form.class.php';

class DynamicFormManagerExecuterComponent extends DynamicFormManager
{
    function run()
    {
    	$trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('dynamic form general');
 
        $form = new DynamicFormExecuteForm($this->get_form(), $this->get_url(), $this->get_user(), $this->get_dynamic_form_title());

        if ($form->validate())
        {
            $success = $form->update_values();
            $this->redirect(Translation :: get($success ? 'DynamicFormExecuted' : 'DynamicFormNotExecuted'), ($success ? false : true), array());
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