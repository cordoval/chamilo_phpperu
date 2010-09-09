<?php

class DefaultSurveyUserTableCellRenderer extends ObjectTableCellRenderer
{

	private $publication_id;
	
	function DefaultSurveyUserTableCellRenderer($publication_id)
	{
		$this->publication_id = $publication_id;
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
		return $publication_id.'|'.$user->get_id();
	}
	
}
?>