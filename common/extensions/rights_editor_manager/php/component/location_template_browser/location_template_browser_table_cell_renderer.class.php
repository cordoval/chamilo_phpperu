<?php
namespace common\extensions\rights_editor_manager;

use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Utilities;
use rights\DefaultRightsTemplateTableCellRenderer;
use rights\RightsUtilities;
/**
 * $Id: location_template_browser_table_cell_renderer.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 */
require_once dirname(__FILE__) . '/location_template_browser_table_column_model.class.php';
require_once Path :: get_rights_path() . 'lib/tables/rights_template_table/default_rights_template_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LocationTemplateBrowserTableCellRenderer extends DefaultRightsTemplateTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LocationTemplateBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $rights_template)
    {
        if ($column === LocationTemplateBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($rights_template);
        }
        
        if (LocationTemplateBrowserTableColumnModel :: is_rights_column($column))
        {
            return $this->get_rights_column_value($column, $rights_template);
        }
        
        return parent :: render_cell($column, $rights_template);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($rights_template)
    {
        $toolbar = new Toolbar();
        return $toolbar->as_html();
    }

    private function get_rights_column_value($column, $rights_template)
    {
        $browser = $this->browser;
        $locations = $browser->get_locations();
        $locked_parent = $locations[0]->get_locked_parent();
        $rights = $this->browser->get_available_rights();
        $rights_template_id = $rights_template->get_id();
        
        $location_url = $browser->get_url(array('application' => $this->application, 'location' => ($locked_parent ? $locked_parent->get_id() : $locations[0]->get_id())));
        
        foreach ($rights as $right_name => $right_id)
        {
            $column_name = Translation :: get(Utilities :: underscores_to_camelcase(strtolower($right_name)));
            if ($column->get_name() == $column_name)
            {
                $rights_url = $browser->get_url(array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_SET_TEMPLATE_RIGHTS, 'rights_template_id' => $rights_template_id, 'right_id' => $right_id));
                return RightsUtilities :: get_rights_icon($location_url, $rights_url, $locked_parent, $right_id, $rights_template, $locations[0]);
            }
        }
        return '&nbsp;';
    }
}
?>