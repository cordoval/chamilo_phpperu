<?php
/**
 * $Id: forum_installer.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.install
 */

require_once dirname(__FILE__) . '/../forum_data_manager.class.php';
require_once dirname(__FILE__) . '/../forum_rights.class.php';

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
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('ForumsSubtreeCreated'));
        }
        
        return true;
    }
    
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>