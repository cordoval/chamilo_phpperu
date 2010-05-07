<?php

class DefaultTestCaseManagerUserTableCellRenderer implements ObjectTableCellRenderer
{
	
	function DefaultTestCaseManagerUserTableCellRenderer()
	{
	}
	
	function render_cell($column, $user)
	{
		
			switch ($column->get_name())
			{
				case User :: PROPERTY_LASTNAME :
					return $user->get_lastname();
				case User :: PROPERTY_FIRSTNAME :
					return $user->get_firstname();
				case User :: PROPERTY_USERNAME :
					return $user->get_username();
				case User :: PROPERTY_EMAIL :
					return '<a href="mailto:' . $user->get_email() . '">' . $user->get_email() . '</a><br/>';
			}
		
	}
	
	function render_id_cell($user){
		return $user->get_id();
	}
	
}
?>