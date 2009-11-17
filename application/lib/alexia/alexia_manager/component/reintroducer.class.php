<?php
/**
 * $Id: reintroducer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia.alexia_manager.component
 */
require_once dirname(__FILE__) . '/../alexia_manager.class.php';
require_once dirname(__FILE__) . '/../alexia_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/alexia_publication_form.class.php';

class AlexiaManagerReintroducerComponent extends AlexiaManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Alexia')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('IntroductionEdit')));
        $trail->add_help('alexia general');
        
        $publication = Request :: get(AlexiaManager :: PARAM_ALEXIA_ID);
        
        if (isset($publication))
        {
            $alexia_publication = $this->retrieve_alexia_publication($publication);
            $content_object = $alexia_publication->get_publication_object();
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_EDIT_INTRODUCTION, AlexiaManager :: PARAM_ALEXIA_ID => $publication)));
            
            if ($form->validate())
            {
                $success = $form->update_content_object();
                
                if ($form->is_version())
                {
                    $alexia_publication->set_content_object($content_object->get_latest_version());
                    $alexia_publication->update();
                }
                
                $message = ($success ? 'ContentObjectUpdated' : 'ContentObjectNotUpdated');
                
                $this->redirect(Translation :: get($message), ! $success, array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS), array(AlexiaManager :: PARAM_ALEXIA_ID));
            }
            else
            {
                $this->display_header($trail, true);
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
        }
    }
}
?>