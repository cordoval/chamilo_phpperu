<?php
/**
 * $Id: portfolio_publication.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/portfolio_rights.class.php';
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
    //not to sure what this property means TODO:find out
    const PROPERTY_PUBLISHED = 'published';
    //id of the parent portfolio-item if there is any???
    const PROPERTY_PARENT_ID = 'parent_id';


//    TODO:I don't think we need these properties from_date, to_date & hidden to not overcomplicate things
        const PROPERTY_FROM_DATE = 'from_date';
        const PROPERTY_TO_DATE = 'to_date';
       const PROPERTY_HIDDEN = 'hidden';


    private $location;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PortfolioDataManager :: get_instance();
    }

    /**
     * Returns the content_object of this PortfolioPublication.
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

    /**
     * Returns the from_date of this PortfolioPublication.
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    function get_location()
    {
        if(!isset($this->location))
        {
            $this->location = PortfolioRights::get_portfolio_location($this->get_id(), PortfolioRights::TYPE_PORTFOLIO_FOLDER, $this->get_publisher());
        }
        return $this->location;
    }

    /**
     * Returns the numeric identifier of the learning object's parent learning
     * object.
     * @return int The identifier.
     */
    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    /**
     * Sets the ID of this learning object's parent learning object.
     * @param int $parent The ID.
     */
    function set_parent_id($parent)
    {
        $this->set_default_property(self :: PROPERTY_PARENT_ID, $parent);
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
     * @param publisher
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
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
        //TODO if we want other users to be able to publish in someone's portfolio this needs to be changed as the location tree identifier doesn't need to be the publisher's id
        $user = $this->get_publisher();
        $parent_location = PortfolioRights::get_portfolio_root_id($user);
            if(!$parent_location)
            {
                $parent_location = PortfolioRights::create_portfolio_root($user)->get_id();
            }
        $object = $this->get_id();
            $this->location = PortfolioRights::create_location_in_portfolio_tree('portfolio', 'portfolio', $object, $parent_location, $user, true, false);

            return $this ->location;
    }


    function create()
    {
        $dm = PortfolioDataManager :: get_instance();
        $pub = $dm->create_portfolio_publication($this);

        $this->create_location();

        return $pub;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    static function get_publication_owner($pid)
    {
        return PortfolioManager::retrieve_portfolio_publication_user($pid);
    }

     static function get_item_owner($cid)
    {
        return PortfolioManager::retrieve_portfolio_item_user($cid);
    }

    /**
     *gets the portfolio-info-object of the portfolio's owner (wich contains update-information etc.)
     * @return portfolioInfo object
     */
    function get_portfolio_info()
    {
        $dm = PortfolioDataManager :: get_instance();
        $info = $dm->retrieve_porfolio_information_by_user($this->get_location()->get_tree_identifier());
        if($info)
        {
            $info = new PortfolioInformation();
            $info->set_last_updated_date(time());
            $info->set_user_id($this->get_location()->get_tree_identifier());
            $info->set_last_updated_item_id('0');
            $info->set_last_updated_item_type(PortfolioRights::TYPE_PORTFOLIO_FOLDER);
            $info->set_last_action(PortfolioInformation::ACTION_FIRST_PORTFOLIO_CREATED);
            $dm->create_portfolio_information($info);
        }
        

        return $info;
    }
}

?>