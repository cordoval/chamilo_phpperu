<?php

require_once dirname ( __FILE__ ) . '/survey_builder_component.class.php';

class SurveyBuilder extends ComplexBuilder {
	
	const ACTION_CREATE_SURVEY = 'create';
	
	const ACTION_CONFIGURE_CONTEXT = 'configure_context';
	const ACTION_BROWSE_CONTEXT = 'browse_context';
	const ACTION_VIEW_CONTEXT = 'view_context';
	const ACTION_SUBSCRIBE_PAGE_BROWSER = 'subscribe_browser';
	const ACTION_UNSUBSCRIBE_PAGE_FROM_TEMPLATE = 'unsubscribe_page_from_template';
	const ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE = 'subscribe_page_to_template';
	const ACTION_TRUNCATE_TEMPLATE = 'truncate_template';
	
	const PARAM_SURVEY_PAGE_ID = 'survey_page';
	const PARAM_SURVEY_ID = 'survey';
	const PARAM_TEMPLATE_ID = 'template_id';
	const PARAM_TEMPLATE_REL_PAGE_ID = 'template_rel_page_id';
	
	const PARAM_TRUNCATE_SELECTED = 'truncate';
	const PARAM_SUBSCRIBE_SELECTED = 'subscribe';
	const PARAM_UNSUBSCRIBE_SELECTED = 'unsubscribe_selected';
	
	function SurveyBuilder($parent) {
		parent::__construct ( $parent );
		$this->parse_input_from_table ();
	}
	
	function run() {
		$action = $this->get_action ();
		
		switch ($action) {
			case ComplexBuilder::ACTION_BROWSE_CLO :
				$component = SurveyBuilderComponent::factory ( 'Browser', $this );
				break;
			case SurveyBuilder::ACTION_CREATE_SURVEY :
				$component = SurveyBuilderComponent::factory ( 'Creator', $this );
				break;
			case SurveyBuilder::ACTION_CONFIGURE_CONTEXT :
				$component = SurveyBuilderComponent::factory ( 'ConfigureContext', $this );
				break;
			case SurveyBuilder::ACTION_BROWSE_CONTEXT :
				$component = SurveyBuilderComponent::factory ( 'ContextBrowser', $this );
				break;
			case SurveyBuilder::ACTION_VIEW_CONTEXT :
				$component = SurveyBuilderComponent::factory ( 'ContextViewer', $this );
				break;
			case SurveyBuilder::ACTION_SUBSCRIBE_PAGE_BROWSER :
				$component = SurveyBuilderComponent::factory ( 'ContextTemplateSubscribePageBrowser', $this );
				break;
			case SurveyBuilder::ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE :
				$component = SurveyBuilderComponent::factory ( 'PageSubscriber', $this );
				break;
		}
		
		if (! $component)
			parent::run ();
		else
			$component->run ();
	}
	
	function get_configure_context_url() {
		return $this->get_url ( array (self::PARAM_BUILDER_ACTION => self::ACTION_BROWSE_CONTEXT, self::PARAM_ROOT_LO => $this->get_root_lo ()->get_id (), self::PARAM_TEMPLATE_ID => $this->get_root_lo ()->get_context_template_id (), 'publish' => Request::get ( 'publish' ) ) );
	}
	
	function get_template_viewing_url($template_id) {
		return $this->get_url ( array (self::PARAM_BUILDER_ACTION => self::ACTION_VIEW_CONTEXT, self::PARAM_ROOT_LO => $this->get_root_lo ()->get_id (), self::PARAM_TEMPLATE_ID => $this->get_root_lo ()->get_context_template_id () ) );
	}
	
	function get_template_suscribe_page_browser_url($template_id) {
		return $this->get_url ( array (self::PARAM_BUILDER_ACTION => self::ACTION_SUBSCRIBE_PAGE_BROWSER, self::PARAM_ROOT_LO => $this->get_root_lo ()->get_id (), self::PARAM_TEMPLATE_ID => $this->get_root_lo ()->get_context_template_id () ) );
	}
	
	function get_template_suscribe_page_url($template_id, $page_id){
		return $this->get_url ( array (self::PARAM_BUILDER_ACTION => self::ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE, self::PARAM_ROOT_LO => $this->get_root_lo ()->get_id (), self::PARAM_TEMPLATE_ID => $template_id, self :: PARAM_SURVEY_PAGE_ID => $page_id ) );
	}
	
	function get_template_emptying_url($template_id) {
	
	}
	
	private function parse_input_from_table() {
		
				
		if (isset ( $_POST ['action'] )) {
						
			if (isset ( $_POST [SurveyContextTemplateRelPageBrowserTable::DEFAULT_NAME . ObjectTable::CHECKBOX_NAME_SUFFIX] )) {
				$selected_ids = $_POST [SurveyContextTemplateRelPageBrowserTable::DEFAULT_NAME . ObjectTable::CHECKBOX_NAME_SUFFIX];
			}
			
			if (isset ( $_POST [SurveyContextTemplateSubscribePageBrowserTable::DEFAULT_NAME . ObjectTable::CHECKBOX_NAME_SUFFIX] )) {
				$selected_ids = $_POST [SurveyContextTemplateSubscribePageBrowserTable::DEFAULT_NAME . ObjectTable::CHECKBOX_NAME_SUFFIX];
			}
			
			if (isset ( $_POST [SurveyContextTemplateBrowserTable::DEFAULT_NAME . ObjectTable::CHECKBOX_NAME_SUFFIX] )) {
				$selected_ids = $_POST [SurveyContextTemplateBrowserTable::DEFAULT_NAME . ObjectTable::CHECKBOX_NAME_SUFFIX];
			}
			
			if (empty ( $selected_ids )) {
				$selected_ids = array ();
			} elseif (! is_array ( $selected_ids )) {
				$selected_ids = array ($selected_ids );
			}
			
			switch ($_POST ['action']) {
				case self::PARAM_UNSUBSCRIBE_SELECTED :
					$this->set_action ( self::ACTION_UNSUBSCRIBE_PAGE_FROM_TEMPLATE );
					Request::set_get ( self::PARAM_TEMPLATE_REL_PAGE_ID, $selected_ids );
					break;
				case self::PARAM_SUBSCRIBE_SELECTED :
					$this->set_action ( self::ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE );
					$location_ids = array ();
					
					foreach ( $selected_ids as $selected_id ) {
						$ids = explode ( '|', $selected_id );
						$page_ids [] = $ids [1];
						$template_id = $ids [0];
					}
					
					Request::set_get ( self::PARAM_TEMPLATE_ID, $template_id );
					Request::set_get ( self::PARAM_SURVEY_PAGE_ID, $page_ids );
					break;
				case self::PARAM_TRUNCATE_SELECTED :
					$this->set_action ( self::ACTION_TRUNCATE_TEMPLATE );
					Request::set_get ( self::PARAM_TEMPLATE_ID, $selected_ids );
					break;
			}
		}
	
	}

}

?>