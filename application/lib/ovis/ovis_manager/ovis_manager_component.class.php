<?php

/**
 * @package application.lib.ovis.ovis_manager
 * Basic functionality of a component to talk with the ovis application
 *
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
abstract class OvisManagerComponent extends WebApplicationComponent
{
	/**
	 * Constructor
	 * @param Ovis $ovis The ovis which
	 * provides this component
	 */
	function OvisManagerComponent($ovis)
	{
		parent :: __construct($ovis);
	}

	//Data Retrieval

	// Url Creation


	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}
}
?>