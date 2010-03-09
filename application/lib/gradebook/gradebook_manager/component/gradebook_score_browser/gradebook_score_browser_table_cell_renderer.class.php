<?php

require_once dirname(__FILE__).'/gradebook_score_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/gradebook_score_table/default_gradebook_score_table_cell_renderer.class.php';

class GradebookScoreBrowserTableCellRenderer extends DefaultGradebookScoreTableCellRenderer
{
	
	private $browser;
	
	function GradebookScoreBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $gradebookreluser)
	{
		
//		$docent_id = $gradebookreluser->get_owner_id();
//		$docent = UserDataManager ::get_instance()->retrieve_user($docent_id);
//		
		
		if ($column === GradebookScoreBrowserTableColumnModel :: get_modification_column())
		{
			//return $this->get_modification_links($gradebookreluser);
		}
		
		if ($property = $column->get_name())
		{
			switch ($property)
			{
//				case 'docent' :
//					return '<a href="mailto:' . $docent->get_email() . '">' . $docent->get_email() . '</a><br/>';
				case Gradebook :: PROPERTY_DESCRIPTION :
					$description = strip_tags(parent :: render_cell($column, $gradebookreluser));
				if(strlen($description) > 175)
				{
					$description = mb_substr($description,0,170).'&hellip;';
				}
				return $description;
			
			}
		}
				
		return parent :: render_cell($column, $gradebookreluser);
	}
	
	private function get_modification_links($gradebookreluser)
	{
//		$toolbar_data = array();
//		
//		$toolbar_data[] = array(
//			'href' => $this->browser->get_gradebook_rel_user_unsubscribing_url($gradebookreluser),
//			'label' => Translation :: get('Unsubscribe'),
//			'img' => Theme :: get_common_image_path().'action_delete.png'
//		);
//		
//		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>