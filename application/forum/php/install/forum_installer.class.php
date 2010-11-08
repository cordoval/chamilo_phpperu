<?php

namespace application\forum;

use common\libraries\WebApplication;
use common\libraries\Installer;
use common\libraries\Translation;
/**
 * $Id: forum_installer.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.install
 */

/**
 * This installer can be used to create the storage structure for the
 * forum application.
 * @author Sven Vanpoucke & Michael Kyndt
 */
class ForumInstaller extends Installer
{

    /**
     * Constructor
     */
    function ForumInstaller($values)
    {
        parent :: __construct($values, ForumDataManager :: get_instance());
    }

	function install_extra()
    {
    	if (!ForumRights :: create_forums_subtree_root_location())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ObjectCreated', array('OBJECT'=> Translation :: get('ForumsSubtree')),Utilities :: COMMON_LIBRARIES);
        }
        
        return true;
    }
    
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>