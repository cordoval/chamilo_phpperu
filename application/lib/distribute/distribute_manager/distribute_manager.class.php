<?php
/**
 * $Id: distribute_manager.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.distribute.distribute_manager
 */
require_once dirname(__FILE__) . '/distribute_manager_component.class.php';
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
    
    const ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS = 'browse';
    const ACTION_VIEW_ANNOUNCEMENT_DISTRIBUTION = 'view';
    const ACTION_DISTRIBUTE_ANNOUNCEMENT = 'distribute';

    /**
     * Constructor
     * @param User $user The current user
     */
    function DistributeManager($user = null)
    {
        parent :: __construct($user);
        //$this->parse_input_from_table();
    }

    /**
     * Run this distribute manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS :
                $component = DistributeManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_DISTRIBUTE_ANNOUNCEMENT :
                $component = DistributeManagerComponent :: factory('Distributor', $this);
                break;
            case self :: ACTION_VIEW_ANNOUNCEMENT_DISTRIBUTION :
                $component = DistributeManagerComponent :: factory('Viewer', $this);
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS);
                $component = DistributeManagerComponent :: factory('Browser', $this);
        
        }
        $component->run();
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

	function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
    }

    function delete_content_object_publications($object_id)
    {
    
    }
    
 	function delete_content_object_publication($publication_id)
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