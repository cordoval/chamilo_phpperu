<?php
require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';


class DefaultGradebookSubscribeUserTableCellRenderer implements ObjectTableCellRenderer
{

	function DefaultGradebookSubscribeUserTableCellRenderer()
	{
	}

	function render_cell($column, $user)
	{
		if ($property = $column->get_name())
		{
			switch ($property)
			{
				case User :: PROPERTY_ID :
					return $user->get_id();
				case User :: PROPERTY_LASTNAME :
					return $user->get_lastname();
				case User :: PROPERTY_FIRSTNAME :
					return $user->get_firstname();
				case User :: PROPERTY_USERNAME :
					return $user->get_username();
				case User :: PROPERTY_EMAIL :
					return $user->get_email();

			}
		}


		return '&nbsp;';
	}

	function render_id_cell($user){
		return $user->get_id();
	}

}
?>