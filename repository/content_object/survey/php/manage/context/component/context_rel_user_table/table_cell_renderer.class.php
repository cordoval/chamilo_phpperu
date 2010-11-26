<?php 
namespace repository\content_object\survey;

use common\libraries\Path;

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once Path :: get_repository_content_object_path() . 'survey/php/manage/context/tables/context_rel_user_table/default_context_rel_user_table_cell_renderer.class.php';

class SurveyContextRelUserBrowserTableCellRenderer extends DefaultSurveyContextRelUserTableCellRenderer
{
    
    private $browser;

    function __construct($browser)
    {
        parent :: __construct($browser);
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $context_rel_user)
    {
        if ($column === SurveyContextRelUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($context_rel_user);
        }
        
        return parent :: render_cell($column, $context_rel_user);
    }

    private function get_modification_links($context_rel_user)
    {
        $toolbar = new Toolbar();
        
//        if (SurveyContextManagerRights :: is_allowed_in_survey_context_manager_subtree(SurveyContextManagerRights :: SUBSCRIBE_USER_RIGHT, $context_rel_user->get_context_id(), SurveyContextManagerRights :: TYPE_CONTEXT_REGISTRATION))
//        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_context_unsubscribe_user_url($context_rel_user), ToolbarItem :: DISPLAY_ICON, true));
//        }
        return $toolbar->as_html();
    }
}
?>