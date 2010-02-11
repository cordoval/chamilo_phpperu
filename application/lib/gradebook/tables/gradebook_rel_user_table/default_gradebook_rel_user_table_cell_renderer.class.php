<?php

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../gradebook_rel_user.class.php';

class DefaultGradebookRelUserTableCellRenderer implements ObjectTableCellRenderer
{

	function DefaultGradebookRelUserTableCellRenderer()
	{
	}

	function render_cell($column, $gradebookreluser)
	{
		$user_id = $gradebookreluser->get_user_id();
		$user = UserDataManager ::get_instance()->retrieve_user($user_id);

		if ($property = $column->get_name())
		{
			switch ($property)
			{
				case User :: PROPERTY_LASTNAME :
					return $user->get_lastname();
				case User :: PROPERTY_FIRSTNAME :
					return $user->get_firstname();
				case User :: PROPERTY_EMAIL :
					return '<a href="mailto:' . $user->get_email() . '">' . $user->get_email() . '</a><br/>';
				case GradebookRelUser :: PROPERTY_SCORE :
					return $gradebookreluser->get_score();
			}

		}
		return '&nbsp;';
	}

	function render_id_cell($gradebookreluser){
		return $gradebookreluser->get_gradebook_id() . '|' . $gradebookreluser->get_user_id();
	}
}
?>