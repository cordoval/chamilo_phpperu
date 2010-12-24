<?php

namespace application\handbook;
use common\libraries\Toolbar;
use user\User;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\ToolbarItem;
require_once   dirname(__FILE__) .  '/handbook_alternatives_picker_table_column_model.class.php';
require_once   dirname(__FILE__) . '/../../../tables/handbook_alternatives_table/default_handbook_alternatives_table_cell_renderer.class.php';


/**
 * Cell renderer for the handbook_publication object browser table
 */
class HandbookAlternativesPickerItemTableCellRenderer extends DefaultHandbookAlternativesTableCellRenderer

{
    /**
     * The handbook_publication browser component
     */
    public $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $handbook)
    {
        if ($column === HandbookAlternativesPickerItemTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($handbook);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case User :: PROPERTY_OFFICIAL_CODE :
                return $handbook->get_id();
        }
        return parent :: render_cell($column, $handbook);
    }

    /**
     * Gets the action links to display
     * @param $handbook The handbook for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($handbook)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('View' , array('OBJECT' => Translation::get('HandbookPublication')), Utilities::COMMON_LIBRARIES),
        		Theme :: get_common_image_path() . 'action_browser.png',
        		$this->browser->get_view_handbook_publication_url($handbook->get_id()),
        		ToolbarItem :: DISPLAY_ICON
        ));

        return $toolbar->as_html();
    }
}
?>