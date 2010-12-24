<?php
namespace repository\content_object\survey;

use repository\ContentObject;

use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\DelegateComponent;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicActionsTab;
use common\libraries\Utilities;
use common\libraries\DynamicAction;
use repository\RepositoryManager;

class SurveyContextManagerManagerChooserComponent extends SurveyContextManager implements DelegateComponent
{
    
    const TAB_CONTEXT = 0;
    const TAB_CONTEXT_TEMPLATE = 1;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->display_header();
        echo $this->get_context_manager_tabs($links);
        $this->display_footer();
    }

    /**
     * Returns an HTML representation of the actions.
     * @return string $html HTML representation of the actions.
     */
    function get_context_manager_tabs($links)
    {
        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $context_manager_tabs = new DynamicTabsRenderer($renderer_name);
        
        $actions = $this->get_actions_for_tab(self :: TAB_CONTEXT);
        $context_manager_tabs->add_tab(new DynamicActionsTab(self :: TAB_CONTEXT, Translation :: get('ContextTab'), Theme :: get_image_path(ContentObject :: get_content_object_type_namespace(Survey :: get_type_name())) . 'tab_place_holder.png', $actions));
        
        $actions = $this->get_actions_for_tab(self :: TAB_CONTEXT_TEMPLATE);
        $context_manager_tabs->add_tab(new DynamicActionsTab(self :: TAB_CONTEXT_TEMPLATE, Translation :: get('ContextTemplateTab'), Theme :: get_image_path(ContentObject :: get_content_object_type_namespace(Survey :: get_type_name())) . 'tab_place_holder.png', $actions));
                
        return $context_manager_tabs->render();
    }

    function get_actions_for_tab($index)
    {
        $actions = array();
        
        switch ($index)
        {
            case self :: TAB_CONTEXT :
                $registration_link = new DynamicAction();
                $registration_link->set_title(Translation :: get('SurveyContextRegistrationLink'));
                $registration_link->set_description(Translation :: get('SurveyContextRegistrationDescription'));
                $registration_link->set_image(Theme :: get_image_path(ContentObject :: get_content_object_type_namespace(Survey :: get_type_name())) . 'action_place_holder.png');
                $registration_link->set_url($this->get_context_registration_browsing_url());
                $actions[] = $registration_link;
                break;
            
            case self :: TAB_CONTEXT_TEMPLATE :
                $template_link = new DynamicAction();
                $template_link->set_title(Translation :: get('SurveyContextTemplateLink'));
                $template_link->set_description(Translation :: get('SurveyContextTemplateDescription'));
                $template_link->set_image(Theme :: get_image_path(ContentObject :: get_content_object_type_namespace(Survey :: get_type_name())) . 'action_place_holder.png');
                $template_link->set_url($this->get_context_template_browsing_url());
                $actions[] = $template_link;
                break;
            
            default :
                
                break;
        }
        
        return $actions;
    }
}
?>