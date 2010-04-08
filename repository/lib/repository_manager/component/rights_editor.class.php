<?php
/**
 * $Id: rights_editor.class.php 239 2009-11-16 14:25:41Z vanpouckesven $
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class RepositoryManagerRightsEditorComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $object_ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);

    	if(!is_array($object_ids))
        {
        	$object_ids = array($object_ids);
        }

        $locations = array();

        foreach($object_ids as $object_id)
        {
        	$locations[] = RepositoryRights :: get_location_by_identifier('content_object', $object_id, $this->get_user_id(), 'user_tree');
        }

        $manager = new RightsEditorManager($this, $locations);

        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
    }

    function get_available_rights()
    {
        $array = RepositoryRights :: get_available_rights();
        unset($array['ADD_RIGHT']);
        unset($array['EDIT_RIGHT']);
        unset($array['DELETE_RIGHT']);

        return $array;
    }

    function display_header($trail)
    {
        $this->get_parent()->display_header($trail, false);
        $object_ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);

        if(!is_array($object_ids))
        {
        	$object_ids = array($object_ids);
        }

        $html = array();
        $html[] = '<div class="content_object padding_10">';
        $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects') . '</div>';
        $html[] = '<div class="description">';
        $html[] = '<ul class="attachments_list">';

        foreach($object_ids as $object_id)
        {
        	$object = $this->retrieve_content_object($object_id);
        	$html[] = '<li><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $object->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($object->get_type()) . 'TypeName')) . '"/> ' . $object->get_title() . '</li>';
        }

        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';

        echo implode("\n", $html);
    }

    function get_url($parameters)
    {
        $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_ID] = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);

        return parent :: get_url($parameters);
    }

    function get_parameters()
    {
        $parameters = parent :: get_parameters();
        $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_ID] = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        return $parameters;
    }

}
?>