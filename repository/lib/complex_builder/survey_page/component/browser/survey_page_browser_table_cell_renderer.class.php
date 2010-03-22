<?php

require_once Path::get_repository_path () . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SurveyPageBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer {
	private $count;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function SurveyPageBrowserTableCellRenderer($browser, $condition) {
		$this->count = RepositoryDataManager::get_instance ()->count_complex_content_object_items ( $condition );
		parent::__construct ( $browser, $condition );
	}
	
	private $lpi_ref_object;
	
	// Inherited
	function render_cell($column, $cloi) {
		$lo = $this->retrieve_content_object ( $cloi->get_ref () );
		$ref_lo = $lo;
		
		if ($column === ComplexBrowserTableColumnModel::get_modification_column ()) {
			return $this->get_modification_links ( $cloi, $ref_lo );
		}
		
		switch ($column->get_name ()) {
			case Translation::get ( Utilities::underscores_to_camelcase ( ContentObject::PROPERTY_TITLE ) ) :
				$title = htmlspecialchars ( $lo->get_title () );
				$title_short = $title;
				
				$title_short = Utilities::truncate_string ( $title_short, 53, false );
				//$title_short = '<a href="' . $this->browser->get_url ( array (ComplexBuilder::PARAM_ROOT_LO => $this->browser->get_root (), ComplexBuilder::PARAM_CLOI_ID => $cloi->get_id (), 'publish' => Request::get ( 'publish' ) ) ) . '">' . $title_short . '</a>';
				//TO DO
				//needs a link to the survey_question viewer not the builder
				return $title_short;
		}
		
		return parent::render_cell ( $column, $cloi, $lo );
	}
	
	protected function get_modification_links($cloi, $lo) {
		$additional_items = array ();
		$parent = RepositoryDataManager::get_instance ()->retrieve_content_object ( $cloi->get_parent () );
	
		$toolbar_data = array ();
		
		$edit_url = $this->browser->get_complex_content_object_item_edit_url ( $cloi, $this->browser->get_root () );
		if ($cloi->is_extended () || get_parent_class ( $this->browser ) == 'ComplexBuilder') {
			$toolbar_data [] = array ('href' => $edit_url, 'label' => Translation::get ( 'Edit' ), 'img' => Theme::get_common_image_path () . 'action_edit.png' );
		} else {
			$toolbar_data [] = array ('label' => Translation::get ( 'EditNA' ), 'img' => Theme::get_common_image_path () . 'action_edit_na.png' );
		}
		
		$delete_url = $this->browser->get_complex_content_object_item_delete_url ( $cloi, $this->browser->get_root () );
		$moveup_url = $this->browser->get_complex_content_object_item_move_url ( $cloi, $this->browser->get_root (), RepositoryManager::PARAM_DIRECTION_UP );
		$movedown_url = $this->browser->get_complex_content_object_item_move_url ( $cloi, $this->browser->get_root (), RepositoryManager::PARAM_DIRECTION_DOWN );
		
		$toolbar_data [] = array ('href' => $delete_url, 'label' => Translation::get ( 'Delete' ), 'img' => Theme::get_common_image_path () . 'action_delete.png', 'confirm' => true );
		
		$allowed = $this->check_move_allowed ( $cloi );
		
		if ($allowed ["moveup"]) {
			$toolbar_data [] = array ('href' => $moveup_url, 'label' => Translation::get ( 'MoveUp' ), 'img' => Theme::get_common_image_path () . 'action_up.png' );
		} else {
			$toolbar_data [] = array ('label' => Translation::get ( 'MoveUpNA' ), 'img' => Theme::get_common_image_path () . 'action_up_na.png' );
		
		}
		
		if ($allowed ["movedown"]) {
			$toolbar_data [] = array ('href' => $movedown_url, 'label' => Translation::get ( 'MoveDown' ), 'img' => Theme::get_common_image_path () . 'action_down.png' );
		} else {
			$toolbar_data [] = array ('label' => Translation::get ( 'MoveDownNA' ), 'img' => Theme::get_common_image_path () . 'action_down_na.png' );
		}
		$toolbar_data = array_merge ( $toolbar_data, $additional_items );
		
		return Utilities::build_toolbar ( $toolbar_data );
	
	}
}
?>