<?php
/**
 * cda.install
 */

require_once dirname(__FILE__).'/../cda_data_manager.class.php';

/**
 * This installer can be used to create the storage structure for the
 * cda application.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function CdaInstaller($values)
    {
    	parent :: __construct($values, CdaDataManager :: get_instance());
    }

	function get_path()
	{
		return dirname(__FILE__);
	}
	
	function install_extra()
    {
        if (! $this->create_languages_subtree())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('LanguagesTreeCreated'));
        }
        
        return true;
    }
    
    private function create_languages_subtree()
    {
    	return RightsUtilities :: create_subtree_root_location(CdaManager :: APPLICATION_NAME, 0, 'languages_tree');
    }
}
?>