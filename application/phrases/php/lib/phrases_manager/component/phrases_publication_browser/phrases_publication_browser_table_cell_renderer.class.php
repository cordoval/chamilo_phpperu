<?php
namespace application\phrases;

use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Request;
use common\libraries\ComplexContentObjectSupport;
use common\libraries\Utilities;
/**
 * $Id: phrases_publication_browser_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component.phrases_publication_browser
 */
require_once dirname(__FILE__) . '/phrases_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/phrases_publication_table/default_phrases_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../phrases_publication.class.php';
require_once dirname(__FILE__) . '/../../phrases_manager.class.php';

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
    function __construct($browser)
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

        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('TakePhrases'), Theme :: get_common_image_path() . 'action_next.png', $this->browser->get_publication_viewer_url($phrases_publication), ToolbarItem :: DISPLAY_ICON));

        if (PhrasesRights :: is_allowed_in_phrasess_subtree(PhrasesRights :: VIEW_RESULTS_RIGHT, $phrases_publication->get_id(), PhrasesRights :: TYPE_PUBLICATION))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('ViewResults'), Theme :: get_common_image_path() . 'action_view_results.png', $this->browser->get_phrases_results_viewer_url($phrases_publication), ToolbarItem :: DISPLAY_ICON));
        }

        $user = $this->browser->get_user();

        if ($user->is_platform_admin() || $user->get_id() == $phrases_publication->get_publisher())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_phrases_publication_url($phrases_publication), ToolbarItem :: DISPLAY_ICON, true));
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_phrases_publication_url($phrases_publication), ToolbarItem :: DISPLAY_ICON));

            if ($phrases_publication->get_hidden())
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Show', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_visible_na.png', $this->browser->get_change_phrases_publication_visibility_url($phrases_publication), ToolbarItem :: DISPLAY_ICON));

            }
            else
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Hide', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_visible.png', $this->browser->get_change_phrases_publication_visibility_url($phrases_publication), ToolbarItem :: DISPLAY_ICON));
            }

            $toolbar->add_item(new ToolbarItem(Translation :: get('Move', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_move.png', $this->browser->get_move_phrases_publication_url($phrases_publication), ToolbarItem :: DISPLAY_ICON));

            if ($phrases instanceof ComplexContentObjectSupport)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('BuildComplexObject', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_build.png', $this->browser->get_build_phrases_url($phrases_publication), ToolbarItem :: DISPLAY_ICON));
            }

            $toolbar->add_item(new ToolbarItem(Translation :: get('ManageRights', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_rights.png', $this->browser->get_rights_editor_url(Request :: get('category'), $phrases_publication->get_id()), ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
}
?>