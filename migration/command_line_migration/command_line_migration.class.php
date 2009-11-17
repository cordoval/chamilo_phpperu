<?php
/**
 * $Id: command_line_migration.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.command_line_migration
 */
/**
 * Start commandline migration
 */

require_once dirname(__FILE__) . '/../../common/global.inc.php';
//require_once 'HTML/QuickForm/Controller.php';
//require_once 'HTML/QuickForm/Rule.php';
//require_once 'HTML/QuickForm/Action/Display.php';
class CommandLineMigration
{

    function migrate($migration)
    {
        
        echo ("\n");
        echo ($this->newlines($migration->get_title()) . "\n");
        
        if ($migration->perform())
        {
            
            $info = $this->newlines($migration->get_info());
            $pos = strpos($info, Translation :: get('Dont_forget')) - 2;
            echo (substr($info, 0, $pos));
        }
        echo ("\n");
    }

    function newlines($message)
    {
        $temp = str_replace("<br />", "\n", $message);
        $temp1 = str_replace("<br>", "\n", $temp);
        return str_replace("<br / >", "\n", $temp1);
    }
}
?>