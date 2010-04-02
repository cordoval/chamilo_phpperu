<?php
/**
 * @package application.streaming_video.streaming_video.component
 */
require_once dirname(__FILE__).'/../streaming_video_manager.class.php';
require_once dirname(__FILE__).'/../streaming_video_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/parameter_form.class.php';

/**
 * Component to edit an existing parameter object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
class StreamingVideoManagerParameterUpdaterComponent extends StreamingVideoManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE)), Translation :: get('BrowseStreamingVideo')));
		$trail->add(new Breadcrumb($this->get_url(array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_parameterS)), Translation :: get('BrowseParameters')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateParameter')));

		$parameter = $this->retrieve_parameter(Request :: get(StreamingVideoManager :: PARAM_parameter));
		$form = new ParameterForm(ParameterForm :: TYPE_EDIT, $parameter, $this->get_url(array(StreamingVideoManager :: PARAM_parameter => $parameter->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_parameter();
			$this->redirect($success ? Translation :: get('ParameterUpdated') : Translation :: get('ParameterNotUpdated'), !$success, array(StreamingVideoManager :: PARAM_ACTION => StreamingVideoManager :: ACTION_BROWSE_parameterS));
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