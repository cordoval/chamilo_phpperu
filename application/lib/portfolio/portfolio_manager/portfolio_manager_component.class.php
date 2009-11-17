<?php
/**
 * $Id: portfolio_manager_component.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 * Basic functionality of a component to talk with the portfolio application
 * @author Sven Vanpoucke
 */
abstract class PortfolioManagerComponent
{
    /**
     * The number of components allready instantiated
     */
    private static $component_count = 0;
    
    /**
     * The portfolio in which this componet is used
     */
    private $portfolio;
    
    /**
     * The id of this component
     */
    private $id;

    /**
     * Constructor
     * @param Portfolio $portfolio The portfolio which
     * provides this component
     */
    protected function PortfolioManagerComponent($portfolio)
    {
        $this->pm = $portfolio;
        $this->id = ++ self :: $component_count;
    }

    /**
     * @see PortfolioManager :: redirect()
     */
    function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
    {
        return $this->get_parent()->redirect($action, $message, $error_message, $extra_params);
    }

    /**
     * @see PortfolioManager :: get_parameter()
     */
    function get_parameter($name)
    {
        return $this->get_parent()->get_parameter($name);
    }

    /**
     * @see PortfolioManager :: get_parameters()
     */
    function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    /**
     * @see PortfolioManager :: set_parameter()
     */
    function set_parameter($name, $value)
    {
        return $this->get_parent()->set_parameter($name, $value);
    }

    /**
     * @see PortfolioManager :: get_url()
     */
    function get_url($parameters = array (), $encode = false, $filter = false, $filterOn = array())
    {
        return $this->get_parent()->get_url($parameters, $encode, $filter, $filterOn);
    }

    /**
     * @see PortfolioManager :: display_header()
     */
    function display_header($breadcrumbtrail, $display_search = false)
    {
        return $this->get_parent()->display_header($breadcrumbtrail, $display_search);
    }

    /**
     * @see PortfolioManager :: display_message()
     */
    function display_message($message)
    {
        return $this->get_parent()->display_message($message);
    }

    /**
     * @see PortfolioManager :: display_error_message()
     */
    function display_error_message($message)
    {
        return $this->get_parent()->display_error_message($message);
    }

    /**
     * @see PortfolioManager :: display_warning_message()
     */
    function display_warning_message($message)
    {
        return $this->get_parent()->display_warning_message($message);
    }

    /**
     * @see PortfolioManager :: display_footer()
     */
    function display_footer()
    {
        return $this->get_parent()->display_footer();
    }

    /**
     * @see PortfolioManager :: display_error_page()
     */
    function display_error_page($message)
    {
        $this->get_parent()->display_error_page($message);
    }

    /**
     * @see PortfolioManager :: display_warning_page()
     */
    function display_warning_page($message)
    {
        $this->get_parent()->display_warning_page($message);
    }

    /**
     * @see PortfolioManager :: display_popup_form
     */
    function display_popup_form($form_html)
    {
        $this->get_parent()->display_popup_form($form_html);
    }

    /**
     * @see PortfolioManager :: get_parent
     */
    function get_parent()
    {
        return $this->pm;
    }

    /**
     * @see PortfolioManager :: get_web_code_path
     */
    function get_path($path_type)
    {
        return $this->get_parent()->get_path($path_type);
    }

    /**
     * @see PortfolioManager :: get_user()
     */
    function get_user()
    {
        return $this->get_parent()->get_user();
    }

    /**
     * @see PortfolioManager :: get_user_id()
     */
    function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

    //Data Retrieval
    

    function count_portfolio_publications($condition)
    {
        return $this->get_parent()->count_portfolio_publications($condition);
    }

    function retrieve_portfolio_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_portfolio_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_portfolio_publication($id)
    {
        return $this->get_parent()->retrieve_portfolio_publication($id);
    }

    function count_portfolio_publication_groups($condition)
    {
        return $this->get_parent()->count_portfolio_publication_groups($condition);
    }

    function retrieve_portfolio_publication_groups($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_portfolio_publication_groups($condition, $offset, $count, $order_property);
    }

    function retrieve_portfolio_publication_group($id)
    {
        return $this->get_parent()->retrieve_portfolio_publication_group($id);
    }

    function count_portfolio_publication_users($condition)
    {
        return $this->get_parent()->count_portfolio_publication_users($condition);
    }

    function retrieve_portfolio_publication_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_portfolio_publication_users($condition, $offset, $count, $order_property);
    }

    function retrieve_portfolio_publication_user($id)
    {
        return $this->get_parent()->retrieve_portfolio_publication_user($id);
    }

    // Url Creation
    

    function get_create_portfolio_publication_url()
    {
        return $this->get_parent()->get_create_portfolio_publication_url();
    }

    function get_delete_portfolio_publication_url($portfolio_publication)
    {
        return $this->get_parent()->get_delete_portfolio_publication_url($portfolio_publication);
    }

    function get_create_portfolio_item_url($parent_id)
    {
        return $this->get_parent()->get_create_portfolio_item_url($parent_id);
    }

    function get_delete_portfolio_item_url($portfolio_item_cid)
    {
        return $this->get_parent()->get_delete_portfolio_item_url($portfolio_item_cid);
    }

    function get_view_portfolio_url($user)
    {
        return $this->get_parent()->get_view_portfolio_url($user);
    }

    function get_browse_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    /**
     * Create a new profile component
     * @param string $type The type of the component to create.
     * @param Profile $portfolio The pm in
     * which the created component will be used
     */
    static function factory($type, $portfolio)
    {
        $filename = dirname(__FILE__) . '/component/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" component');
        }
        $class = 'PortfolioManager' . $type . 'Component';
        require_once $filename;
        return new $class($portfolio);
    }
}
?>