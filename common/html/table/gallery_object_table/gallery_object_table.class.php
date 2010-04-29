<?php
class GalleryObjectTable
{
	/**
	 * Suffix for checkbox name when using actions on selected learning objects.
	 */
	const CHECKBOX_NAME_SUFFIX = '_id';
	/**
	 * The name of this table
	 */
	private $table_name;
	/**
	 * The default number of rows to display in this table
	 */
	private $default_row_count;
	/**
	 * The default number of columns to display in this table
	 */
	private $default_column_count;
	/**
	 * The column model assigned to this table
	 */
	private $column_model;
	/**
	 * The data provider assigned to this table
	 */
	private $data_provider;
	/**
	 * The cell renderer assigned to this table
	 */
	private $cell_renderer;
	/**
	 * The available sort properties
	 */
	private $properties;
	/**
	 * Additional parameters
	 */
	private $additional_parameters;
	/**
	 * The form actions to use in this table
	 */
	private $form_actions;
	
	private $ajax_enabled;
	private $enable_order_directions;

	/**
	 * Constructor. Creates a michelangelo table.
	 * @param ObjectTableDataProvider $data_provider The data provider,
	 *                                                       which supplies the
	 *                                                       learning objects
	 *                                                       to display.
	 * @param string $table_name The name for the HTML table element.
	 * @param ObjectTableColumnModel $column_model The column model of
	 *                                                     the table. Omit to
	 *                                                     use the default
	 *                                                     model.
	 * @param ObjectTableCellRenderer $cell_renderer The cell renderer
	 *                                                       for the table.
	 *                                                       Omit to use the
	 *                                                       default renderer.
	 */
	function GalleryObjectTable($data_provider, $table_name = null, $cell_renderer = null, $properties = array())
	{
		$this->set_data_provider($data_provider);
		$this->set_name($table_name);
		$this->set_cell_renderer($cell_renderer);
		$this->set_properties($properties);
		$this->set_default_row_count(10);
		$this->set_default_column_count(3);
		$this->set_order_directions_enabled(true);
		$this->set_additional_parameters($this->determine_additional_parameters());
	}
	/**
	 * Determines the additional parameters from $_GET and $_POST variables
	 * @return array An array with all additional parameters
	 */
	private function determine_additional_parameters()
	{
		$prefix = $this->get_name().'_';
		$out = array();
		$param = array_merge($_GET, $_POST);
		foreach ($param as $k => $v)
		{
			if (strpos($k, $prefix) === false)
			{
				$out[$k] = $v;
			}
		}
		return $out;
	}

	/**
	 * Creates an HTML representation of the table.
	 * @return string The HTML.
	 */
	function as_html()
	{
		$count = $this->get_default_column_count() * $this->get_default_row_count();
		$table = new GalleryTable($this->get_name(), array ($this, 'get_object_count'), array ($this, 'get_objects'), array ($this, 'get_properties'), $count, 0, SORT_ASC, $this->get_order_directions_enabled(), $this->get_ajax_enabled());
		
		$table->set_additional_parameters($this->get_additional_parameters());
		if ($this->has_form_actions())
		{
			$table->set_form_actions($this->get_form_actions(), $this->get_checkbox_name());
		}
		return $table->as_html();
	}

	/**
	 * Gets the default row count of the table.
	 * @return int The number of rows.
	 */
	function get_default_row_count()
	{
		return $this->default_row_count;
	}
	
	/**
	 * Gets the default column count of the table.
	 * @return int The number of columns.
	 */
	function get_default_column_count()
	{
		return $this->default_column_count;
	}

	/**
	 * Sets the default row count of the table.
	 * @param int $default_row_count The number of rows.
	 */
	function set_default_row_count($default_row_count)
	{
		$this->default_row_count = $default_row_count;
	}
	
	/**
	 * Sets the default column count of the table.
	 * @param int $default_column_count The number of columns.
	 */
	function set_default_column_count($default_column_count)
	{
		$this->default_column_count = $default_column_count;
	}

	/**
	 * Gets the name of the HTML table element.
	 * @return string The name.
	 */
	function get_name()
	{
		return $this->table_name;
	}

	/**
	 * Sets the name of the HTML table element.
	 * @param string $name The name.
	 */
	function set_name($name)
	{
		$this->table_name = $name;
	}
	
