<?php
namespace admin;

use common\libraries\Display;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;

class ApplicationRegistrationDisplay extends RegistrationDisplay
{

    function get_action_bar()
    {
        $registration = $this->get_registration();
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if (! $this->get_registration()->is_up_to_date())
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('UpdatePackage'), Theme :: get_common_image_path() . 'action_update.png', $this->get_component()->get_registration_update_url($this->get_registration()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PackageIsAlreadyUpToDate'), Theme :: get_common_image_path() . 'action_update_na.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        // TODO: Temporarily disabled archive option
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('UpdatePackageFromArchive'), Theme :: get_image_path() . 'action_update_archive.png', $this->get_component()->get_registration_update_archive_url($this->get_registration()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->get_registration()->is_active())
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Deactivate', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_deactivate.png', $this->get_component()->get_registration_deactivation_url($this->get_registration()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Activate', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_activate.png', $this->get_component()->get_registration_activation_url($this->get_registration()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Deinstall', array(), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_deinstall.png', $this->get_component()->get_registration_removal_url($this->get_registration()), ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));

        return $action_bar;
    }
}
?>