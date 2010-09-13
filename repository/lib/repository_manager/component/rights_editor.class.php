<?php
/**
 * $Id: rights_editor.class.php 239 2009-11-16 14:25:41Z vanpouckesven $
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class RepositoryManagerRightsEditorComponent extends RepositoryManager
{
	private $tree;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $type = Request :: get(self :: PARAM_TYPE);
        $identifiers = Request :: get(self :: PARAM_IDENTIFIER);

        $locations = array();

        switch($type)
        {
        	case RepositoryRights :: TYPE_CONTENT_OBJECT:
        		$tree = RepositoryRights :: TREE_TYPE_CONTENT_OBJECT;
        		$tree_identifier = 0;
        		if(!$identifiers)
        		{
        			$locations[] = RepositoryRights :: get_content_objects_subtree_root();
        		}
        		break;
        	case RepositoryRights :: TYPE_EXTERNAL_REPOSITORY:
        		$tree = RepositoryRights :: TREE_TYPE_EXTERNAL_REPOSITORY;
        		$tree_identifier = 0;
        		if(!$identifiers)
        		{
        			$locations[] = RepositoryRights :: get_external_repositories_subtree_root();
        		}
        		break;
        	default:
        		$tree = RepositoryRights :: TREE_TYPE_USER;
        		$tree_identifier = $this->get_user_id();
       		    if(!$identifiers)
        		{
        			$locations[] = RepositoryRights :: get_user_root($this->get_user_id());
        		}
        		break;
        }

        $this->tree = $tree;

        if ($identifiers && ! is_array($identifiers))
        {
            $identifiers = array($identifiers);
        }

        foreach ($identifiers as $identifier)
        {
        	$locations[] = RepositoryRights :: get_location_by_identifier($type, $identifier, $tree_identifier, $tree);
        }

        if($type == RepositoryRights::TYPE_USER_CONTENT_OBJECT)
            $manager = RightsEditorManager :: factory($this->retrieve_content_object($identifier),$this, $locations);
        else
            $manager = RightsEditorManager :: factory(null, $this, $locations);
        //$manager = new RightsEditorManager($this, $locations);
        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
    }

    function get_available_rights()
    {
    	switch($this->tree)
        {
        	case RepositoryRights :: TREE_TYPE_CONTENT_OBJECT:
        		return RepositoryRights :: get_available_rights_for_content_object_subtree();
        	case RepositoryRights :: TREE_TYPE_EXTERNAL_REPOSITORY:
        		return RepositoryRights :: get_available_rights_for_external_repositories_substree();
        	default:
        		return RepositoryRights :: get_available_rights_for_users_subtree();
        }
    }

    function display_header($trail)
    {
        parent :: display_header($trail, false);

        $type = Request :: get(self :: PARAM_TYPE);

        if($type == RepositoryRights :: TYPE_USER_CONTENT_OBJECT)
        {
	        $object_ids = Request :: get(self :: PARAM_IDENTIFIER);

	        if (! is_array($object_ids))
	        {
	            $object_ids = array($object_ids);
	        }

	        $html = array();
	        $html[] = '<div class="content_object padding_10">';
	        $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects') . '</div>';
	        $html[] = '<div class="description">';
	        $html[] = '<ul class="attachments_list">';

	        foreach ($object_ids as $object_id)
	        {
	            $object = $this->retrieve_content_object($object_id);
	            $html[] = '<li><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $object->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($object->get_type()) . 'TypeName')) . '"/> ' . $object->get_title() . '</li>';
	        }

	        $html[] = '</ul>';
	        $html[] = '</div>';
	        $html[] = '</div>';

	        echo implode("\n", $html);
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_rights_editor');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_IDENTIFIER, self :: PARAM_TYPE);
    }

}
?>