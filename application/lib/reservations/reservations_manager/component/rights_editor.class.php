<?php
/**
 * $Id: rights_editor.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager_component.class.php';

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class ReservationsManagerRightsEditorComponent extends ReservationsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $type = Request :: get('type');
        $id = Request :: get('id');
        
        $location = ReservationsRights :: get_location_by_identifier($type, $id);
        
        $manager = new RightsEditorManager($this, array($location));
        $manager->run();
    }

    function get_available_rights()
    {
        $type = Request :: get('type');
        $rights = array();
        
        if ($type == 'category')
        {
            $rights['VIEW_RIGHT'] = ReservationsRights :: VIEW_RIGHT;
            $rights['MAKE_RESERVATION_RIGHT'] = ReservationsRights :: MAKE_RESERVATION_RIGHT;
            $rights['ADD_RIGHT'] = ReservationsRights :: ADD_RIGHT;
            $rights['EDIT_RIGHT'] = ReservationsRights :: EDIT_RIGHT;
            $rights['DELETE_RIGHT'] = ReservationsRights :: DELETE_RIGHT;
        }
        else
        {
            $rights['VIEW_RIGHT'] = ReservationsRights :: VIEW_RIGHT;
            $rights['MAKE_RESERVATION_RIGHT'] = ReservationsRights :: MAKE_RESERVATION_RIGHT;
        }
        
        return $rights;
    }

    function display_header($old_trail)
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(parent :: get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        
        $type = Request :: get('type');
        
        if ($type == 'category')
        {
            $trail->add(new Breadcrumb(parent :: get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES)), Translation :: get('ManageCategories')));
        }
        else
        {
            $trail->add(new Breadcrumb(parent :: get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)), Translation :: get('ManageItems')));
        }
        
        $trail->merge($old_trail);
        
        parent :: display_header($trail);
    }

    function get_url($parameters)
    {
        $parameters['type'] = Request :: get('type');
        $parameters['id'] = Request :: get('id');
        
        return parent :: get_url($parameters);
    }

    function get_parameters()
    {
        $parameters = parent :: get_parameters();
        $parameters['type'] = Request :: get('type');
        $parameters['id'] = Request :: get('id');
        return $parameters;
    }

}
?>