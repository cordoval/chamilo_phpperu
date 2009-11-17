<?php
/**
 * $Id: portfolio_publication_group.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */


/**
 * This class describes a PortfolioPublicationGroup data object
 *
 * @author Sven Vanpoucke
 */
class PortfolioPublicationGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * PortfolioPublicationGroup properties
     */
    const PROPERTY_PORTFOLIO_PUBLICATION = 'portfolio_publication_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PORTFOLIO_PUBLICATION, self :: PROPERTY_GROUP_ID);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PortfolioDataManager :: get_instance();
    }

    /**
     * Returns the portfolio_publication of this PortfolioPublicationGroup.
     * @return the portfolio_publication.
     */
    function get_portfolio_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PORTFOLIO_PUBLICATION);
    }

    /**
     * Sets the portfolio_publication of this PortfolioPublicationGroup.
     * @param portfolio_publication
     */
    function set_portfolio_publication($portfolio_publication)
    {
        $this->set_default_property(self :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication);
    }

    /**
     * Returns the group_id of this PortfolioPublicationGroup.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this PortfolioPublicationGroup.
     * @param group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function create()
    {
        $dm = PortfolioDataManager :: get_instance();
        return $dm->create_portfolio_publication_group($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>