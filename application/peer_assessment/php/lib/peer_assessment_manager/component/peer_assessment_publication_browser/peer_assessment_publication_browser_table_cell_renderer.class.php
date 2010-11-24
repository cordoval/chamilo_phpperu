<?php

namespace application\peer_assessment;

use common\libraries;

use repository\ContentObject;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Utilities;

require_once dirname(__FILE__) . '/peer_assessment_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/peer_assessment_publication_table/default_peer_assessment_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../peer_assessment_publication.class.php';
require_once dirname(__FILE__) . '/../../peer_assessment_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 * @author Nick Van Loocke
 */
class PeerAssessmentPublicationBrowserTableCellRenderer extends DefaultPeerAssessmentPublicationTableCellRenderer
{

    /**
     * The browser component
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
    function render_cell($column, $peer_assessment_publication)
    {
        if ($property = $column->get_name())
        {
            switch ($property)
            {
                case ContentObject :: PROPERTY_TITLE :
                    $url = $this->browser->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_VIEW_PEER_ASSESSMENT, PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
                    return htmlspecialchars($peer_assessment_publication->get_content_object()->get_title());
                case ContentObject :: PROPERTY_DESCRIPTION :
                    return $peer_assessment_publication->get_content_object()->get_description();
            }
        }
        if ($column === PeerAssessmentPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($peer_assessment_publication);
        }
        return parent :: render_cell($column, $peer_assessment_publication);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($peer_assessment_publication)
    {

        $toolbar = new Toolbar();

        $user = $this->browser->get_user();

        $toolbar->add_item(new ToolbarItem(Translation :: get('TakePeerAssessment'), Theme :: get_common_image_path() . 'action_next.png', $this->browser->get_take_peer_assessment_publication_url($peer_assessment_publication), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('ViewResults'), Theme :: get_common_image_path() . 'action_view_results.png', $this->browser->get_peer_assessment_results_viewer_url($peer_assessment_publication), ToolbarItem :: DISPLAY_ICON));

        if ($user->is_platform_admin() || $user->get_id() == $peer_assessment_publication->get_publisher())
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_peer_assessment_publication_url($peer_assessment_publication), ToolbarItem :: DISPLAY_ICON, true));
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_peer_assessment_publication_url($peer_assessment_publication), ToolbarItem :: DISPLAY_ICON));

            if ($peer_assessment_publication->get_hidden())
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Show', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_visible_na.png', $this->browser->get_change_peer_assessment_publication_visibility_url($peer_assessment_publication), ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Hide', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_visible.png', $this->browser->get_change_peer_assessment_publication_visibility_url($peer_assessment_publication), ToolbarItem :: DISPLAY_ICON));
            }

            $toolbar->add_item(new ToolbarItem(Translation :: get('Move', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_move.png', $this->browser->get_move_peer_assessment_publication_url($peer_assessment_publication), ToolbarItem :: DISPLAY_ICON));
            $toolbar->add_item(new ToolbarItem(Translation :: get('BuildComplexObject', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_build.png', $this->browser->get_build_peer_assessment_url($peer_assessment_publication), ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }

}

?>