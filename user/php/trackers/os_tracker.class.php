<?php
/**
 * $Id: os_tracker.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.trackers
 */

require_once dirname(__FILE__) . '/user_tracker.class.php';

/**
 * This class tracks the os that a user uses
 */
class OsTracker extends UserTracker
{

    function validate_parameters(array $parameters = array())
    {
        $server = $parameters['server'];
        $user_agent = $server['HTTP_USER_AGENT'];
        $os = $this->extract_os_from_useragent($user_agent);

        $this->set_type(self :: TYPE_OS);
        $this->set_name($os);
    }

    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker($event)
    {
        $condition = new EqualityCondition(self :: PROPERTY_TYPE, self :: TYPE_OS);
        return $this->remove($condition);
    }

    /**
     * Inherited
     */
    function export($start_date, $end_date, $event)
    {
        $condition = new EqualityCondition(self :: PROPERTY_TYPE, self :: TYPE_OS);
        return $this->get_data_manager()->retrieve_tracker_items($this->get_table_name(), $condition);
    }

    /**
     * Extracts a os from the useragent
     * @param User Agent $user_agent
     * @return string The Os
     */
    function extract_os_from_useragent($user_agent)
    {
        // default values, if nothing corresponding found
        $viewable_os = "Unknown";
        $list_os = $this->load_os();

        // search for corresponding pattern in $_SERVER['HTTP_USER_AGENT']
        // for os
        for($i = 0; $i < count($list_os); $i ++)
        {
            $pos = strpos($user_agent, $list_os[$i][0]);
            if ($pos !== false)
            {
                $viewable_os = $list_os[$i][1];
            }
        }

        return $viewable_os;
    }

    /**
     * Function used to list all the available os with their names
     * @return array of os
     */
    function load_os()
    {
        $buffer = split("#", "Windows 95|Win 95#Windows_95|Win 95#Windows 98|Win 98#Windows NT|Win NT#Windows NT 5.0|Win 2000#Windows NT 5.1|Win XP#Windows 2000|Win 2000#Windows XP|Win XP#Windows ME|Win Me#Win95|Win 95#Win98|Win 98#WinNT|Win NT#linux-2.2|Linux 2#Linux|Linux#Linux 2|Linux 2#Macintosh|Mac#Mac_PPC|Mac#Mac_PowerPC|Mac#SunOS 5|SunOS 5#SunOS 6|SunOS 6#FreeBSD|FreeBSD#beOS|beOS#InternetSeer|InternetSeer#Googlebot|Googlebot#Teleport Pro|Teleport Pro");
        $i = 0;
        foreach ($buffer as $buffer1)
        {
            list($list_os[$i][0], $list_os[$i][1]) = split('[|]', $buffer1);
            $i += 1;
        }
        return $list_os;
    }
}
?>