	/**
     * Gets whether the table is ajax enabled.
     * @return string The ajax_enabled property.
     */
    function get_ajax_enabled()
    {
        return $this->ajax_enabled;
    }

    /**
     * Sets whether the table is ajax enabled.
     * @param string $ajax_enabled The ajax_enabled property.
     */
    function set_ajax_enabled($ajax_enabled)
    {
        $this->ajax_enabled = $ajax_enabled;
    }
    
    function get_order_directions_enabled()
    {
    	return $this->enable_order_directions;
    }

    function set_order_directions_enabled($enable_order_directions)
    {
    	$this->enable_order_directions = $enable_order_directions;
    }
	/**
	 * Gets the table's data provider.
	 * @return MichelangeloTableDataProvider The data provider.
	 */
	function get_data_provider()
	{
		return $this->data_provider;
	}

	/**
	 * Sets the table's data provider.
	 * @param MichelangeloTableDataProvider $data_provider The data provider.
	 */
	function set_data_provider($data_provider)
	{
		$this->data_provider = $data_provider;
	}
	
	/**
	 * Gets the table's cell renderer.
	 * @return MichelangeloTableCellRenderer The cell renderer.
	 */
	function get_cell_renderer()
	{
		return $this->cell_renderer;
	}
	
	/**
	 * Gets the table's properties.
	 * @return Array The properties.
	 */
	function get_properties()
	{
		return $this->properties;
	}

	/**
	 * Sets the table's cell renderer.
	 * @param MichelangeloTableCellRenderer $renderer The cell renderer.
	 */
	function set_cell_renderer($renderer)
	{
		$this->cell_renderer = $renderer;
	}
	
	/**
	 * Sets the table's properties.
	 * @param Array $properties The properties.
	 */
	function set_properties($properties)
	{
		$this->properties = $properties;
	}

	/**
	 * Gets the additional parameters to use in URLs the table generates.
	 * @return array The parameters as an associative array.
	 */
	function get_additional_parameters()
	{
		return $this->additional_parameters;
	}

	/**
	 * Sets the additional parameters to use in URLs the table generates.
	 * @param array $parameters The parameters as an associative array.
	 */
	function set_additional_parameters($parameters)
	{
		$this->additional_parameters = $parameters;
	}

	/**
	 * Gets the actions for the mass-update form at the bottom of the table.
	 * @return array The actions as an associative array.
	 */
	function get_form_actions()
	{
		return $this->form_actions;
	}

	/**
	 * Sets the actions for the mass-update form at the bottom of the table.
	 * @param array $actions The actions as an associative array.
	 */
	function set_form_actions($actions)
	{
		$this->form_actions = $actions;
	}

	/**
	 * Checks if the table has form actions, i.e. if the form at its bottom
	 * allows the user to mass-update learning objects.
	 * @return boolean True if mass-updating is enabled, false otherwise.
	 */
	function has_form_actions()
	{
		return count($this->get_form_actions());
	}

	/**
	 * Gets the element name that the checkboxes before learning objects
	 * displayed by this table share. Only applies if the table allows
	 * mass-updating.
	 * @return string The element name.
	 */
	function get_checkbox_name()
	{
		return $this->get_name().self :: CHECKBOX_NAME_SUFFIX;
	}

	/**
	 * You should not be concerned with this method. It is only public because
	 * of technical limitations.
	 */
	function get_objects($offset, $count, $order_property, $order_direction)
	{
		$property = $this->get_properties();
		$objects = $this->get_data_provider()->get_objects($offset, $count, $property[$order_property], $order_direction);
		
		$column_count = $this->get_default_column_count();
		$row_count = $this->get_default_row_count();
		
		$table_data = array ();
		$row = array ();
		$i = 0;
		foreach($objects as $object)
		{
			if ($i >= $column_count)
			{
				$table_data[] = $row;
				$row = array();
				
				$row[] = array($object->get_id(), $this->get_cell_renderer()->render_cell($object));
				$i = 1;
			}
			else
			{
				$row[] = array($object->get_id(), $this->get_cell_renderer()->render_cell($object));
				$i++;
			}
		}
		$table_data[] = $row;
		return $table_data;
	}

	/**
	 * You should not be concerned with this method. It is only public because
	 * of technical limitations.
	 */
	function get_object_count()
	{
		return $this->get_data_provider()->get_object_count();
	}
}
?>