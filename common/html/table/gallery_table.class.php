<?php
require_once "HTML/Table.php";
require_once "Pager/Pager.php";
require_once "Pager/Sliding.php";
require_once 'table_sort.class.php';
/**
 * This class allows you to display a sortable data-table. It is possible to
 * split the data in several pages.
 * Using this class you can:
 * - automatically create checkboxes of the first table column
 *     - a "select all" and "deselect all" link is added
 *     - only if you provide a list of actions for the selected items
 * - click on the table header to sort the data
 * - choose how many items you see per page
 * - navigate through all data-pages
 */
class GalleryTable extends HTML_Table
{
	/**
	 * A name for this table
	 */
	private $table_name;
	/**
	 * The page to display
	 */
	private $page_nr;
	/**
	 * The property on which we want to sort
	 */
	private $property;
    /**
     * The sorting direction (SORT_ASC or SORT_DESC)
     */
    private $direction;
    /**
     * Number of items to display per page
     */
    private $per_page;
    /**
     * The default number of items to display per page
     */
    private $default_items_per_page;
    /**
     * A prefix for the URL-parameters, can be used on pages with multiple
     * SortableTables
     */
    private $param_prefix;
    /**
     * The pager object to split the data in several pages
     */
    private $pager;
    /**
     * The total number of items in the table
     */
    private $total_number_of_items;
    /**
     * The function to get the total number of items
     */
    private $get_total_number_function;
    /**
     * The function to the the data to display
     */
    private $get_data_function;
	/**
	 * The function to the the sort properties
	 */
	var $get_properties_function;	
    /**
     * An array with defined column-filters
     */
    private $column_filters;
    /**
     * A list of actions which will be available through a select list
     */
    private $form_actions;
    /**
     * Additional parameters to pass in the URL
     */
    private $additional_parameters;
    /**
     * Additional attributes for the th-tags
     */
    private $th_attributes;
    /**
     * Additional attributes for the td-tags
     */
    private $td_attributes;
    /**
     * Additional attributes for the tr-tags
     */
    private $tr_attributes;
    /**
     * Array with names of the other tables defined on the same page of this
     * table
     */
    private $other_tables;
	
