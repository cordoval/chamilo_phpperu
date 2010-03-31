<?php

/**
 * class to handle the different rights in the portfolio application
 *
 * @author nblocry
 */
class portfolioRights {

    const VIEW_RIGHT = '1';
    const EDIT_RIGHT = '2';
    const VIEW_FEEDBACK_RIGHT = '3';
    const GIVE_FEEDBACK_RIGHT = '4';

    const PORTFOLIO_FOLDER = 'portfolio';
    const PORTFOLIO_ITEM = 'portfolio_item';

    /**
     * this method checks wether the current user has a specific right on a specific location
     * @param $right: right to check
     * @param $location: location to check the right for
     * @param $type: type of object the location refers to, can be "portfolio" or "portfolio-item"
     */
        function is_allowed($right, $location, $type)
    {
        return RightsUtilities::is_allowed($right, $location, $type, PortfolioManager::APPLICATION_NAME, null, $user_id, 'portfolio_tree');
    }



    /**
     * create a portfolio_tree for a specific user. The portfolio-tree is a tree of locations that represent
     * the structure of the portfolio and can be used to have items inherit rights
     *  @param $user: id of the user that owns the portfolio to identify it (tree-identifier)
     */
    static function create_portfolio_root($user)
    {
        return RightsUtilities :: create_subtree_root_location(PortfolioManager::APPLICATION_NAME, $user, 'portfolio_tree', true);
    }
    /**
     * get the root of a specific user's portfolio-tree.
     * @param $user_id: id of the user the portfolio belongs to, this is the tree_identifier
     * @return returns the id of the root location or "false" when no root location is found
     */
    static function get_portfolio_root_id($user_id)
    {
        return RightsUtilities::get_root_id(PortfolioManager::APPLICATION_NAME, 'portfolio_tree', $user_id);
    }



    /**
     * create a location for a portfolio or portfolio-item in the portfolio-tree
     * @param $type: type of object the location refers to, can be "portfolio" or "portfolio-item"
     * @param $identifier: id of the object the location refers to
     * @param $user_id: id of the user that owns the portfolio to identify in wich portfolio-tree to look
     * @param $name: name for the location
     *@param $parent: parent of the location
     * @param $inherit: true or false --> does the location inherit rights from it's parents
     * @param $locked: true of false --> can children override rights set for this location?
     * @return location when location has been created or false
     */
    static function create_location_in_portfolio_tree($name, $type, $identifier, $parent, $user_id, $inherit, $locked)
    {
    	return RightsUtilities::create_location($name, PortfolioManager::APPLICATION_NAME, $type, $identifier, $inherit, $parent, $locked, $user_id, 'portfolio_tree', true);
    }
    /**
     * this method wil return the location of a certain portfolio-publication
     * or of a specific portfolio-item based on the id of the published item/portfolio
     * @param $type: type of object the location refers to, can be "portfolio" or "portfolio-item"
     * @param $identifier: id of the object the location refers to
     * @param $user_id: id of the user that owns the portfolio to identify in wich portfolio-tree to look
     * @return location
     */
    function get_location_by_identifier($type, $identifier, $user_id)
    {
        return RightsUtilities::get_location_by_identifier(PortfolioManager::APPLICATION_NAME, $type, $identifier, $user_id, 'portfolio_tree');
    }

    /**
     * this method wil return the id of the location of a certain portfolio-publication
     * or of a specific portfolio-item based on the id of the published item/portfolio
     * @param $type: type of object the location refers to, can be "portfolio" or "portfolio-item"
     * @param $identifier: id of the object the location refers to
     * @param $user_id: id of the user that owns the portfolio to identify in wich portfolio-tree to look
     * @return location_id
     */
   static function get_location_id_by_identifier_from_user_subtree($type, $identifier, $user_id)
    {
    	return RightsUtilities :: get_location_id_by_identifier(PortfolioManager::APPLICATION_NAME, $type, $identifier, $user_id, 'portfolio_tree');
    }





}
?>
