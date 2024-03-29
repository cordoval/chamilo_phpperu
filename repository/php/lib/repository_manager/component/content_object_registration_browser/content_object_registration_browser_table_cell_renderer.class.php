<?php
namespace repository;
use common\libraries\Path;
use admin\RegistrationBrowserTableCellRenderer;
use admin\RegistrationBrowserTableColumnModel;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use rights\RightsManager;
/**
 * $Id: registration_browser_table_cell_renderer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_manager.component.registration_browser
 */
/**
 * Cell rendere for the learning object browser table
 */
class ContentObjectRegistrationBrowserTableCellRenderer extends RegistrationBrowserTableCellRenderer
{

	function render_cell($column, $registration)
    {
    	if ($column === RegistrationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($registration);
        }
        
        return parent :: render_cell($column, $registration);
    }
    
    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($registration)
    {
        $toolbar = new Toolbar();

		$toolbar->add_item(new ToolbarItem(Translation :: get('ManageRights', null, RightsManager :: APPLICATION_NAME), Theme :: get_common_image_path().'action_rights.png',
					$this->get_browser()->get_content_object_type_rights_editing_url($registration), ToolbarItem :: DISPLAY_ICON));

        
        return $toolbar->as_html();
    }
}
?>