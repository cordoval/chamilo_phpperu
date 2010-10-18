<?php

require_once dirname(__FILE__) . '/user_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/user_table/default_user_table_cell_renderer.class.php';

class SurveyUserBrowserTableCellRenderer extends DefaultSurveyUserTableCellRenderer
{
    
    private $browser;
    private $publication_id;
    private $type;

    function SurveyUserBrowserTableCellRenderer($browser, $publication_id, $type)
    {
        parent :: __construct($publication_id);
        $this->browser = $browser;
        $this->publication_id = $publication_id;
        $this->type = $type;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === SurveyUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        
        return parent :: render_cell($column, $user);
    }

    private function get_modification_links($user)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if ($this->type == SurveyUserBrowserTable :: TYPE_INVITEES)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('TakeSurvey'), Theme :: get_common_image_path() . 'action_next.png', $this->browser->get_survey_invitee_publication_viewer_url($this->publication_id, $user->get_id()), ToolbarItem :: DISPLAY_ICON));
        }
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('CancelInvitations'), Theme :: get_common_image_path() . 'action_unsubscribe.png', $this->browser->get_survey_cancel_invitation_url($this->publication_id, $user->get_id()), ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
?>