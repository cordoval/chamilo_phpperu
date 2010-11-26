<?php
namespace repository;

use common\libraries\Request;
use common\extensions\rights_editor_manager\RightsEditorManager;
/**
 * $Id: rights_editor.class.php 239 2009-11-16 14:25:41Z vanpouckesven $
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class ExternalInstanceManagerRightsEditorComponent extends ExternalInstanceManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $identifiers = Request :: get(self :: PARAM_INSTANCE);
        $this->set_parameter(self :: PARAM_INSTANCE, $identifiers);

        $locations = array();

    	if(!$identifiers)
		{
        	$locations[] = RepositoryRights :: get_videos_conferencing_subtree_root();
		}

        if ($identifiers && ! is_array($identifiers))
        {
            $identifiers = array($identifiers);
        }

        foreach ($identifiers as $identifier)
        {
        	$locations[] = RepositoryRights :: get_location_by_identifier_from_videos_conferencing_subtree($identifier);
        }

        $manager = new RightsEditorManager($this, $locations);
        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
    }

    function get_available_rights()
    {
		return RepositoryRights :: get_available_rights_for_external_repositories_substree();
    }

}
?>