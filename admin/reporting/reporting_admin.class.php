<?php
/**
 * @author Michael Kyndt
 * @package admin.reporting
 * $Id: reporting_admin.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 */
class ReportingAdmin
{

    function ReportingAdmin()
    {
    }

    public static function getNoOfApplications()
    {
        $user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
        $adm = new AdminManager($user);
        $arr[Translation :: get('NumberOfApplications')][0] = 0;
        foreach ($adm->get_application_platform_admin_links() as $application_links)
        {
            $arr[Translation :: get('NumberOfApplications')][0] ++;
        }
        
        return Reporting :: getSerieArray($arr);
    }

    public static function getMostUsedApplications()
    {
    
    }
}
?>