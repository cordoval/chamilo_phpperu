<?php

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/tables/survey_context_registration_table/default_survey_context_registration_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SurveyContextRegistrationBrowserTableCellRenderer extends DefaultSurveyContextRegistrationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function SurveyContextRegistrationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $context_registration)
    {
        if ($column === SurveyContextRegistrationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($context_registration);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case SurveyContextRegistration :: PROPERTY_NAME :
                $title = parent :: render_cell($column, $context_registration);
                $title_short = $title;
                if (strlen($title_short) > 53)
                {
                    $title_short = mb_substr($title_short, 0, 50) . '&hellip;';
                }
                
                return '<a href="' . htmlentities($this->browser->get_context_registration_viewing_url($context_registration)) . '" title="' . $title . '">' . $title_short . '</a>';
            case SurveyContextRegistration :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $context_registration));
                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }
                return Utilities :: truncate_string($description);
//            case Translation :: get('SurveyPages') :
//                $survey_id = $this->browser->get_root_content_object()->get_id();
//                $context_registration_id = $context_registration->get_id();
//                $conditions = array();
//                $conditions[] = new EqualityCondition(SurveyContextRegistrationRelPage :: PROPERTY_SURVEY_ID, $survey_id);
//                $conditions[] = new EqualityCondition(SurveyContextRegistrationRelPage :: PROPERTY_TEMPLATE_ID, $context_registration_id);
//                $condition = new AndCondition($conditions);
//                return SurveyContextRegistrationDataManager :: get_instance()->count_template_rel_pages($condition);
//            case Translation :: get('SubContextRegistrations') :
//                return $context_registration->count_children(false);
        }
        
        return parent :: render_cell($column, $context_registration);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($context_registration)
    {
        
    }
}
?>