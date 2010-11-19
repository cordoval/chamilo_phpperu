<?php namespace repository\content_object\survey;

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/tables/context_rel_group_table/default_context_rel_group_table_cell_renderer.class.php';

class SurveyContextRelGroupTableCellRenderer extends DefaultSurveyContextRelGroupTableCellRenderer
{
    
    private $browser;

    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $rel_group)
    {
        if ($column === SurveyContextRelGroupTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($rel_group);
        }
        
        return parent :: render_cell($column, $rel_group);
    }

    private function get_modification_links($rel_group)
    {
        $toolbar = new Toolbar();
        
//        if (SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: SUBSCRIBE_USER_RIGHT, $rel_group->get_period_id(), SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION))
//        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_context_unsubscribe_group_url($rel_group), ToolbarItem :: DISPLAY_ICON, true));
//        }
        return $toolbar->as_html();
    }
}
?>