	/**
	 * Create a new SortableTable
	 * @param string $table_name A name for the table (default = 'table')
	 * @param string $get_total_number_function A user defined function to get
	 * the total number of items in the table
	 * @param string $get_data_function A function to get the data to display on
	 * the current page
	 * @param int $default_column The default column on which the data should be
	 * sorted
	 * @param int $default_items_per_page The default number of items to show
	 * on one page
	 * @param int $default_order_direction The default order direction; either
	 * the constant SORT_ASC or SORT_DESC
	 */
	function GalleryTable($table_name = 'table', $get_total_number_function = null, $get_data_function = null, $get_properties_function = null, $default_items_per_page = 20, $default_property = 0, $default_order_direction = SORT_ASC, $ajax_enabled = false)
	{
		parent :: HTML_Table(array ('class' => 'gallery_table'), 0, true);
		$this->table_name = $table_name;
		$this->additional_parameters = array ();
		$this->param_prefix = $table_name.'_';
		
		$this->page_nr = isset ($_GET[$this->param_prefix.'page_nr']) ? $_GET[$this->param_prefix.'page_nr'] : 1;
		$this->per_page = isset ($_GET[$this->param_prefix.'per_page']) ? $_GET[$this->param_prefix.'per_page'] : $default_items_per_page;
		$this->property = isset ($_GET[$this->param_prefix.'property']) ? $_GET[$this->param_prefix.'property'] : $default_property;
		$this->direction = isset ($_GET[$this->param_prefix.'direction']) ? $_GET[$this->param_prefix.'direction'] : $default_direction;
		
		$this->pager = null;
		$this->default_items_per_page = $default_items_per_page;
		$this->total_number_of_items = -1;
		$this->get_total_number_function = $get_total_number_function;
		$this->total_number_of_items = $this->get_total_number_of_items();
		$this->get_data_function = $get_data_function;
   		$this->get_properties_function = $get_properties_function;
        if ($this->per_page == 'all')
        {
        	$this->per_page = $this->total_number_of_items;
        }

        $this->ajax_enabled = $ajax_enabled;
		$this->column_filters = array ();
		$this->form_actions = array ();
		$this->checkbox_name = null;
		$this->td_attributes = array ();
		$this->th_attributes = array ();
		$this->other_tables = array();
	}
	
	 
	
	
	/**
	 * Get the Pager object to split the showed data in several pages
	 */
	function get_pager()
	{
        if (is_null($this->pager))
        {
            $total_number_of_items = $this->total_number_of_items;
            $params['mode'] = 'Sliding';
            $params['perPage'] = $this->per_page;
            $params['totalItems'] = $total_number_of_items;
            $params['urlVar'] = $this->param_prefix . 'page_nr';
            $params['prevImg'] = '<img src="' . Theme :: get_common_image_path() . 'action_prev.png"  style="vertical-align: middle;"/>';
            $params['nextImg'] = '<img src="' . Theme :: get_common_image_path() . 'action_next.png"  style="vertical-align: middle;"/>';
            $params['firstPageText'] = '<img src="' . Theme :: get_common_image_path() . 'action_first.png"  style="vertical-align: middle;"/>';
            $params['lastPageText'] = '<img src="' . Theme :: get_common_image_path() . 'action_last.png"  style="vertical-align: middle;"/>';
            $params['firstPagePre'] = '';
            $params['lastPagePre'] = '';
            $params['firstPagePost'] = '';
            $params['lastPagePost'] = '';
            $params['spacesBeforeSeparator'] = '';
            $params['spacesAfterSeparator'] = '';
            $params['currentPage'] = $this->page_nr;
            $query_vars = array_keys($_GET);
            $query_vars_needed = array($this->param_prefix . 'column', $this->param_prefix . 'direction', $this->param_prefix . 'per_page');
            if (count($this->additional_parameters) > 0)
            {
                $query_vars_needed = array_merge($query_vars_needed, array_keys($this->additional_parameters));
            }
            $query_vars_exclude = array_diff($query_vars, $query_vars_needed);
            $params['excludeVars'] = $query_vars_exclude;
            $params['extraVars'] = $this->additional_parameters;
            $this->pager = Pager :: factory($params);
        }
        return $this->pager;
	}
	/**
	 * Displays the table, complete with navigation buttons to browse through
	 * the data-pages.
	 */
	function display()
	{
		echo $this->as_html();
	}
	/**
	 * Returns the complete table HTML. Alias of as_html().
	 */
	function toHTML()
	{
		return $this->as_html();
	}
	
    function toHTML_export()
    {
        return $this->as_html(true);
    }
    
