<?php

require_once dirname ( __FILE__ ) . '/question_browser_table_column_model.class.php';
require_once dirname ( __FILE__ ) . '/../../tables/page_question_table/default_page_question_table_cell_renderer.class.php';

class SurveyPageQuestionBrowserTableCellRenderer extends DefaultSurveyPageQuestionTableCellRenderer {
	/**
	 * The repository browser component
	 */
	private $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function SurveyPageQuestionBrowserTableCellRenderer($browser) {
		parent::__construct ();
		$this->browser = $browser;
	}
	
	// Inherited
	function render_cell($column, $complex_item) {
		
		if ($column === SurveyPageQuestionBrowserTableColumnModel::get_modification_column ()) {
			return $this->get_modification_links ( $complex_item );
		}
		
		return parent::render_cell ( $column, $complex_item );
	}
	
	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($complex_item) {
		$toolbar_data = array ();
		
		$toolbar_data [] = array ('href' => $this->browser->get_change_question_visibility_url ( $complex_item ), 'label' => Translation::get ( 'ToggleVisibility' ), 'img' => Theme::get_common_image_path () . 'action_visible.png' );
		
		if ($complex_item->get_visible () == 1) {
			$configure_url = $this->browser->get_configure_question_url ( $complex_item );
			$toolbar_data [] = array ('href' => $configure_url, 'label' => Translation::get ( 'Configure' ), 'img' => Theme::get_common_image_path () . 'action_build_prerequisites.png' );
		}
		
		return Utilities::build_toolbar ( $toolbar_data );
	}
	
	function render_id_cell($complex_item) {
		$id = $complex_item->get_id ();
		return $id;
	}

}
?>