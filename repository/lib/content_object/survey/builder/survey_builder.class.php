<?php

//require_once dirname ( __FILE__ ) . '/survey_builder_component.class.php';


require_once dirname(__FILE__) . '/component/context_template_browser/browser_table.class.php';
require_once dirname(__FILE__) . '/component/context_template_rel_page_browser/rel_page_browser_table.class.php';
require_once dirname(__FILE__) . '/component/context_template_subscribe_page_browser/subscribe_page_browser_table.class.php';

class SurveyBuilder extends ComplexBuilder implements ComplexMenuSupport
{

    const ACTION_CREATE_SURVEY = 'create';

    const ACTION_CONFIGURE_CONTEXT = 'configure_context';
    const ACTION_BROWSE_CONTEXT = 'browse_context';
    const ACTION_VIEW_CONTEXT = 'view_context';
    const ACTION_SUBSCRIBE_PAGE_BROWSER = 'subscribe_browser';
    const ACTION_UNSUBSCRIBE_PAGE_FROM_TEMPLATE = 'unsubscribe_page_from_template';
    const ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE = 'subscribe_page_to_template';
    const ACTION_TRUNCATE_TEMPLATE = 'truncate_template';
    const ACTION_CONFIGURE_PAGE = 'configure_page';
    const ACTION_CHANGE_QUESTION_VISIBILITY = 'change_question_visibility';
    const ACTION_CONFIGURE_QUESTION = 'configure_question';

    const PARAM_SURVEY_PAGE_ID = 'survey_page';
    const PARAM_SURVEY_ID = 'survey';
    const PARAM_TEMPLATE_ID = 'template_id';
    const PARAM_TEMPLATE_REL_PAGE_ID = 'template_rel_page_id';
    const PARAM_COMPLEX_QUESTION_ITEM = 'complex_question_item';

    const PARAM_TRUNCATE_SELECTED = 'truncate';
    const PARAM_SUBSCRIBE_SELECTED = 'subscribe';
    const PARAM_UNSUBSCRIBE_SELECTED = 'unsubscribe_selected';

    function SurveyBuilder($parent)
    {
        parent :: __construct($parent);
        $this->parse_input_from_survey_table();
    }

