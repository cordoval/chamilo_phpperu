<?php
/**
 * $Id: laika_browser_filter_form.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.forms
 */
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_manager/laika_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_data_manager.class.php';

class LaikaBrowserFilterForm extends FormValidator
{
    const BROWSER_FILTER_SCALE = 'filter_scale';
    const BROWSER_FILTER_CODE = 'filter_code';
    const BROWSER_FILTER_GROUP = 'filter_group';

    private $manager;
    private $renderer;

    /**
     * Creates a new search form
     * @param RepositoryManager $manager The repository manager in which this
     * search form will be displayed
     * @param string $url The location to which the search request should be
     * posted.
     */
    function __construct($manager, $url)
    {
        parent :: __construct('laika_browser_filter_form', 'post', $url);

        $this->renderer = clone $this->defaultRenderer();
        $this->manager = $manager;

        $this->build_form();

        $this->setDefaults();

        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function build_form()
    {
        $ldm = LaikaDataManager :: get_instance();

        $this->renderer->setFormTemplate('<form {attributes}><div class="filter_form">{content}</div><div class="clear">&nbsp;</div></form>');
        $this->renderer->setElementTemplate('<div class="row"><div class="formw">{label}&nbsp;{element}</div></div>');

        // The Laika Scales
        $scales = $ldm->retrieve_laika_scales(null, null, null, new ObjectTableOrder(LaikaScale :: PROPERTY_TITLE));
        $scale_options = array();
        while ($scale = $scales->next_result())
        {
            $scale_options[$scale->get_id()] = $scale->get_title();
        }

        // The Laika Percentile Codes
        $codes = $ldm->retrieve_percentile_codes();
        $code_options = array();
        foreach ($codes as $code)
        {
            $code_options[$code] = $code;
        }

        $this->addElement('select', self :: BROWSER_FILTER_SCALE, Translation :: get('Scale'), $scale_options);
        $this->addElement('select', self :: BROWSER_FILTER_GROUP, Translation :: get('Group'), $this->get_groups());
        $this->addElement('select', self :: BROWSER_FILTER_CODE, Translation :: get('Code'), $code_options);
        $this->addElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'normal search'));
        //$this->addElement('submit', 'search', Translation :: get('Filter'));
    }

    function get_filter_conditions()
    {
        if (! $this->validate() && ! $this->get_parameters_are_set())
        {
            return null;
        }

        $filter_scale = Request :: get(self :: BROWSER_FILTER_SCALE);
        $filter_code = Request :: get(self :: BROWSER_FILTER_CODE);
        $filter_group = Request :: get(self :: BROWSER_FILTER_GROUP);

        $form_validates = $this->validate();

        if ($form_validates)
        {
            $values = $this->exportValues();

            $filter_scale = $values[self :: BROWSER_FILTER_SCALE];
            $filter_code = $values[self :: BROWSER_FILTER_CODE];
            $filter_group = $values[self :: BROWSER_FILTER_GROUP];
        }
        else
        {
            $filter_scale = Request :: get(self :: BROWSER_FILTER_SCALE);
            $filter_code = Request :: get(self :: BROWSER_FILTER_CODE);
            $filter_group = Request :: get(self :: BROWSER_FILTER_GROUP);
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(LaikaCalculatedResult :: PROPERTY_SCALE_ID, $filter_scale);
        $conditions[] = new EqualityCondition(LaikaCalculatedResult :: PROPERTY_PERCENTILE_CODE, $filter_code);

        if ($filter_group != '0')
        {
            $gdm = GroupDataManager :: get_instance();
            $group = $gdm->retrieve_group($filter_group);
            $users = $group->get_users(true, true);

            if (count($users) == 0)
            {
                $users = array('0');
            }

            $conditions[] = new InCondition(LaikaAttempt :: PROPERTY_USER_ID, $users, LaikaAttempt :: get_table_name());
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }

    function get_filter_parameters()
    {
        if (! $this->validate() && ! $this->get_parameters_are_set())
        {
            return array();
        }

        if ($this->validate())
        {
            $values = $this->exportValues();

            $parameters = array();
            $parameters[self :: BROWSER_FILTER_SCALE] = $values[self :: BROWSER_FILTER_SCALE];
            $parameters[self :: BROWSER_FILTER_CODE] = $values[self :: BROWSER_FILTER_CODE];
            $parameters[self :: BROWSER_FILTER_GROUP] = $values[self :: BROWSER_FILTER_GROUP];

            return $parameters;
        }
        else
        {
            $parameters = array();
            $parameters[self :: BROWSER_FILTER_SCALE] = Request :: get(self :: BROWSER_FILTER_SCALE);
            $parameters[self :: BROWSER_FILTER_CODE] = Request :: get(self :: BROWSER_FILTER_CODE);
            $parameters[self :: BROWSER_FILTER_GROUP] = Request :: get(self :: BROWSER_FILTER_GROUP);

            return $parameters;
        }
    }

    function get_parameters_are_set()
    {
        $filter_scale = Request :: get(self :: BROWSER_FILTER_SCALE);
        $filter_code = Request :: get(self :: BROWSER_FILTER_CODE);
        $filter_group = Request :: get(self :: BROWSER_FILTER_GROUP);

        return (isset($filter_scale) && isset($filter_code) && isset($filter_group));
    }

    /**
     * Display the form
     */
    function display()
    {
        $html = array();
        $html[] = '<div style="text-align: right; clear: both;">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }

    function get_groups()
    {
        $group_menu = new GroupMenu(null, null, false, true);
        $renderer = new OptionsMenuRenderer();
        $group_menu->render($renderer, 'sitemap');

        $options = $renderer->toArray();

        if (count($options) === 0)
        {
            $options[0] = '--&nbsp;' . Translation :: get('NoGroupsAvailable') . '&nbsp;--';
        }

        return $options;
    }

    function setDefaults($defaults = array ())
    {
        $filter_scale = Request :: get(self :: BROWSER_FILTER_SCALE);
        $filter_code = Request :: get(self :: BROWSER_FILTER_CODE);
        $filter_group = Request :: get(self :: BROWSER_FILTER_GROUP);

        $filter_scale_set = isset($filter_scale);
        $filter_code_set = isset($filter_code);
        $filter_group_set = isset($filter_group);

        if ($filter_scale_set && $filter_code_set && $filter_group_set)
        {
            $defaults[self :: BROWSER_FILTER_SCALE] = Request :: get(self :: BROWSER_FILTER_SCALE);
            $defaults[self :: BROWSER_FILTER_CODE] = Request :: get(self :: BROWSER_FILTER_CODE);
            $defaults[self :: BROWSER_FILTER_GROUP] = Request :: get(self :: BROWSER_FILTER_GROUP);
        }

        parent :: setDefaults($defaults);
    }
}
?>