<?php
/**
 * $Id: phrases_mastery_level_browser_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component.phrases_mastery_level_browser
 */
require_once dirname(__FILE__) . '/phrases_mastery_level_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../../../tables/phrases_mastery_level_table/default_phrases_mastery_level_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../../phrases_mastery_level.class.php';
require_once dirname(__FILE__) . '/../../../../phrases_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Hans De Bisschop
 * @author
 */

class PhrasesMasteryLevelBrowserTableCellRenderer extends DefaultPhrasesMasteryLevelTableCellRenderer
{
    /**
     * The browser component
     * @var PhrasesManagerPhrasesMasteryLevelsBrowserComponent
     */
    private $browser;

    private $object_count;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function PhrasesMasteryLevelBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    function set_object_count($count)
    {
        $this->object_count = $count;
    }

    // Inherited
    function render_cell($column, $phrases_mastery_level)
    {
        if ($column === PhrasesMasteryLevelBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($phrases_mastery_level);
        }

        return parent :: render_cell($column, $phrases_mastery_level);
    }

    /**
     * Gets the action links to display
     * @param PhrasesMasteryLevel $phrases_mastery_level The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($phrases_mastery_level)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_phrases_mastery_level_url($phrases_mastery_level), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_phrases_mastery_level_url($phrases_mastery_level), ToolbarItem :: DISPLAY_ICON));

        if ($phrases_mastery_level->get_display_order() > 1)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUp'), Theme :: get_common_image_path() . 'action_up.png', $this->browser->get_url(array(PhrasesMasteryLevelManager :: PARAM_MASTERY_LEVEL_MANAGER_ACTION => PhrasesMasteryLevelManager :: ACTION_MOVE_UP, PhrasesMasteryLevelManager :: PARAM_PHRASES_MASTERY_LEVEL_ID => $phrases_mastery_level->get_id())), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUpNA'), Theme :: get_common_image_path() . 'action_up_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }

        if ($phrases_mastery_level->get_display_order() < $this->object_count)
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDown'), Theme :: get_common_image_path() . 'action_down.png', $this->browser->get_url(array(PhrasesMasteryLevelManager :: PARAM_MASTERY_LEVEL_MANAGER_ACTION => PhrasesMasteryLevelManager :: ACTION_MOVE_DOWN, PhrasesMasteryLevelManager :: PARAM_PHRASES_MASTERY_LEVEL_ID => $phrases_mastery_level->get_id())), ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDownNA'), Theme :: get_common_image_path() . 'action_down_na.png', null, ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
}
?>