<?php
require_once dirname(__FILE__).'/gradebook_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/gradebook_table/default_gradebook_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../gradebook.class.php';
require_once dirname(__FILE__).'/../../../gradebook_rel_user.class.php';
require_once dirname(__FILE__).'/../../gradebook_manager.class.php';

class GradebookBrowserTableCellRenderer extends DefaultGradebookTableCellRenderer
{
	private $browser;
	
	function GradebookBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $gradebook)
	{
		if ($column === GradebookBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($gradebook);
		}
		
		// Add special features here
		switch ($column->get_name())
		{
			// Exceptions that need post-processing go here ...
			case Gradebook :: PROPERTY_NAME :
				$title = parent :: render_cell($column, $gradebook);
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_gradebook_viewing_url($gradebook)).'" title="'.$title.'">'.$title_short.'</a>';
			case Gradebook :: PROPERTY_DESCRIPTION :
				$description = strip_tags(parent :: render_cell($column, $gradebook));
				if(strlen($description) > 175)
				{
					$description = mb_substr($description,0,170).'&hellip;';
				}
				return $description;
			case 'Users' :
				$condition = new EqualityCondition(GradebookRelUser :: PROPERTY_GRADEBOOK_ID,$gradebook->get_id());
				$count = $this->browser->count_gradebook_rel_users($condition);
				return $count;
	
		}
		
		return parent :: render_cell($column, $gradebook);
	}
	
	private function get_modification_links($gradebook)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_gradebook_editing_url($gradebook),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_gradebook_subscribe_user_browser_url($gradebook),
			'label' => Translation :: get('AddUsers'),
			'img' => Theme :: get_common_image_path().'action_subscribe.png',
		);
		
		$condition = new EqualityCondition(GradebookRelUser :: PROPERTY_GRADEBOOK_ID, $gradebook->get_id());
		$users = $this->browser->retrieve_gradebook_rel_users($condition);
		$visible = ($users->size() > 0);
		
		if($visible)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_gradebook_emptying_url($gradebook),
				'label' => Translation :: get('Truncate'),
				'img' => Theme :: get_common_image_path().'action_recycle_bin.png',
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('TruncateNA'),
				'img' => Theme :: get_common_image_path().'action_recycle_bin_na.png',
			);
		}
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_gradebook_delete_url($gradebook),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png'
		);
		
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>