	/**
	 * Returns the complete table HTML.
	 */
	function as_html($empty_table = false)
	{
		$empty_table = false;
		if ($this->get_total_number_of_items() == 0)
		{
			$cols = $this->getColCount();
			$this->setCellAttributes(1, 0, 'style="font-style: italic;text-align:center;" colspan='.$cols);
			$this->setCellContents(1, 0, Translation :: get('NoSearchResults'));
			$empty_table = true;
		}
		if (!$empty_table)
		{
			$page = $this->get_page_select_form();
			$sort = $this->get_sort_select_form();
			$direction = $this->get_direction_select_form();
			$nav = $this->get_navigation_html();
			
			$html = '<table style="width:100%;">';
			$html .= '<tr>';
			$html .= '<td style="width:25%;">';
			$html .= $page;
			$html .= $sort;
			$html .= $direction;
			$html .= '</td>';
			$html .= '<td style="text-align:center;">';
			$html .= $this->get_table_title();
			$html .= '</td>';
			$html .= '<td style="text-align:right;width:25%;">';
			$html .= $nav;
			$html .= '</td>';
			$html .= '</tr>';
			$html .= '</table>';
			if (count($this->form_actions))
			{
				$html .= '<script type="text/javascript">
							/* <![CDATA[ */
							function setCheckbox(formName, value) {
								var d = document[formName];
								for (i = 0; i < d.elements.length; i++) {
									if (d.elements[i].type == "checkbox") {
									     d.elements[i].checked = value;
									}
								}
							}
							function anyCheckboxChecked(formName) {
								var d = document[formName];
								for (i = 0; i < d.elements.length; i++) {
									if (d.elements[i].type == "checkbox" && d.elements[i].checked)
										return true;
								}
								return false;
							}
							/* ]]> */
							</script>';
				$params = $this->get_gallery_table_param_string.'&amp;'.$this->get_additional_url_paramstring();

				$html .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?'.$params.'" name="form_'.$this->table_name.'"  onsubmit="return anyCheckboxChecked(\'form_'.$this->table_name.'\') &amp;&amp; confirm(\''.addslashes(htmlentities(Translation :: get("ConfirmYourChoice"))).'\');">';
			}
		}
		$html .= $this->get_table_html();
		if (!$empty_table)
		{
			$html .= '<table style="width:100%;">';
			$html .= '<tr>';
			$html .= '<td colspan="2">';
			if (count($this->form_actions))
			{
				$html .= '<a href="?'.$params.'&amp;'.$this->param_prefix.'selectall=1" onclick="setCheckbox(\'form_'.$this->table_name.'\', true); return false;">'.Translation :: get('SelectAll').'</a> - ';
				$html .= '<a href="?'.$params.'"  onclick="setCheckbox(\'form_'.$this->table_name.'\', false); return false;">'.Translation :: get('UnSelectAll').'</a> ';
				$html .= '<select name="action">';
				foreach ($this->form_actions as $action => $label)
				{
					$html .= '<option value="'.$action.'">'.$label.'</option>';
				}
				$html .= '</select>';
				$html .= '<input type="submit" value="'.Translation :: get('Ok').'"/>';
			}
			else
			{
				$html .= $form;
			}
			$html .= '</td>';
			$html .= '<td style="text-align:right;">';
			$html .= $nav;
			$html .= '</td>';
			$html .= '</tr>';
			$html .= '</table>';
			if (count($this->form_actions) > 0)
			{
				$html .= '</form>';
			}
		}
		return $html;
	}
	/**
	 * Get the HTML-code with the navigational buttons to browse through the
	 * data-pages.
	 */
    function get_navigation_html()
    {
        $pager = $this->get_pager();
        $pager_links = $pager->getLinks();
        $showed_items = $pager->getOffsetByPageId();
        return $pager_links['first'] . ' ' . $pager_links['back'] .
            ' ' . $pager->getCurrentPageId() . ' / ' . $pager->numPages() . ' ' .
            $pager_links['next'] . ' ' . $pager_links['last'];
    }
	/**
	 * Get the HTML-code with the data-table.
	 */
	function get_table_html()
	{
		$pager = $this->get_pager();
		$offset = $pager->getOffsetByPageId();
		$from = $offset[0] - 1;
		$table_data = $this->get_table_data($from);
		foreach ($table_data as $index => $row)
		{
			$row = $this->filter_data($row);
			$this->addRow($row);
		}
		$this->altRowAttributes(0, array ('class' => 'row_odd'), array ('class' => 'row_even'), true);
		$this->altColAttributes(0, array ('class' => 'col_odd'), array ('class' => 'col_even'), true);
		
		foreach ($this->th_attributes as $column => $attributes)
		{
			$this->setCellAttributes(0, $column, $attributes);
		}
		foreach ($this->td_attributes as $column => $attributes)
		{
			$this->setColAttributes($column, $attributes);
		}
		return parent :: toHTML();
	}
	/**
	 * Get the HTML-code wich represents a form to select how many items a page
	 * should contain.
	 */
	function get_page_select_form()
	{
        $total_number_of_items = $this->total_number_of_items;
        if ($total_number_of_items <= $this->default_items_per_page)
        {
            return '';
        }
        $result[] = '<form method="get" action="' . $_SERVER['PHP_SELF'] . '" style="display:inline;">';
        $param[$this->param_prefix . 'direction'] = $this->direction;
        $param[$this->param_prefix . 'page_nr'] = $this->page_nr;
        $param[$this->param_prefix . 'property'] = $this->property;
        $param = array_merge($param, $this->additional_parameters);
        foreach ($param as $key => & $value)
        {
            if (is_array($value))
            {
                $ser = self :: serialize_array($value, $key);
                $result = array_merge($result, $ser);
            }
            else
            {
                $result[] = '<input type="hidden" name="' . $key . '" value="' . $value . '"/>';
            }
        }
        $result[] = '<select name="' . $this->param_prefix . 'per_page" onchange="javascript:this.form.submit();">';
        for ($nr = 10; $nr <= min(50, $total_number_of_items); $nr += 10)
        {
            $result[] = '<option value="' . $nr . '" ' . ($nr == $this->per_page ? 'selected="selected"' : '') . '>' . $nr . '</option>';
        }
        if ($total_number_of_items < 500)
        {
            //$result[] = '<option value="' . $total_number_of_items . '" ' . ($total_number_of_items == $this->per_page ? 'selected="selected"' : '') . '>ALL</option>';
            $result[] = '<option value="' . 'all' . '" ' . ($total_number_of_items == $this->per_page ? 'selected="selected"' : '') . '>ALL</option>';
        }
        $result[] = '</select>';
        $result[] = '<noscript>';
        $result[] = '<button class="normal" type="submit" value="' . Translation :: get('Ok') . '">' . Translation :: get('Ok') . '</button>';
        $result[] = '</noscript>';
        $result[] = '</form>';
        return implode("\n", $result);
	}

	/**
	 * Get the HTML-code wich represents a form to select what property the gallery should be sorted on.
	 */
	function get_sort_select_form()
	{
		$properties = $this->get_table_properties();
		$result = array();
		
		if (count($properties) > 1)
		{
			$result[] = '<form method="get" action="'.$_SERVER['PHP_SELF'].'" style="display:inline;">';
			$param[$this->param_prefix.'page_nr'] = $this->page_nr;
			$param[$this->param_prefix.'direction'] = $this->direction;
			$param = array_merge($param, $this->additional_parameters);
			
			foreach ($param as $key => $value)
			{
				if (is_array($value))
				{
					$ser = self :: serialize_array($value, $key);
					$result = array_merge($result, $ser);
				}
				else
				{
					$result[] = '<input type="hidden" name="'.$key.'" value="'.$value.'"/>';
				}
			}
			
			$result[] = '<select name="'.$this->param_prefix.'property" onchange="javascript:this.form.submit();">';
			
			foreach ($properties as $index => $property)
			{
				$result[] = '<option value="'.$index.'" '. ($index == $this->property ? 'selected="selected"' : '').'>'. Translation :: get('Sort' . ucfirst($property)) .'</option>';
			}

			$result[] = '</select>';
			$result[] = '<noscript>';
			$result[] = '<input type="submit" value="ok"/>';
			$result[] = '</noscript>';
			$result[] = '</form>';
		}
		return implode("\n", $result);
	}
	
	/**
	 * Get the HTML-code wich represents a form to select the order direction.
	 */
	function get_direction_select_form()
	{
		$result[] = '<form method="get" action="'.$_SERVER['PHP_SELF'].'" style="display:inline;">';
		$param[$this->param_prefix.'page_nr'] = $this->page_nr;
		$param[$this->param_prefix.'property'] = $this->property;
		$param = array_merge($param, $this->additional_parameters);
		foreach ($param as $key => $value)
		{
			if (is_array($value))
			{
				$ser = self :: serialize_array($value, $key);
				$result = array_merge($result, $ser);
			}
			else
			{
				$result[] = '<input type="hidden" name="'.$key.'" value="'.$value.'"/>';
			}
		}
		$result[] = '<select name="'.$this->param_prefix.'direction" onchange="javascript:this.form.submit();">';
		$result[] = '<option value="'. SORT_ASC .'" '. (SORT_ASC == $this->direction ? 'selected="selected"' : '').'>'. Translation :: get('ASC') .'</option>';
		$result[] = '<option value="'. SORT_DESC .'" '. (SORT_DESC == $this->direction ? 'selected="selected"' : '').'>'. Translation :: get('DESC') .'</option>';
		$result[] = '</select>';
		$result[] = '<noscript>';
		$result[] = '<input type="submit" value="ok"/>';
		$result[] = '</noscript>';
		$result[] = '</form>';
		$result = implode("\n", $result);
		return $result;
	}

    /**
     * Get the table title.
     */
    function get_table_title()
    {
        $showed_items = $this->get_pager()->getOffsetByPageId();
        return $showed_items[0] . ' - ' . $showed_items[1] . ' / ' . $this->total_number_of_items;
    }

    /**
     * Get the parameter-string with additional parameters to use in the URLs
     * generated by this SortableTable
     */
    function get_additional_url_paramstring()
    {
        $param_string_parts = array();
        foreach ($this->additional_parameters as $key => & $value)
        {
            if (is_array($value))
            {
                $ser = self :: serialize_array($value, $key, true);
                $param_string_parts = array_merge($param_string_parts, $ser);
            }
            else
            {
                $param_string_parts[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        $result = implode('&amp;', $param_string_parts);
        foreach ($this->other_tables as $index => & $tablename)
        {
            if (Request :: get($tablename . '_direction'))
                $param[$tablename . '_direction'] = Request :: get($tablename . '_direction');
            if (Request :: get($tablename . '_page_nr'))
                $param[$tablename . '_page_nr'] = Request :: get($tablename . '_page_nr');
            if (Request :: get($tablename . '_per_page'))
                $param[$tablename . '_per_page'] = Request :: get($tablename . '_per_page');
            if (Request :: get($tablename . '_column'))
                $param[$tablename . '_column'] = Request :: get($tablename . '_column');
            $param_string_parts = array();
            foreach ($param as $key => & $value)
            {
                $param_string_parts[] = urlencode($key) . '=' . urlencode($value);
            }
            if (count($param_string_parts) > 0)
                $result .= '&amp;' . implode('&amp;', $param_string_parts);
        }
        return $result;
    }

	/**
	 * Get the parameter-string with the SortableTable-related parameters to use
	 * in URLs
	 */
	function get_gallery_table_param_string()
	{
        $param[$this->param_prefix . 'direction'] = $this->direction;
		$param[$this->param_prefix.'page_nr'] = $this->page_nr;
		$param[$this->param_prefix.'per_page'] = $this->per_page;
        $param[$this->param_prefix . 'property'] = $this->property;
		$param_string_parts = array ();
        foreach ($param as $key => & $value)
        {
            $param_string_parts[] = urlencode($key) . '=' . urlencode($value);
        }
        return implode('&amp;', $param_string_parts);
	}
	/**
	 * Add a filter to a column. If another filter was allready defined for the
	 * given column, it will be overwritten.
	 * @param int $column The number of the column
	 * @param string $function The name of the filter-function. This should be a
	 * function wich requires 1 parameter and returns the filtered value.
	 */
	function set_column_filter($column, $function)
	{
		$this->column_filters[$column] = $function;
	}

    /**
     * Define a list of actions which can be performed on the table-date.
     * If you define a list of actions, the first column of the table will be
     * converted into checkboxes.
     * @param array $actions A list of actions. The key is the name of the
     * action. The value is the label to show in the select-box
     * @param string $checkbox_name The name of the generated checkboxes. The
     * value of the checkbox will be the value of the first column.
     */
    function set_form_actions($actions, $checkbox_name = 'id', $select_name = 'action')
    {
        $this->form_actions = $actions;
        $this->checkbox_name = $checkbox_name;
        $this->form_actions_select_name = $select_name;
    }

	/**
	 * Define a list of additional parameters to use in the generated URLs
	 * @param array $parameters
	 */
	function set_additional_parameters($parameters)
	{
		$this->additional_parameters = $parameters;
	}

	/**
	 * Set other tables on the same page.
	 * If you have other sortable tables on the page displaying this sortable
	 * tables, you can define those other tables with this function. If you
	 * don't define the other tables, there sorting and pagination will return
	 * to their default state when sorting this table.
	 * @param array $tablenames An array of table names.
	 */
	function set_other_tables($tablenames)
	{
		$this->other_tables = $tablenames;
	}

	/**
	 * Transform all data in a table-row, using the filters defined by the
	 * function set_column_filter(...) defined elsewhere in this class.
	 * If you've defined actions, the first element of the given row will be
	 * converted into a checkbox
	 * @param array $row A row from the table.
	 */
	function filter_data($row)
	{
		foreach ($row as $index => $value)
		{
			if (strlen($row[$index][0]) == 0)
			{
				$row[$index] = '-';
			}
			else
			{
				$row[$index] = $row[$index][1];
				if (count($this->form_actions) > 0)
				{
					$row[$index] .= '<br /><input type="checkbox" name="'.$this->checkbox_name.'[]" value="'.$value[0].'"';
					if (isset ($_GET[$this->param_prefix.'selectall']))
					{
						$row[$index] .= ' checked="checked"';
					}
					$row[$index] .= '/>';
				}
			}
		}
		return $row;
	}
	/**
	 * Get the total number of items. This function calls the function given as
	 * 2nd argument in the constructor of a SortableTable. Make sure your
	 * function has the same parameters as defined here.
	 */
	function get_total_number_of_items()
	{
		if ($this->total_number_of_items == -1 && !is_null($this->get_total_number_function))
		{
			$this->total_number_of_items = call_user_func($this->get_total_number_function);
		}
		return $this->total_number_of_items;
	}
	/**
	 * Get the data to display.  This function calls the function given as
	 * 2nd argument in the constructor of a SortableTable. Make sure your
	 * function has the same parameters as defined here.
	 * @param int $from Index of the first item to return.
	 * @param int $per_page The number of items to return
     * @param int $column The number of the column on which the data should be
     * sorted
     * @param string $direction In which order should the data be sorted (ASC
     * or DESC)
	 */
	function get_table_data($from = null, $per_page = null, $property = null, $direction = null)
	{
		if (!is_null($this->get_data_function))
		{
			return call_user_func($this->get_data_function, $from, $this->per_page, $this->property, $this->direction);
		}
		return array ();
	}
	/**
	 * Get the sort properties.  This function calls the function given as
	 * 3rd argument in the constructor of a GalleryTable. Make sure your
	 * function has the same parameters as defined here.
	 */
	function get_table_properties()
	{
		if (!is_null($this->get_properties_function))
		{
			return call_user_func($this->get_properties_function);
		}
		return array ();
	}
	/**
	 * Serializes a URL parameter passed as an array into a query string or
	 * hidden inputs.
	 * @param array $params The parameter's value.
	 * @param string $key The parameter's name.
	 * @param boolean $as_query_string True to format the result as a query
	 *                                 string, false for hidden inputs.
	 * @return array The query string parts (to be joined by ampersands or
	 *               another separator), or the hidden inputs as HTML, each
	 *               array element containing a single input.
	 */
    private function serialize_array($params, $key, $as_query_string = false)
    {
        $out = array();
        foreach ($params as $k => & $v)
        {
            if (is_array($v))
            {
                $ser = self :: serialize_array($v, $key . '[' . $k . ']', $as_query_string);
                $out = array_merge($out, $ser);
            }
            else
            {
                $v = urlencode($v);
            }
            $k = urlencode($key . '[' . $k . ']');
            $out[] = ($as_query_string ? $k . '=' . $v : '<input type="hidden" name="' . $k . '" value="' . $v . '"/>');
        }
        return $out;
    }

    /**
     * Gets the AJAX status of the table
     *
     * @return boolean Whether or not the table should have AJAX functionality
     */
    function is_ajax_enabled()
    {
        return $this->ajax_enabled;
    }

    /**
     * Sets the table's AJAX status to true
     */
    function enable_ajax()
    {
        $this->ajax_enabled = true;
    }

    /**
     * Sets the table's AJAX status to false
     */
    function disable_ajax()
    {
        $this->ajax_enabled = false;
    }

    function get_per_page()
    {
        return $this->per_page;
    }

    function get_column()
    {
        return $this->column;
    }

    function get_direction()
    {
        return $this->direction;
    }
}
/**
 * Sortable table which can be used for data available in an array
 */
class GalleryTableFromArray extends GalleryTable
{
	/**
	 * The array containing all data for this table
	 */
	private $table_data;
	/**
	 * Constructor
	 * @param array $table_data
	 * @param int $default_items_per_page
	 */
	
	function GalleryTableFromArray($table_data, $default_property = 1, $default_items_per_page = 20, $tablename = 'tablename')
	{
        $this->table_data = $table_data;
		parent :: GalleryTable($tablename, array($this, 'get_total_number_of_items'), array($this, 'get_table_data'), array($this, 'get_table_properties'), $default_items_per_page, $default_property, $default_order_direction, $ajax_enabled);
	}
	/**
	 * Get table data to show on current page
	 * @see SortableTable#get_table_data
	 */
	function get_table_data($from = 1)
	{
        $content = TableSort :: sort_table($this->table_data, $this->get_property(), $this->get_direction());
        return array_slice($content, $from, $this->get_per_page());
	}
	/**
	 * Get total number of items
	 * @see SortableTable#get_total_number_of_items
	 */
	function get_total_number_of_items()
	{
		return count($this->table_data);
	}
}
?>