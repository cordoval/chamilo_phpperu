<?php
/**
 * @package application.lib.ovis.ovis_manager
 */
require_once dirname(__FILE__).'/ovis_manager_component.class.php';
require_once dirname(__FILE__).'/../ovis_data_manager.class.php';

/**
 * A ovis manager
 *
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
 class OvisManager extends WebApplication
 {
 	const APPLICATION_NAME = 'ovis';


	const ACTION_BROWSE = 'browse';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function OvisManager($user = null)
    {
    	parent :: __construct($user);
    	$this->parse_input_from_table();
    }

    /**
	 * Run this ovis manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE:
				$component = OvisManagerComponent :: factory('Browser', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE);
				$component = OvisManagerComponent :: factory('Browser', $this);

		}
		$component->run();
	}

	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			switch ($_POST['action'])
			{
			}

		}
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving

	// Url Creation

	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}

	// Dummy Methods which are needed because we don't work with learning objects
	function content_object_is_published($object_id)
	{
	}

	function any_content_object_is_published($object_ids)
	{
	}

	function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
	{
	}

	function get_content_object_publication_attribute($object_id)
	{

	}

	function count_publication_attributes($type = null, $condition = null)
	{

	}

	function delete_content_object_publications($object_id)
	{

	}

	function update_content_object_publication_id($publication_attr)
	{

	}

	function get_content_object_publication_locations($content_object)
	{

	}

	function publish_content_object($content_object, $location)
	{

	}
}
?>