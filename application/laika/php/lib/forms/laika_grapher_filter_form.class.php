<?php
/**
 * $Id: laika_grapher_filter_form.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.forms
 */
require_once dirname(__FILE__) . '/../laika_manager/laika_manager.class.php';
require_once dirname(__FILE__) . '/../laika_data_manager.class.php';
require_once Path :: get_application_path() . 'laika/php/lib/laika_graph_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/document/document.class.php';

class LaikaGrapherFilterForm extends FormValidator
{
    const GRAPH_FILTER_START_DATE = 'filter_start_date';
    const GRAPH_FILTER_END_DATE = 'filter_end_date';
    const GRAPH_FILTER_SCALE = 'filter_scale';
    const GRAPH_FILTER_CODE = 'filter_code';
    const GRAPH_FILTER_GROUP = 'filter_group';

    const GRAPH_FILTER_TYPE = 'filter_type';
    const GRAPH_FILTER_SAVE = 'filter_save';
    const GRAPH_FILTER_ATTEMPT = 'filter_attempt';

    private $manager;
    private $renderer;

    private $selected_scales;
    private $selected_groups;
    private $selected_codes;

    /**
     * Creates a new search form
     * @param RepositoryManager $manager The repository manager in which this
     * search form will be displayed
     * @param string $url The location to which the search request should be
     * posted.
     */
    function __construct($manager, $url)
    {
        parent :: __construct('laika_analyzer_filter_form', 'post', $url);

        $this->manager = $manager;

        $this->build_form();

        $this->setDefaults();
    }

    /**
     * Build the simple search form.
     */
    private function build_form()
    {
        $ldm = LaikaDataManager :: get_instance();

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

        $this->addElement('category', Translation :: get('Dates'));
        $this->add_timewindow(self :: GRAPH_FILTER_START_DATE, self :: GRAPH_FILTER_END_DATE, Translation :: get('StartTimeWindow'), Translation :: get('EndTimeWindow'), false);
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Groups'));

        $group_options = $this->get_groups();

        if (count($group_options) > 0)
        {
            if (count($group_options) < 10)
            {
                $count = count($group_options);
            }
            else
            {
                $count = 10;
            }

            $this->addElement('select', self :: GRAPH_FILTER_GROUP, Translation :: get('Group'), $this->get_groups(), array('multiple', 'size' => $count));
            $this->addRule(self :: GRAPH_FILTER_GROUP, Translation :: get('ThisFieldIsRequired'), 'required');
        }
        else
        {
            $this->addElement('static', 'group_text', Translation :: get('Group'), Translation :: get('NoGroupsAvailable'));
            $this->addElement('hidden', self :: GRAPH_FILTER_GROUP, null);
        }

        $this->addElement('category');

        $this->addElement('category', Translation :: get('Results'));
        $this->addElement('select', self :: GRAPH_FILTER_SCALE, Translation :: get('Scale'), $scale_options, array('multiple', 'size' => '10'));
        $this->addRule(self :: GRAPH_FILTER_SCALE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('select', self :: GRAPH_FILTER_CODE, Translation :: get('Code'), $code_options, array('multiple', 'size' => '4'));
        $this->addRule(self :: GRAPH_FILTER_CODE, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addElement('category');

        $this->addElement('category', Translation :: get('Options'));

        $group = array();
        $group[] = $this->createElement('radio', self :: GRAPH_FILTER_TYPE, null, Translation :: get('RenderGraphAndTable'), LaikaGraphRenderer :: RENDER_GRAPH_AND_TABLE);
        $group[] = $this->createElement('radio', self :: GRAPH_FILTER_TYPE, null, Translation :: get('RenderGraph'), LaikaGraphRenderer :: RENDER_GRAPH);
        $group[] = $this->createElement('radio', self :: GRAPH_FILTER_TYPE, null, Translation :: get('RenderTable'), LaikaGraphRenderer :: RENDER_TABLE);
        $this->addGroup($group, self :: GRAPH_FILTER_TYPE, Translation :: get('RenderType'), '<br/>', false);

        $allow_save = PlatformSetting :: get('allow_save', LaikaManager :: APPLICATION_NAME);
        if ($allow_save == true)
        {
            $this->addElement('checkbox', self :: GRAPH_FILTER_SAVE, Translation :: get('SaveToRepository'));
        }

        $maximum_attempts = PlatformSetting :: get('maximum_attempts', LaikaManager :: APPLICATION_NAME);
        if ($maximum_attempts > 1)
        {
            $group = array();
            $group[] = $this->createElement('radio', self :: GRAPH_FILTER_ATTEMPT, null, Translation :: get('OnlyIncludeFirstAttempt'), LaikaGraphRenderer :: RENDER_ATTEMPT_FIRST);
            $group[] = $this->createElement('radio', self :: GRAPH_FILTER_ATTEMPT, null, Translation :: get('OnlyIncludeMostRecentAttempt'), LaikaGraphRenderer :: RENDER_ATTEMPT_LAST);
            $group[] = $this->createElement('radio', self :: GRAPH_FILTER_ATTEMPT, null, Translation :: get('IncludeAllAttempts'), LaikaGraphRenderer :: RENDER_ATTEMPT_ALL);
            $this->addGroup($group, self :: GRAPH_FILTER_ATTEMPT, Translation :: get('AttemptsToInclude'), '<br/>', false);
        }
        else
        {
            $this->addElement('hidden', self :: GRAPH_FILTER_ATTEMPT, LaikaGraphRenderer :: RENDER_ATTEMPT_ALL);
        }

        $this->addElement('category');

        $buttons = array();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'normal search'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Display the form
     */
    function display()
    {
        $html = array();
        $html[] = '<div style="clear: both; margin-bottom: 10px;">';
        $html[] = parent :: display();
        $html[] = $this->getValidationScript();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        return implode('', $html);
    }

    function get_groups()
    {
        $group_menu = new GroupMenu(null, null, false, true);
        $renderer = new OptionsMenuRenderer();
        $group_menu->render($renderer, 'sitemap');

        return $renderer->toArray();
    }

    function setDefaults($defaults = array ())
    {
        $defaults[self :: GRAPH_FILTER_START_DATE] = strtotime("-1 year");
        $defaults[self :: GRAPH_FILTER_END_DATE] = time();
        $defaults[self :: GRAPH_FILTER_TYPE] = LaikaGraphRenderer :: RENDER_GRAPH_AND_TABLE;
        $defaults[self :: GRAPH_FILTER_SAVE] = 0;
        $defaults[self :: GRAPH_FILTER_ATTEMPT] = LaikaGraphRenderer :: RENDER_ATTEMPT_FIRST;

        parent :: setDefaults($defaults);
    }

    function render_graphs()
    {
        $values = $this->exportValues();

        $renderer = new LaikaGraphRenderer($values[self :: GRAPH_FILTER_GROUP], $values[self :: GRAPH_FILTER_SCALE], $values[self :: GRAPH_FILTER_CODE]);
        $renderer->set_type($values[self :: GRAPH_FILTER_TYPE]);
        $renderer->set_attempt($values[self :: GRAPH_FILTER_ATTEMPT]);
        if (isset($values[self :: GRAPH_FILTER_SAVE]))
        {
            $renderer->save();
        }
        $html = $renderer->render_graphs();

        return $html;
    }
}
?>