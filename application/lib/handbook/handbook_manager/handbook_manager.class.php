<?php
/**
 * @package application.lib.handbook.handbook_manager
 */
require_once dirname(__FILE__).'/../handbook_data_manager.class.php';
require_once dirname(__FILE__).'/component/handbook_publication_browser/handbook_publication_browser_table.class.php';

/**
 * A handbook manager
 *
 * @author Sven Vanpoucke
 * @author Nathalie Blocry
 */
 class HandbookManager extends WebApplication
 {
 	const APPLICATION_NAME = 'handbook';

	const PARAM_HANDBOOK_PUBLICATION = 'handbook_publication';
	const PARAM_DELETE_SELECTED_HANDBOOK_PUBLICATIONS = 'delete_selected_handbook_publications';
        const PARAM_HANDBOOK_PUBLICATION_ID = 'hpid';
        const PARAM_HANDBOOK_ID = 'hid';
        const PARAM_HANDBOOK_SELECTION_ID = 'hsid';

	const ACTION_DELETE_HANDBOOK_PUBLICATION = 'handbook_publication_deleter';
	const ACTION_EDIT_HANDBOOK_PUBLICATION = 'handbook_publication_editor';
	const ACTION_CREATE_HANDBOOK_PUBLICATION = 'handbook_publication_creator';
	const ACTION_BROWSE_HANDBOOK_PUBLICATIONS = 'handbook_publications_browser';
        const ACTION_VIEW_HANDBOOK = 'handbook_viewer';
        const ACTION_VIEW_HANDBOOK_PUBLICATION = 'handbook_publications_browser';
        const PARAM_HANDBOOK_OWNER_ID = 'handbook_owner';

        const DEFAULT_ACTION = self :: ACTION_BROWSE_HANDBOOK_PUBLICATIONS;


	const ACTION_BROWSE = 'browse';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function HandbookManager($user = null)
    {
    	parent :: __construct($user);
    	
    }


    
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED_HANDBOOK_PUBLICATIONS :

					$selected_ids = $_POST[HandbookPublicationBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_HANDBOOK_PUBLICATION);
					$_GET[self :: PARAM_HANDBOOK_PUBLICATION] = $selected_ids;
					break;
			}

		}
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving

	function count_handbook_publications($condition)
	{
		return HandbookDataManager :: get_instance()->count_handbook_publications($condition);
	}

	function retrieve_handbook_publications($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return HandbookDataManager :: get_instance()->retrieve_handbook_publications($condition, $offset, $count, $order_property);
	}

 	function retrieve_handbook_publication($id)
	{
		return HandbookDataManager :: get_instance()->retrieve_handbook_publication($id);
	}

	// Url Creation

	function get_create_handbook_publication_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_HANDBOOK_PUBLICATION));
	}

	function get_update_handbook_publication_url($handbook_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_HANDBOOK_PUBLICATION,
								    self :: PARAM_HANDBOOK_PUBLICATION => $handbook_publication->get_id()));
	}

 	function get_delete_handbook_publication_url($handbook_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_HANDBOOK_PUBLICATION,
								    self :: PARAM_HANDBOOK_PUBLICATION => $handbook_publication->get_id()));
	}

	function get_browse_handbook_publications_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_HANDBOOK_PUBLICATIONS));
	}

	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}

        function get_view_handbook_publication_url($handbook_publication_id)
        {
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_HANDBOOK_PUBLICATION, self::PARAM_HANDBOOK_PUBLICATION_ID => $handbook_id));
	}
        function get_view_handbook_url($handbook_id)
        {
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_HANDBOOK, self::PARAM_HANDBOOK_ID => $handbook_id));
	}


    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

}
?>