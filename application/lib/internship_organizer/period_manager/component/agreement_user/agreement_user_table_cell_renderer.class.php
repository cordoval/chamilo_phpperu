<?php

require_once dirname(__FILE__) . '/../../../tables/user_table/default_user_table_cell_renderer.class.php';
require_once Path :: get_user_path() . '/lib/user_table/default_user_table_cell_renderer.class.php';

class InternshipOrganizerPeriodAgreementUserBrowserTableCellRenderer extends DefaultInternshipOrganizerUserTableCellRenderer
{
    
    private $browser;
    private $user_type;

    function InternshipOrganizerPeriodAgreementUserBrowserTableCellRenderer($browser, $user_type)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->user_type = $user_type;
    }

    // Inherited
    function render_cell($column, $user)
    {
           
        if ($column === InternshipOrganizerPeriodAgreementUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        return parent :: render_cell($column, $user);
    }
    
    function render_id_cell($user)
    {
        $agreement = $this->browser->get_agreement();
        return $agreement->get_id() . '|' . $user->get_id().'|'.$this->user_type;
    }

    private function get_modification_links($user)
    {
        $toolbar = new Toolbar();
        if ($this->user_type != InternshipOrganizerUserType :: STUDENT)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_unsubscribe_agreement_rel_user_url($this->browser->get_agreement(), $user, $this->user_type), ToolbarItem :: DISPLAY_ICON, true));
        
        }
        return $toolbar->as_html();
    
    }

}
?>