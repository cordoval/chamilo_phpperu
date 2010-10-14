<?php
namespace repository\content_object\survey_page;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\ResourceManager;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
require_once dirname ( __FILE__ ) . '/browser/survey_page_browser_table_cell_renderer.class.php';

class SurveyPageBuilderBrowserComponent extends SurveyPageBuilder {

	function run()
	{


		$browser = ComplexBuilderComponent ::factory(ComplexBuilderComponent::BROWSER_COMPONENT, $this);

		$browser->run();
	}

//	function run() {
//		$menu_trail = $this->get_content_object_breadcrumbs ();
//		$trail = BreadcrumbTrail :: get_instance ( false );
//		$trail->merge ( $menu_trail );
//		$trail->add_help ( 'repository survey_page builder' );
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
//		if ($action_bar) {
//			echo '<br />';
//			echo $action_bar->as_html ();
//		}
//
//		echo '<br />';
//		$types = $lo->get_allowed_types ();
//		echo $this->get_creation_links ( $lo, $types );
//		echo '<div class="clear">&nbsp;</div><br />';
//
//		echo '<div>';
//		echo $this->get_clo_table_html ( false, null, new SurveyPageBrowserTableCellRenderer ( $this->get_parent (), $this->get_clo_table_condition () ) );
//		echo '</div>';
//		echo '<div class="clear">&nbsp;</div>';
//
//		$this->display_footer ();
//	}
//
//	function get_action_bar() {
//		$pub = Request::get ( 'publish' );
//
//		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );
//
//		if ($pub && $pub != '') {
//			$action_bar->add_common_action ( new ToolbarItem ( Translation::get ( 'Publish' ), Theme::get_common_image_path () . 'action_publish.png', $_SESSION ['redirect_url'] ) );
//			return $action_bar;
//		}
//	}
//
//	function get_creation_links($lo, $types = array()) {
//		$html [] = '<div class="select_complex_element">';
//		$html [] = '<span class="title">' . Theme::get_common_image ( 'place_content_objects' ) . Translation::get ( 'SurveyPageAddContentObject' ) . '</span>';
//		$html [] = '<div id="content_object_selection">';
//
//		if (count ( $types ) == 0) {
//			$types = $lo->get_allowed_types ();
//		}
//
//		foreach ( $types as $type ) {
//
//			$url = $this->get_url ( array (ComplexBuilder::PARAM_BUILDER_ACTION => ComplexBuilder::ACTION_CREATE_CLOI, ComplexBuilder::PARAM_TYPE => $type, ComplexBuilder::PARAM_ROOT_LO => $this->get_root_lo ()->get_id (), ComplexBuilder::PARAM_CLOI_ID => ($this->get_cloi () ? $this->get_cloi ()->get_id () : null), 'publish' => Request::get ( 'publish' ) ) );
//
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