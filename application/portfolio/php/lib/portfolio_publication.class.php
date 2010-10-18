<?php
/**
 * $Id: portfolio_publication.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/rights/portfolio_rights.class.php';
/**
 * This class describes a PortfolioPublication data object
 *
 * @author Sven Vanpoucke
 */
class PortfolioPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * PortfolioPublication properties
     */
    //id of the content object that is being published in the portfolio
    const PROPERTY_CONTENT_OBJECT = 'content_object_id';
   //id of the user who published the portfolio item
    const PROPERTY_PUBLISHER = 'publisher_id';
    //date this portfolio was published in the portfolio application
    const PROPERTY_PUBLISHED = 'published';
    //id of the owner of this portfolio
    const PROPERTY_OWNER_ID = 'owner_id';





    //id of the parent portfolio-item if there is any???
    //const PROPERTY_PARENT_ID = 'parent_id';



    private $location;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self::PROPERTY_OWNER_ID));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PortfolioDataManager :: get_instance();
    }

    /**
     * Returns the id of the content_object of this PortfolioPublication.
     * @return the content_object.
     */
    function get_content_object()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT);
    }

    /**
     * Sets the content_object of this PortfolioPublication.
     * @param content_object_id
     */
    function set_content_object($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT, $content_object_id);
    }



    function get_location()
    {
//        if(!isset($this->location))
//        {
            $this->location = PortfolioRights::get_portfolio_location($this->get_id(), PortfolioRights::TYPE_PORTFOLIO_FOLDER, $this->get_publisher());
//        }
        return $this->location;
    }




    /**
     * Returns the publisher of this PortfolioPublication.
     * @return the publisher.
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Sets the publisher of this PortfolioPublication.
     * @param publisher_id
     */
    function set_publisher($publisher_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher_id);
    }

      /**
     * Returns the owner of this PortfolioPublication.
     * @return the owner id.
     */
    function get_owner()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_ID);
    }

    /**
     * Sets the owner of this PortfolioPublication.
     * @param owner_id
     */
    function set_owner($owner_id)
    {
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
    }

    /**
     * Returns the publishing date of this PortfolioPublication.
     * @return the date the publication was published
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }


    /**
     * Sets the publishedate of this PortfolioPublication.
     * @param publishe_date:date the item was published
     */
    function set_published($publishe_date)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $publishe_date);
    }

    /*
     * creates a location for the portfolio-publication under the user's portfolio-tree root.
     * if necessary (= first portfolio publication for the user) the root is created
     * @return the location or "false"
     */
    function create_location()
    {
        $user_id = $this->get_owner();
        $parent_location = null;
        $object_id = $this->get_id();
        $this->location = PortfolioRights::create_location_in_portfolio_tree(PortfolioRights::TYPE_PORTFOLIO_FOLDER, PortfolioRights::TYPE_PORTFOLIO_FOLDER, $object_id, $parent_location, $user_id, true, false, true);
        return $this ->location;
    }


    function create()
    {
        $dm = PortfolioDataManager :: get_instance();
        $pub = $dm->create_portfolio_publication($this);
        $this->create_location();
        $this->update_information(PortfolioInformation::ACTION_PORTFOLIO_ADDED);
        return $pub;
    }

    /**
     * update the portfolio information for the action performed on this publication
     * @param <type> $action
     * @return <bool> $success
     */
    function update_information($action)
    {
        return PortfolioManager::update_portfolio_info($this->get_id, PortfolioRights::TYPE_PORTFOLIO_FOLDER, $action, $this->get_owner());
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    static function get_publication_owner($pid)
    {
        return PortfolioManager::retrieve_portfolio_publication_owner($pid);
    }

     static function get_item_owner($cid)
    {
        return PortfolioManager::retrieve_portfolio_item_owner($cid);
    }

   
}

?>