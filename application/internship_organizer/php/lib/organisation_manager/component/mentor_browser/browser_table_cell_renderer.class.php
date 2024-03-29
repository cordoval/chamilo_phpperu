<?php
namespace application\internship_organizer;

use common\libraries\Toolbar;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\ToolbarItem;

require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/mentor_table/default_mentor_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../mentor.class.php';
//require_once dirname(__FILE__) . '/../../mentor_manager.class.php';


class InternshipOrganizerMentorBrowserTableCellRenderer extends DefaultInternshipOrganizerMentorTableCellRenderer
{

    private $browser;

    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $mentor)
    {
        if ($column === InternshipOrganizerMentorBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($mentor);
        }

        return parent :: render_cell($column, $mentor);
    }

    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($mentor_rel_location)
    {

        $toolbar = new Toolbar();

        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_mentor_url($mentor_rel_location), ToolbarItem :: DISPLAY_ICON));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_DELETE, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_mentor_url($mentor_rel_location), ToolbarItem :: DISPLAY_ICON, true));
        }
        if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_view_mentor_url($mentor_rel_location), ToolbarItem :: DISPLAY_ICON));
        }
        return $toolbar->as_html();
    }
}
?>