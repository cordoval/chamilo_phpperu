<?php
namespace application\phrases;

use common\libraries\Translation;
use common\libraries\WebApplication;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;
use common\libraries\Utilities;
/**
 * $Id: phrases_publication_browser_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component.phrases_publication_browser
 */
require_once WebApplication :: get_application_class_lib_path('phrases') . 'phrases_manager/component/publication_manager/component/phrases_publication_browser/phrases_publication_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('phrases') . 'tables/phrases_publication_table/default_phrases_publication_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Hans De Bisschop
 * @author
 */

class PhrasesPublicationBrowserTableCellRenderer extends DefaultPhrasesPublicationTableCellRenderer
{
    /**
     * The browser component
     * @var PhrasesManagerPhrasesPublicationsBrowserComponent
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function PhrasesPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $phrases_publication)
    {
        if ($column === PhrasesPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($phrases_publication);
        }

        return parent :: render_cell($column, $phrases_publication);
    }

    /**
     * Gets the action links to display
     * @param PhrasesPublication $phrases_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($phrases_publication)
    {
        $phrases = $phrases_publication->get_publication_object();

        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_phrases_publication_url($phrases_publication), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_phrases_publication_url($phrases_publication), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Build', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_build.png', $this->browser->get_build_phrases_publication_url($phrases_publication), ToolbarItem :: DISPLAY_ICON));

        return $toolbar->as_html();
    }
}
?>