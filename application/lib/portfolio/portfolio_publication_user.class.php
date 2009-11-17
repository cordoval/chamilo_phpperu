<?php
/**
 * $Id: portfolio_publication_user.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */


/**
 * This class describes a PortfolioPublicationUser data object
 *
 * @author Sven Vanpoucke
 */
class PortfolioPublicationUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * PortfolioPublicationUser properties
     */
    const PROPERTY_PORTFOLIO_PUBLICATION = 'portfolio_publication_id';
    const PROPERTY_USER = 'user_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PORTFOLIO_PUBLICATION, self :: PROPERTY_USER);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PortfolioDataManager :: get_instance();
    }

    /**
     * Returns the portfolio_publication of this PortfolioPublicationUser.
     * @return the portfolio_publication.
     */
    function get_portfolio_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PORTFOLIO_PUBLICATION);
    }

    /**
     * Sets the portfolio_publication of this PortfolioPublicationUser.
     * @param portfolio_publication
     */
    function set_portfolio_publication($portfolio_publication)
    {
        $this->set_default_property(self :: PROPERTY_PORTFOLIO_PUBLICATION, $portfolio_publication);
    }

    /**
     * Returns the user of this PortfolioPublicationUser.
     * @return the user.
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this PortfolioPublicationUser.
     * @param user
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function create()
    {
        $dm = PortfolioDataManager :: get_instance();
        return $dm->create_portfolio_publication_user($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>