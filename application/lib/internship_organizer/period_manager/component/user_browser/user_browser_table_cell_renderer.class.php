<?php

require_once dirname(__FILE__) . '/../../../tables/user_table/default_user_table_cell_renderer.class.php';
require_once Path :: get_user_path() . '/lib/user_table/default_user_table_cell_renderer.class.php';

class InternshipOrganizerPeriodUserBrowserTableCellRenderer extends DefaultInternshipOrganizerUserTableCellRenderer
{
    
    private $browser;
    private $user_type;

    function InternshipOrganizerPeriodUserBrowserTableCellRenderer($browser, $user_type)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->user_type = $user_type;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === InternshipOrganizerPeriodUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        return parent :: render_cell($column, $user);
    }
	
 function render_id_cell($user)
    {
        $period = $this->browser->get_period();
    	return $period->get_id().'|'.$user->get_id();
    }
    
    private function get_modification_links($user)
    {
        $toolbar = new Toolbar();
        if ($this->user_type == InternshipOrganizerUserType :: STUDENT)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('CreateInternshipOrganizerAgreement'), Theme :: get_common_image_path() . 'action_add.png', $this->browser->get_period_create_agreement_url($this->browser->get_period(),$user), ToolbarItem :: DISPLAY_ICON, true));
        
        }
        return $toolbar->as_html();
    
    }

}
?>