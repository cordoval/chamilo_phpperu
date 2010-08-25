<?php
/**
 * $Id: course_group_table.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_group.component.course_group_table
 */

class CourseGroupTable
{
    /**
     * Default table name
     */
    const DEFAULT_NAME = 'course_group';
    /**
     * Suffix for checkbox name when using actions on selected course_groups.
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
     * Additional parameters
     */
    private $additional_parameters;
    /**
     * The form actions to use in this table
     */
    private $form_actions;

    private $parent;
    
    /**
     * Constructor. Creates a course_group table.
     * @param CourseGroupTableDataProvider $data_provider The data provider, which
     * supplies the course_groups to display.
     * @param string $table_name The name for the HTML table element.
     * @param CourseGroupTableColumnModel $column_model The column model of the table.
     * Omit to use the default model.
     * @param CourseGroupTableCellRenderer $cell_renderer The cell renderer for the
     * table. Omit to use the default renderer.
     */
    function CourseGroupTable($parent, $data_provider, $table_name = null, $column_model = null, $cell_renderer = null)
    {
        $this->parent = $parent;
    	$this->set_data_provider($data_provider);
        $this->set_name(isset($table_name) ? $table_name : self :: DEFAULT_NAME);
        $this->set_column_model(isset($column_model) ? $column_model : new DefaultCourseGroupTableColumnModel($data_provider->get_parent()));
        $this->set_cell_renderer(isset($cell_renderer) ? $cell_renderer : new DefaultCourseGroupTableCellRenderer($data_provider->get_parent()));
        $this->set_default_row_count(10);
        $this->set_additional_parameters($this->determine_additional_parameters());
        
        $actions = array();
        if($parent->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
	    	$actions[] = new ObjectTableFormAction(CourseGroupTool :: PARAM_DELETE_COURSE_GROUPS, Translation :: get('RemoveSelected'));
	    	$this->set_form_actions(new ObjectTableFormActions(null,$actions));
        }
    }

    /**
     * Determines the additional parameters from $_GET and $_POST variables
     * @return array An array with all additional parameters
     */
    private function determine_additional_parameters()
    {
        $prefix = $this->get_name() . '_';
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
        $table = new SortableTable($this->get_name(), array($this, 'get_course_group_count'), array($this, 'get_course_groups'), $this->get_column_model()->get_default_order_column() + ($this->has_form_actions() ? 1 : 0), $this->get_default_row_count(), $this->get_column_model()->get_default_order_direction());
        $table->set_additional_parameters($this->get_additional_parameters());
        if ($this->has_form_actions())
        {
            $table->set_form_actions($this->get_form_actions(), $this->get_checkbox_name());
            $table->set_header(0, '', false);
        }
        $column_count = $this->get_column_model()->get_column_count();
        for($i = 0; $i < $column_count; $i ++)
        {
            $column = $this->get_column_model()->get_column($i);
            $table->set_header(($this->has_form_actions() ? $i + 1 : $i), htmlentities($column->get_title()), $column->is_sortable());
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
     * Sets the default row count of the table.
     * @param int $default_row_count The number of rows.
     */
    function set_default_row_count($default_row_count)
    {
        $this->default_row_count = $default_row_count;
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
     * Gets the table's data provider.
     * @return CourseGroupTableDataProvider The data provider.
     */
    function get_data_provider()
    {
        return $this->data_provider;
    }

    /**
     * Sets the table's data provider.
     * @param CourseGroupTableDataProvider $data_provider The data provider.
     */
    function set_data_provider($data_provider)
    {
        $this->data_provider = $data_provider;
    }

    /**
     * Gets the table's column model.
     * @return CourseGroupTableColumnModel The column model.
     */
    function get_column_model()
    {
        return $this->column_model;
    }

    /**
     * Sets the table's column model.
     * @param CourseGroupTableColumnModel $model The column model.
     */
    function set_column_model($model)
    {
        $this->column_model = $model;
    }

    /**
     * Gets the table's cell renderer.
     * @return CourseGroupTableCellRenderer The cell renderer.
     */
    function get_cell_renderer()
    {
        return $this->cell_renderer;
    }

    /**
     * Sets the table's cell renderer.
     * @param CourseGroup $renderer The cell renderer.
     */
    function set_cell_renderer($renderer)
    {
        $this->cell_renderer = $renderer;
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
        return $this->get_name() . self :: CHECKBOX_NAME_SUFFIX;
    }

    /**
     * You should not be concerned with this method. It is only public because
     * of technical limitations.
     */
    function get_course_groups($offset, $count, $order_column)
    {
        $course_groups = $this->get_data_provider()->get_course_groups($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
        if (is_null($course_groups))
        {
            return array();
        }
        $table_data = array();
        $column_count = $this->get_column_model()->get_column_count();
        while ($course_group = $course_groups->next_result())
        {
            $row = array();
            if ($this->has_form_actions())
            {
                $row[] = $course_group->get_id();
            }
            for($i = 0; $i < $column_count; $i ++)
            {
                $row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $course_group);
            }
            $table_data[] = $row;
        }
        return $table_data;
    }

    /**
     * You should not be concerned with this method. It is only public because
     * of technical limitations.
     */
    function get_course_group_count()
    {
        return $this->get_data_provider()->get_course_group_count();
    }
}
?>