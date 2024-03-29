<?php
namespace application\internship_organizer;

use common\libraries\Toolbar;
use common\libraries\WebApplication;
use common\libraries\CoreApplication;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\ToolbarItem;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'tables/user_table/default_user_table_cell_renderer.class.php';
require_once CoreApplication :: get_application_class_lib_path('user') . 'user_table/default_user_table_cell_renderer.class.php';
class InternshipOrganizerPeriodAgreementUserBrowserTableCellRenderer extends DefaultInternshipOrganizerUserTableCellRenderer
{

    private $browser;
    private $user_type;

    function __construct($browser, $user_type)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->user_type = $user_type;
    }

    // Inherited
    function render_cell($column, $user)
    {

        if ($column === InternshipOrganizerPeriodAgreementUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        return parent :: render_cell($column, $user);
    }

    function render_id_cell($user)
    {
        $agreement = $this->browser->get_agreement();
        return $agreement->get_id() . '|' . $user->get_id() . '|' . $this->user_type;
    }

    private function get_modification_links($user)
    {
        $toolbar = new Toolbar();
        if ($this->user_type != InternshipOrganizerUserType :: STUDENT)
        {
            if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_AGREEMENT_USER_RIGHT, $this->browser->get_agreement()->get_period_id(), InternshipOrganizerRights :: TYPE_PERIOD))
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Unsubscribe'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_unsubscribe_agreement_rel_user_url($this->browser->get_agreement(), $user, $this->user_type), ToolbarItem :: DISPLAY_ICON));
            }
        }
        return $toolbar->as_html();

    }

}
?>