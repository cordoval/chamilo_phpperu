<?php
/**
 * @package application.context_linker.context_linker.component
 */
require_once dirname(__FILE__).'/../context_linker_manager.class.php';
require_once dirname(__FILE__).'/../../forms/context_link_form.class.php';

/**
 * Component to edit an existing context_link object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManagerContextLinkUpdaterComponent extends ContextLinkerManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $context_link = $this->retrieve_context_link(Request :: get(ContextLinkerManager :: PARAM_CONTEXT_LINK));

        $mdm = MetadataDataManager :: get_instance();

        $params = array();
        $params[ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID] = $context_link->get_original_content_object_id();
        $params[ContextLinkerManager :: PARAM_ALTERNATIVE_CONTENT_OBJECT_ID] = $context_link->get_alternative_content_object_id();

        $condition = new EqualityCondition(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID, $context_link->get_original_content_object_id());
        $metadata_property_values = $mdm->retrieve_full_metadata_property_values($condition);

        $form = new ContextLinkForm(ContextLinkForm :: TYPE_EDIT, $context_link, $metadata_property_values, $this->get_url(array_merge($params, array(ContextLinkerManager :: PARAM_CONTEXT_LINK => $context_link->get_id())), $this->get_user()));

        if($form->validate())
        {
                $success = $form->update_context_link();
                $this->redirect($success ? Translation :: get('ContextLinkUpdated') : Translation :: get('ContextLinkNotUpdated'), !$success, array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTEXT_LINKS, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => $context_link->get_original_content_object_id()));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
}
?>