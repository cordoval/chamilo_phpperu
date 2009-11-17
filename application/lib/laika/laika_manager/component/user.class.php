<?php
/**
 * $Id: user.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component
 */
require_once dirname(__FILE__) . '/../laika_manager.class.php';
require_once dirname(__FILE__) . '/../laika_manager_component.class.php';
require_once dirname(__FILE__) . '/../../laika_utilities.class.php';
require_once dirname(__FILE__) . '/laika_user_browser/laika_user_browser_table.class.php';
require_once dirname(__FILE__) . '/../../forms/laika_user_filter_form.class.php';

class LaikaManagerUserComponent extends LaikaManagerComponent
{
    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME)), Translation :: get('Laika')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseUsers')));
        
        if (! LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, 'user', 'laika_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->display_header($trail);
        echo $this->get_user_table();
        $this->display_footer();
    }

    function get_user_table()
    {
        $html = array();
        $this->form = new LaikaBrowserFilterForm($this, $this->get_url());
        $table = new LaikaUserBrowserTable($this, $this->get_table_parameters(), $this->get_condition());
        
        $html[] = $this->form->display();
        $html[] = $table->as_html();
        
        return implode("\n", $html);
    }

    function get_condition()
    {
        $form = $this->form;
        
        return $form->get_filter_conditions();
    }

    function get_table_parameters()
    {
        $form = $this->form;
        $form_parameters = $form->get_filter_parameters();
        $parameters = $this->get_parameters();
        
        return array_merge($form_parameters, $parameters);
    }
}
?>