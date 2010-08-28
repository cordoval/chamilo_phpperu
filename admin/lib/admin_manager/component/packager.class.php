<?php
/**
 * @package admin.lib.admin_manager.component
 * @author Hans De Bisschop
 */
class AdminManagerPackagerComponent extends AdminManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! AdminRights :: is_allowed(AdminRights :: RIGHT_VIEW))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        PackageManager :: launch($this);
    }
}
?>