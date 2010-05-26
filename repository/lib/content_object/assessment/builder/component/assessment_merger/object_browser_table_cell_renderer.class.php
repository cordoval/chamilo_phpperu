<?php
/**
 * $Id: object_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component.assessment_merger
 */
require_once dirname(__FILE__) . '/object_browser_table_column_model.class.php';
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ObjectBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ObjectManagerBrowserComponent $browser
     */
    function ObjectBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === ObjectBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($content_object);
        }
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $content_object->get_modification_date());
        }
        
        return parent :: render_cell($column, $content_object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($content_object)
    {
        $toolbar_data[] = array('href' => $this->browser->get_question_selector_url($content_object->get_id()), 'label' => Translation :: get('SelectQuestion'), 'img' => Theme :: get_common_image_path() . 'action_right.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>