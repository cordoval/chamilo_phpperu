<?php
/**
 * $Id: reporting_rights.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.reporting
 */
class ReportingRights
{

    function ReportingRights()
    {
    }

    public static function getUsersPerRightsTemplate($params)
    {
        $rdm = RightsDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();
        
        $list = $rdm->retrieve_rights_templates();
        
        while ($rights_template = $list->next_result())
        {
            $arr[$rights_template->get_id()][0] = 0;
        }
        
        $list = $udm->retrieve_user_rights_templates();
        
        while ($bla = $list->next_result())
        {
            $arr[$bla->get_rights_template_id()][0] ++;
        }
        
        foreach ($arr as $key => $value)
        {
            $arr[Translation :: get($rdm->retrieve_rights_template($key)->get_name())] = $arr[$key];
            unset($arr[$key]);
        }
        
        return Reporting :: getSerieArray($arr);
    } //getUsersPerRightsTemplate

    
    public static function getGroupsPerRightsTemplate($params)
    {
        $rdm = RightsDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();
        
        $list = $rdm->retrieve_rights_templates();
        
        while ($rights_template = $list->next_result())
        {
            $arr[$rights_template->get_id()][0] = 0;
        }
        
        $list = $gdm->retrieve_group_rights_templates();
        
        while ($group = $list->next_result())
        {
            $arr[$group->get_rights_template_id()][0] ++;
        }
        
        $group = $gdm->retrieve_group(0);
        
        foreach ($arr as $key => $value)
        {
            $arr[Translation :: get($rdm->retrieve_rights_template($key)->get_name())] = $arr[$key];
            unset($arr[$key]);
        }
        
        return Reporting :: getSerieArray($arr);
    } //getgroupsperrights_template

    
    public static function getNoOfRightsTemplates($params)
    {
        $rdm = RightsDataManager :: get_instance();
        
        $list = $rdm->retrieve_rights_templates();
        
        while ($rights_template = $list->next_result())
        {
            $arr[Translation :: get('RightsTemplates')][0] ++;
        }
        
        return Reporting :: getSerieArray($arr);
    } //getnoofrights_templates

    
    public static function getRightsTemplates($params)
    {
        $rdm = RightsDataManager :: get_instance();
        
        $list = $rdm->retrieve_rights_templates();
        
        while ($rights_template = $list->next_result())
        {
            $arr[Translation :: get('RightsTemplates')][] = $rights_template->get_name();
        }
        
        return Reporting :: getSerieArray($arr);
    } //getrights_templates
}
?>