    function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Creator');
                break;
            case ComplexBuilder :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Viewer');
                break;
            case ComplexBuilder :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Updater');
                break;
            case ComplexBuilder :: ACTION_MOVE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Mover');
                break;
            case ComplexBuilder :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = $this->create_component('Deleter');
                break;
            case SurveyBuilder :: ACTION_CONFIGURE_CONTEXT :
                $component = $this->create_component('ConfigureContext');
                break;
            case SurveyBuilder :: ACTION_BROWSE_CONTEXT :
                $component = $this->create_component('ContextBrowser');
                break;
            case SurveyBuilder :: ACTION_VIEW_CONTEXT :
                $component = $this->create_component('ContextViewer');
                break;
            case SurveyBuilder :: ACTION_SUBSCRIBE_PAGE_BROWSER :
                $component = $this->create_component('ContextTemplateSubscribePageBrowser');
                break;
            case SurveyBuilder :: ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE :
                $component = $this->create_component('PageSubscriber');
                break;
            case SurveyBuilder :: ACTION_UNSUBSCRIBE_PAGE_FROM_TEMPLATE :
                $component = $this->create_component('PageUnsubscriber');
                break;
            case SurveyBuilder :: ACTION_TRUNCATE_TEMPLATE :
                $component = $this->create_component('ContextTemplateTruncater');
                break;
            case SurveyBuilder :: ACTION_CONFIGURE_PAGE :
                $component = $this->create_component('Configure');
                break;
            case self :: ACTION_CHANGE_QUESTION_VISIBILITY :
                $component = $this->create_component('VisibilityChanger');
                break;
            case self :: ACTION_CONFIGURE_QUESTION :
                $component = $this->create_component('ConfigureQuestion');
                break;
            default :
                $this->set_action(ComplexBuilder :: ACTION_BROWSE);
                $component = $this->create_component('Browser');
        }
        $component->run();
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function show_menu()
    {
        return false;
    }

    function get_action_bar($content_object)
    {

        return "";
    }

    function get_configure_url($selected_cloi)
    {
        return $this->get_url(array(
                self :: PARAM_BUILDER_ACTION => self :: ACTION_CONFIGURE_PAGE, self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(), self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_cloi,
                self :: PARAM_SURVEY_PAGE_ID => $selected_cloi->get_ref()));
    }

    function get_configure_context_url()
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE_CONTEXT, self :: PARAM_TEMPLATE_ID => $this->get_root_content_object()->get_context_template_id()));
    }

    function get_template_viewing_url($template_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_VIEW_CONTEXT, self :: PARAM_TEMPLATE_ID => $template_id));
    }

    function get_template_suscribe_page_browser_url($template_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_SUBSCRIBE_PAGE_BROWSER, self :: PARAM_TEMPLATE_ID => $template_id));
    }

    function get_template_suscribe_page_url($template_id, $page_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE, self :: PARAM_TEMPLATE_ID => $template_id, self :: PARAM_SURVEY_PAGE_ID => $page_id));
    }

    function get_template_unsubscribing_page_url($template_rel_page)
    {
        $id = $template_rel_page->get_survey_id() . '|' . $template_rel_page->get_template_id() . '|' . $template_rel_page->get_page_id();
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_UNSUBSCRIBE_PAGE_FROM_TEMPLATE, self :: PARAM_TEMPLATE_REL_PAGE_ID => $id));
    }

    function get_template_emptying_url($template_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_TRUNCATE_TEMPLATE, self :: PARAM_TEMPLATE_ID => $template_id));
    }

    function get_change_question_visibility_url($complex_question_item)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CHANGE_QUESTION_VISIBILITY, self :: PARAM_COMPLEX_QUESTION_ITEM => $complex_question_item->get_id()));
    }

    function get_configure_question_url($complex_question_item)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CONFIGURE_QUESTION, self :: PARAM_COMPLEX_QUESTION_ITEM => $complex_question_item->get_id()));
    }

    private function parse_input_from_survey_table()
    {
        $action = Request :: post('action');
        if (isset($action))
        {

            $template_rel_page = Request :: post(SurveyContextTemplateRelPageBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX);
            if (isset($template_rel_page))
            {
                $selected_ids = Request :: post(SurveyContextTemplateRelPageBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX);
            }
            $template_subscribe_page = Request :: post(SurveyContextTemplateSubscribePageBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX);

            if (isset($template_subscribe_page))
            {
                $selected_ids = Request :: post(SurveyContextTemplateSubscribePageBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX);
            }

            $template = Request :: post(SurveyContextTemplateBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX);
            if (isset($template))
            {
                $selected_ids = Request :: post(SurveyContextTemplateBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX);
            }

            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }

            switch ($action)
            {
                case self :: PARAM_UNSUBSCRIBE_SELECTED :
                    $this->set_action(self :: ACTION_UNSUBSCRIBE_PAGE_FROM_TEMPLATE);
                    Request :: set_get(self :: PARAM_TEMPLATE_REL_PAGE_ID, $selected_ids);
                    break;
                case self :: PARAM_SUBSCRIBE_SELECTED :
                    $this->set_action(self :: ACTION_SUBSCRIBE_PAGE_TO_TEMPLATE);
                    $location_ids = array();

                    foreach ($selected_ids as $selected_id)
                    {
                        $ids = explode('|', $selected_id);
                        $page_ids[] = $ids[1];
                        $template_id = $ids[0];
                    }

                    Request :: set_get(self :: PARAM_TEMPLATE_ID, $template_id);
                    Request :: set_get(self :: PARAM_SURVEY_PAGE_ID, $page_ids);
                    break;
                case self :: PARAM_TRUNCATE_SELECTED :
                    $this->set_action(self :: ACTION_TRUNCATE_TEMPLATE);
                    Request :: set_get(self :: PARAM_TEMPLATE_ID, $selected_ids);
                    break;
            }
        }

    }

}

?>