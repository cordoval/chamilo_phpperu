<?php
/**
 * $Id: distribute_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.distribute.distribute_manager
 */
require_once dirname(__FILE__) . '/../distribute_data_manager.class.php';
//require_once dirname(__FILE__).'/component/distribute_publication_browser/distribute_publication_browser_table.class.php';


/**
 * A distribute manager
 * @author Hans De Bisschop
 */
class DistributeManager extends WebApplication
{
    const APPLICATION_NAME = 'distribute';

    const PARAM_ANNOUNCEMENT_DISTRIBUTION = 'distribution';

    const ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS = 'browser';
    const ACTION_VIEW_ANNOUNCEMENT_DISTRIBUTION = 'viewer';
    const ACTION_DISTRIBUTE_ANNOUNCEMENT = 'distributer';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS;

    /**
     * Constructor
     * @param User $user The current user
     */
    function DistributeManager($user = null)
    {
        parent :: __construct($user);
        //$this->parse_input_from_table();
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Data Retrieving


    function count_announcement_distributions($condition)
    {
        return DistributeDataManager :: get_instance()->count_announcement_distributions($condition);
    }

    function retrieve_announcement_distributions($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return DistributeDataManager :: get_instance()->retrieve_announcement_distributions($condition, $offset, $count, $order_property);
    }

    function retrieve_announcement_distribution($id)
    {
        return DistributeDataManager :: get_instance()->retrieve_announcement_distribution($id);
    }

    // Url Creation


    function get_create_announcement_distribution_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_DISTRIBUTE_PUBLICATION));
    }

    function get_update_announcement_distribution_url($announcement_distribution)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_DISTRIBUTE_PUBLICATION, self :: PARAM_DISTRIBUTE_PUBLICATION => $announcement_distribution->get_id()));
    }

    function get_delete_announcement_distribution_url($announcement_distribution)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_DISTRIBUTE_PUBLICATION, self :: PARAM_DISTRIBUTE_PUBLICATION => $announcement_distribution->get_id()));
    }

    function get_browse_announcement_distributions_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_DISTRIBUTE_PUBLICATIONS));
    }

    function get_announcement_distribution_viewing_url($announcement_distribution)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ANNOUNCEMENT_DISTRIBUTION, self :: PARAM_ANNOUNCEMENT_DISTRIBUTION => $announcement_distribution->get_id()));
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