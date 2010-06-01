<?php

//require_once dirname ( __FILE__ ) . '/../survey_builder_component.class.php';
require_once dirname ( __FILE__ ) . '/browser/survey_browser_table_cell_renderer.class.php';

class SurveyBuilderBrowserComponent extends SurveyBuilder {
	
function run()
	{
		$browser = ComplexBuilderComponent ::factory(ComplexBuilderComponent::BROWSER_COMPONENT, $this);
		
		$browser->run();
	}
	
	function get_action_bar($content_object) {
				
		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );
		$action_bar->add_tool_action( new ToolbarItem ( Translation::get ( 'ConfigureSurveyContext' ), Theme::get_common_image_path () . 'action_build_prerequisites.png', $this->get_configure_context_url($content_object) ) );
		return $action_bar->as_html();
	}
	
    function get_complex_content_object_table_html($show_subitems_column = true, $model = null, $renderer = null)
    {
        return parent :: get_complex_content_object_table_html($show_subitems_column, $model, new SurveyBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition()));
    }
//	
//	function run() {
//		$menu_trail = $this->get_clo_breadcrumbs ();
//		$trail = new BreadcrumbTrail ( false );
//		$trail->merge ( $menu_trail );
//		$trail->add_help ( 'repository survey builder' );
//		
//		if ($this->get_cloi ()) {
//			$lo = RepositoryDataManager::get_instance ()->retrieve_content_object ( $this->get_cloi ()->get_ref () );
//		} else {
//			$lo = $this->get_root_lo ();
//		}
//		
//		$this->display_header ( $trail );
//		$action_bar = $this->get_action_bar ( $lo );
//		
//		echo '<br />';
//		echo $action_bar->as_html ();
//		
//		
//		echo '<br />';
//		$types = $lo->get_allowed_types ();
//		echo $this->get_creation_links ( $lo, $types );
//		echo '<div class="clear">&nbsp;</div><br />';
//		
//		echo '<div>';
//		echo $this->get_clo_table_html ( false, null, new SurveyBrowserTableCellRenderer ( $this->get_parent (), $this->get_clo_table_condition () ) );
//		echo '</div>';
//		echo '<div class="clear">&nbsp;</div>';
//		
//		$this->display_footer ();
//	}
//	
//	function get_action_bar($co) {
//				
//		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );
//		$action_bar->add_tool_action( new ToolbarItem ( Translation::get ( 'ConfigureSurveyContext' ), Theme::get_common_image_path () . 'action_build_prerequisites.png', $this->get_configure_context_url($co) ) );
//		return $action_bar;
//	}
//	
//	function get_creation_links($lo, $types = array()) {
//		$html [] = '<div class="select_complex_element">';
//		$html [] = '<span class="title">' . Theme::get_common_image ( 'place_content_objects' ) . Translation::get ( 'SurveyAddContentObject' ) . '</span>';
//		$html [] = '<div id="content_object_selection">';
//		
//		if (count ( $types ) == 0) {
//			$types = $lo->get_allowed_types ();
//		}
//		
//		foreach ( $types as $type ) {
//			
//			$url = $this->get_url ( array (ComplexBuilder::PARAM_BUILDER_ACTION => ComplexBuilder::ACTION_CREATE_CLOI, ComplexBuilder::PARAM_TYPE => $type, ComplexBuilder::PARAM_ROOT_LO => $this->get_root_lo ()->get_id (), ComplexBuilder::PARAM_CLOI_ID => ($this->get_cloi () ? $this->get_cloi ()->get_id () : null), 'publish' => Request::get ( 'publish' ) ) );
//			$html [] = '<a href="' . $url . '"><div class="create_block" style="background-image: url(' . Theme::get_common_image_path () . 'content_object/big/' . $type . '.png);">';
//			$html [] = Translation::get ( ContentObject::type_to_class ( $type ) . 'TypeName' );
//			$html [] = '<div class="clear">&nbsp;</div>';
//			$html [] = '</div></a>';
//		}
//		
//		$html [] = '<div class="clear">&nbsp;</div>';
//		$html [] = ResourceManager::get_instance ()->get_resource_html ( Path::get ( WEB_LIB_PATH ) . 'javascript/repository.js' );
//		$html [] = '</div>';
//		$html [] = '<div class="clear">&nbsp;</div>';
//		$html [] = '</div>';
//		
//		return implode ( "\n", $html );
//	}
}

?>