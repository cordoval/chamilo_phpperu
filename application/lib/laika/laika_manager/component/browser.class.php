<?php
/**
 * $Id: browser.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component
 */
require_once dirname(__FILE__) . '/../laika_manager.class.php';
require_once dirname(__FILE__) . '/../laika_manager_component.class.php';
require_once dirname(__FILE__) . '/laika_calculated_result_browser/laika_calculated_result_browser_table.class.php';
require_once dirname(__FILE__) . '/../../forms/laika_browser_filter_form.class.php';

class LaikaManagerBrowserComponent extends LaikaManagerComponent
{
    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_HOME)), Translation :: get('Laika')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseResults')));

        if (! LaikaRights :: is_allowed(LaikaRights :: VIEW_RIGHT, LaikaRights :: LOCATION_BROWSER, 'laika_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $this->display_header($trail);
        echo $this->get_calculated_result_table();
        $this->display_footer();
    }

    function get_calculated_result_table()
    {
        $html = array();

        $this->form = new LaikaBrowserFilterForm($this, $this->get_url());
        $table = new LaikaCalculatedResultBrowserTable($this, $this->get_table_parameters(), $this->get_condition());

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