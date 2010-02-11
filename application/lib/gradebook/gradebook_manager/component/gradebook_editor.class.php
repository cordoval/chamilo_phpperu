<?php

require_once dirname(__FILE__).'/../../forms/gradebook_form.class.php';

class GradebookManagerGradebookEditorComponent extends GradebookManagerComponent
{
	function run()
	{

		if (!GradebookRights :: is_allowed(GradebookRights :: EDIT_RIGHT, 'browser', 'sts_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$id = $_GET[GradebookManager :: PARAM_GRADEBOOK_ID];
		if ($id)
		{
			$gradebook = $this->retrieve_gradebook($id);
			$trail = new BreadcrumbTrail();

			$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('GradeBook')));
			$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK)), Translation :: get('BrowseGradeBook')));
			$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_GRADEBOOK, GradebookManager :: PARAM_GRADEBOOK_ID => $id)), $gradebook->get_name()));
			$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_EDIT_GRADEBOOK, GradebookManager :: PARAM_GRADEBOOK_ID => $id)), Translation :: get('Edit').' '.$gradebook->get_name()));

			$form = new GradebookForm(GradebookForm :: TYPE_EDIT, $gradebook, $this->get_url(array(GradebookManager :: PARAM_GRADEBOOK_ID => $id)), $this->get_user());
				
			if($form->validate())
			{

				$success = $form->update_gradebook();
//				$gradebook = $form->get_gradebook();
				$this->redirect( Translation :: get($success ? 'GradebookUpdated' : 'GradebookNotUpdated'), ($success ? false : true), array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_GRADEBOOK, GradebookManager :: PARAM_GRADEBOOK_ID => $gradebook->get_id()));
			}
			else
			{
				$this->display_header($trail);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGradebookSelected')));
		}
	}
}
?>