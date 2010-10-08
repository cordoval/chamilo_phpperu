<?php

/**
 * @package application.context_linker.context_linker.component
 */
require_once dirname(__FILE__) . '/../context_linker_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/confirmation_form.class.php';

/**
 * Component to delete context_links objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManagerContextLinkDeleterComponent extends ContextLinkerManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = $_GET[ContextLinkerManager :: PARAM_CONTEXT_LINK];
        
        if(!$context_link = $this->retrieve_context_link($id))
        {
            die(Translation :: get('NoContextLinkId'));
        }

        $form = new ConfirmationForm($this->get_url(array(ContextLinkerManager :: PARAM_CONTEXT_LINK => Request :: get(ContextLinkerManager :: PARAM_CONTEXT_LINK))));

        $form->build_confirmation_form();

        if($form->validate())
        {
            $values = $form->exportValues();
            if($values['yes'])
            {
                $rdm = RepositoryDataManager :: get_instance();
                $content_object =  $rdm->retrieve_content_object($context_link->get_alternative_content_object_id());
                $co_id = $content_object->get_id();

                if($content_object->delete())
                {
                    $message = 'ContentObjectDeleted';

                    //delete other context_links
                    //TODO : has to be integrated in content object dataClass later on

                    $condition1 = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $co_id);
                    $condition2 = new EqualityCondition(ContextLink :: PROPERTY_ALTERNATIVE_CONTENT_OBJECT_ID, $co_id);
                    $condition = new OrCondition($condition1, $condition2);

                    $related_context_links = $this->retrieve_context_links($condition);

                    while($dependent_context_link = $related_context_links->next_result())
                    {
                        $dependent_context_link->delete();
                    }
                }
                else
                {
                    $message = 'ContentObjectNotDeleted';
                }
            }

            if (!$context_link->delete())
            {
                $message .= 'ContextLinkNotDeleted';
                $fail = true;
            }
            else
            {
                $message .= 'ContextLinkDeleted';
            }

            $this->redirect(Translation :: get($message), ($fail ? true : false), array(ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTEXT_LINKS, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => $context_link->get_original_content_object_id()));
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