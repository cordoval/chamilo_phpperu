<?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'forms/external_item_form.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'data_provider/gradebook_tree_menu_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'forms/external_grade_evaluation_input_form.class.php';
class GradebookManagerExternalCreatorComponent extends GradebookManager
{
	function run()
	{
		$trail = $this->get_general_breadcrumbs();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL)), Translation :: get('CreatingExternal')));
		
		$form = new ExternalItemForm(ExternalItemForm :: TYPE_CREATE, $this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL, GradebookTreeMenuDataProvider :: PARAM_ID => Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID))), Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID),$this->get_user());
    	
    	if($form->validate())
    	{
			$this->redirect(null, null, array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL_GRADE, GradebookTreeMenuDataProvider :: PARAM_ID => Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID), 'values' => $form->exportValues()));
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