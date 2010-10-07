<?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'forms/create_external_item_form.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'data_provider/gradebook_tree_menu_data_provider.class.php';

class GradebookManagerEditExternalEvaluationComponent extends GradebookManager
{
	function run()
	{
		$trail = $this->get_general_breadcrumbs();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL)), Translation :: get('CreatingExternal')));
		
		$form = new CreateExternalItemForm(CreateExternalItemForm :: TYPE_EDIT, $this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_CREATE_EXTERNAL, GradebookTreeMenuDataProvider :: PARAM_ID => Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID))), Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID),$this->get_user());
    
    	if($form->validate())
    	{
    		$success = $form->update_evaluation();
            $this->redirect($success ? Translation :: get('ExternalGradesUpdated') : Translation :: get('ExternalGradesNotUpdated'), ! $success, array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK));
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