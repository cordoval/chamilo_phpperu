<?php


/**
 * @package application.context_linker.context_linker.component
 */
require_once dirname(__FILE__).'/../context_linker_manager.class.php';
require_once dirname(__FILE__).'/../../forms/context_link_form.class.php';
require_once dirname(__FILE__) . '/../../../metadata/metadata_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../metadata/metadata_manager/metadata_manager.class.php';

/**
 * Component to create a new context_link object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManagerContextLinkPublisherComponent extends ContextLinkerManager
{
    /**
     * Runs this component and displays its output.
     */

    private $content_object;
    

    function run()
    {
        $context_link = new ContextLink();
        $context_link->set_original_content_object_id(Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID));
        $context_link->set_alternative_content_object_id(Request :: get(ContextLinkerManager :: PARAM_ALTERNATIVE_CONTENT_OBJECT_ID));
        $context_link->set_date(time());

        $mdm = MetadataDataManager :: get_instance();

        $condition = new EqualityCondition(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID, Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID));
        $metadata_property_values = $mdm->retrieve_full_metadata_property_values($condition);

        $params = array();
        $params[ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID] = request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID);
        $params[ContextLinkerManager :: PARAM_ALTERNATIVE_CONTENT_OBJECT_ID] = Request ::get(ContextLinkerManager :: PARAM_ALTERNATIVE_CONTENT_OBJECT_ID);

        $form = new ContextLinkForm(ContextLinkForm :: TYPE_CREATE, $context_link, $metadata_property_values, $this->get_url($params), $this->get_user());

        if($form->validate())
        {
                $success = $form->create_context_link();
                $this->redirect($success ? Translation :: get('ContextLinkCreated') : Translation :: get('ContextLinkNotCreated'), !$success, array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTEXT_LINKS, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID)));
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