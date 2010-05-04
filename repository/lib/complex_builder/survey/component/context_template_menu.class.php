<?php

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path::get_repository_path () . '/lib/content_object/survey/survey_context_template.class.php';

class SurveyContextTemplateMenu extends HTML_Menu {
	const TREE_NAME = __CLASS__;
	
	/**
	 * The string passed to sprintf() to format category URLs
	 */
	private $urlFmt;
	
	private $root_co;
	
	/**
	 * The array renderer used to determine the breadcrumbs.
	 */
	private $array_renderer;
	
	private $include_root;
	
	private $current_template;
	
	private $show_complete_tree;
	
	private $hide_current_template;
	
	/**
	 * Creates a new category navigation menu.
	 * @param int $owner The ID of the owner of the categories to provide in
	 * this menu.
	 * @param int $current_category The ID of the current category in the menu.
	 * @param string $url_format The format to use for the URL of a category.
	 * Passed to sprintf(). Defaults to the string
	 * "?category=%s".
	 * @param array $extra_items An array of extra tree items, added to the
	 * root.
	 */
	function SurveyContextTemplateMenu($current_template, $root_co, $url_format = '?go=build_complex&application=repository&builder_action=browse_context&root_lo=%s&template_id=%s', $include_root = true, $show_complete_tree = false, $hide_current_template = false) {
		$this->root_co = $root_co;
		$this->include_root = $include_root;
		$this->show_complete_tree = $show_complete_tree;
		$this->hide_current_template = $hide_current_template;
		
//		if ($current_template == '0' || is_null ( $current_template )) {
//			$survey = RepositoryDataManager::get_instance ()->retrieve_content_object ( $this->root_co );
//			$template_id = $survey->get_context_template_id ();
//			$this->current_template = SurveyContextDataManager::get_instance ()->retrieve_survey_context_template ( $template_id );
//		} else {

			$this->current_template = SurveyContextDataManager::get_instance ()->retrieve_survey_context_template ( $current_template );
//		}
		
		$this->urlFmt = $url_format;
		$menu = $this->get_menu ();
		parent::__construct ( $menu );
		$this->array_renderer = new HTML_Menu_ArrayRenderer ();
		$this->forceCurrentUrl ( $this->get_url ($this->current_template->get_id () ) );
	}
	
	function get_menu() {
		$include_root = $this->include_root;
		$survey = RepositoryDataManager::get_instance ()->retrieve_content_object ( $this->root_co );
		$template = $survey->get_context_template_id();
				
		if (! $include_root) {
			return $this->get_menu_items ( $template );
		} else {
			$menu = array ();
			
			$menu_item = array ();
			$menu_item ['title'] = $survey->get_context_template_name ();
			$menu_item ['url'] = $this->get_home_url (  $template );
			
			$sub_menu_items = $this->get_menu_items (  $template );
			if (count ( $sub_menu_items ) > 0) {
				$menu_item ['sub'] = $sub_menu_items;
			}
			
			$menu_item ['class'] = 'home';
			$menu_item [OptionsMenuRenderer::KEY_ID] =  $template;
			$menu [ $template] = $menu_item;
			return $menu;
		}
	}
	
	/**
	 * Returns the menu items.
	 * @param array $extra_items An array of extra tree items, added to the
	 * root.
	 * @return array An array with all menu items. The structure of this array
	 * is the structure needed by PEAR::HTML_Menu, on which this
	 * class is based.
	 */
	private function get_menu_items($parent_id = 0) {
		$current_template = $this->current_template;
		
		$show_complete_tree = $this->show_complete_tree;
		$hide_current_template = $this->hide_current_template;
		
		$condition = new EqualityCondition ( SurveyContextTemplate::PROPERTY_PARENT_ID, $parent_id );
		$templates = SurveyContextDataManager::get_instance ()->retrieve_survey_context_templates ( $condition, null, null, new ObjectTableOrder ( SurveyContextTemplate::PROPERTY_CONTEXT_TYPE ) );
		
		while ( $template = $templates->next_result () ) {
			$template_id = $template->get_id ();
			
			if (! ($template_id == $current_template->get_id () && $hide_current_template)) {
				$menu_item = array ();
				$menu_item ['title'] = $template->get_name ();
				$menu_item ['url'] = $this->get_url ( $template_id);
				
				if ($template->is_parent_of ( $current_template ) || $template->get_id () == $current_template->get_id () || $show_complete_tree) {
					if ($template->has_children ()) {
						$menu_item ['sub'] = $this->get_menu_items (  $template_id );
					}
				} else {
					if ($template->has_children ()) {
						$menu_item ['children'] = 'expand';
					}
				}
				
				$menu_item ['class'] = 'category';
				$menu_item [OptionsMenuRenderer::KEY_ID] =  $template_id;
				$menu [ $template_id] = $menu_item;
			}
		}
		
		return $menu;
	}
	
	/**
	 * Gets the URL of a given category
	 * @param int $category The id of the category
	 * @return string The requested URL
	 */
	function get_url($template) {
		// TODO: Put another class in charge of the htmlentities() invocation
		return htmlentities ( sprintf ( $this->urlFmt, $this->root_co, $template ) );
	}
	
	private function get_home_url($template) {
		// TODO: Put another class in charge of the htmlentities() invocation
		//        $url = str_replace('&template_id=%s', '', $this->urlFmt);
		return htmlentities ( sprintf ( $this->urlFmt, $this->root_co, $template ) );
	}
	
	/**
	 * Get the breadcrumbs which lead to the current category.
	 * @return array The breadcrumbs.
	 */
	function get_breadcrumbs() {
		$this->render ( $this->array_renderer, 'urhere' );
		$breadcrumbs = $this->array_renderer->toArray ();
		foreach ( $breadcrumbs as $crumb ) {
			$crumb ['name'] = $crumb ['title'];
			unset ( $crumb ['title'] );
		}
		return $breadcrumbs;
	}
	
	/**
	 * Renders the menu as a tree
	 * @return string The HTML formatted tree
	 */
	function render_as_tree() {
		$renderer = new TreeMenuRenderer ( $this->get_tree_name () );
		$this->render ( $renderer, 'sitemap' );
		return $renderer->toHTML ();
	}
	
	static function get_tree_name() {
		return Utilities::camelcase_to_underscores ( self::TREE_NAME );
	}
}