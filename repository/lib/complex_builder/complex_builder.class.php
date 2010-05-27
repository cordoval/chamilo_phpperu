<?php
/**
 * $Id: complex_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder
 */
require_once dirname(__FILE__) . '/complex_menu.class.php';

/**
 * This class represents a basic complex builder structure.
 * When a builder is needed for a certain type of complex learning object an extension should be written.
 * We will make use of the repoviewer for selection, creation of learning objects
 *
 * @author Sven Vanpoucke
 *
 */
abstract class ComplexBuilder extends SubManager
{
    const PARAM_BUILDER_ACTION = 'builder_action';
    const PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID = 'cloi';
    const PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID = 'selected_cloi';
    const PARAM_DELETE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM = 'delete_selected_cloi';
    const PARAM_MOVE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM = 'move_selected_cloi';
    const PARAM_TYPE = 'type';
    const PARAM_DIRECTION = 'direction';
    

    const ACTION_BROWSE = 'browse';
    const ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM = 'delete_cloi';
    const ACTION_VIEW_COMPLEX_CONTENT_OBJECT_ITEM = 'view_cloi';
    const ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM = 'update_cloi';
    const ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM = 'create_cloi';
    const ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEM = 'move_cloi';
    const ACTION_CHANGE_PARENT = 'change_parent';

    private $menu;

    /**
     * The current item in treemenu to determine where we are in the structure
     * @var ComplexContentObjectItem
     */
    private $complex_content_object_item;

    /**
     * The item we select to execute an action like update / delete / move etc
     * @var ComplexContentObjectItem
     */
    private $selected_complex_content_object_item;

    function ComplexBuilder($parent)
    {
        parent :: __construct($parent);

        $complex_content_object_item_id = Request :: get(self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        if ($complex_content_object_item_id)
        {
            $this->complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_content_object_item_id);
        }

    	$selected_complex_content_object_item_id = Request :: get(self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        if ($selected_complex_content_object_item_id)
        {
            $this->selected_complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($selected_complex_content_object_item_id);
        }

        $this->set_action(Request :: get(self :: PARAM_BUILDER_ACTION));
        $this->parse_input_from_table();
    }

    //Singleton
    private static $instance;

    static function factory($parent, $type)
    {
        $file = dirname(__FILE__) . '/../content_object/' . $type . '/builder/' . $type . '_builder.class.php';
        require_once $file;
        $class = Utilities :: underscores_to_camelcase($type) . 'Builder';
    	return new $class($parent);
    }

    protected function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            $selected_ids = $_POST[RepositoryBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            switch ($_POST['action'])
            {
                case self :: PARAM_DELETE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM :
                    $this->set_action(self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM);
                    Request :: set_get(self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $selected_ids);
                    break;
                case self :: PARAM_MOVE_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM:
                	$this->set_action(self :: ACTION_CHANGE_PARENT);
                	Request :: set_get(self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID, $selected_ids);
                    break;
            }
        }
    }

    function get_action()
    {
        return $this->get_parameter(self :: PARAM_BUILDER_ACTION);
    }

    function set_action($action)
    {
        $this->set_parameter(self :: PARAM_BUILDER_ACTION, $action);
    }

    function get_root_content_object()
    { 
    	return $this->get_parent()->get_root_content_object();
    }

    function get_complex_content_object_item()
    {
    	return $this->complex_content_object_item;
    }

	function get_selected_complex_content_object_item()
    {
    	return $this->selected_complex_content_object_item;
    }

	function get_root_content_object_id()
    {
        return $this->get_parent()->get_root_content_object()->get_id();
    }

    function get_complex_content_object_item_id()
    {
    	if($this->complex_content_object_item)
    	{
    		return $this->complex_content_object_item->get_id();
    	}
    }

	function get_selected_complex_content_object_item_id()
    {
    	if($this->selected_complex_content_object_item)
    	{
    		return $this->selected_complex_content_object_item->get_id();
    	}
    }

    /**
     * Common functionality
     */

    function get_complex_content_object_table_html($show_subitems_column = true, $model = null, $renderer = null)
    {
        $parameters = $this->get_parameters();
    	$parameters[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $table = new ComplexBrowserTable($this, $parameters, $this->get_complex_content_object_table_condition(), $show_subitems_column, $model, $renderer);
        return $table->as_html();
    }

    function get_complex_content_object_table_condition()
    {
        if ($this->get_complex_content_object_item())
        {
            return new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_complex_content_object_item()->get_ref(), ComplexContentObjectItem :: get_table_name());
        }
        return new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_root_content_object_id(), ComplexContentObjectItem :: get_table_name());
    }

    function get_complex_content_object_menu()
    {
        if (is_null($this->menu))
        {
            $this->build_complex_content_object_menu();
        }
        return $this->menu->render_as_tree();
    }

    function get_complex_content_object_breadcrumbs()
    {
        if (is_null($this->menu))
        {
            $this->build_complex_content_object_menu();
        }
        return $this->menu->get_breadcrumbs();
    }

    private function build_complex_content_object_menu()
    {
        $this->menu = new ComplexMenu($this->get_root_content_object(), $this->get_complex_content_object_item(),
        							  $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE)));
    }

    //url building

    function get_complex_content_object_item_edit_url($selected_content_object_item_id)
    {
      
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
        							self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
        							self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }

    function get_complex_content_object_item_delete_url($selected_content_object_item_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
        							self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
        							self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }

	function get_complex_content_object_item_view_url($selected_content_object_item_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT_ITEM,
        							self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
                							self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }

    function get_complex_content_object_item_move_url($selected_content_object_item_id, $direction)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEM,
        							self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
                							self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
        							self :: PARAM_DIRECTION => $direction));
    }

	function get_complex_content_object_parent_changer_url($selected_content_object_item_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CHANGE_PARENT,
        							self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_content_object_item_id,
                							self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }

    function get_browse_url()
    {
    	return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE,
        							self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }

	function get_create_complex_content_object_item_url()
	{
    	return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
        							self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
    }
    
    function get_additional_links()
    {
    	return array();
    }

    function get_creation_links($content_object, $types = array(), $additional_links = array())
    {
        $html[] = '<div class="category_form"><div id="content_object_selection">';

        if (count($types) == 0)
        {
            $types = $content_object->get_allowed_types();
        }

        foreach ($types as $type)
        {
            $url = $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
            						    self :: PARAM_TYPE => $type,
            						    self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));

            $html[] = '<a href="' . $url . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
            $html[] = Translation :: get(ContentObject :: type_to_class($type) . 'TypeName');
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div></a>';
        }

        foreach ($this->get_additional_links() as $link)
        {
            $type = $link['type'];
            $html[] = '<a href="' . $link['url'] . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
            $html[] = $link['title'];
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div></a>';
        }

        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

	function get_application_component_path()
	{
		return Path :: get_repository_path() . 'lib/complex_builder/component/';
	}
	
	function show_menu()
	{
		return true;
	}
}

?>