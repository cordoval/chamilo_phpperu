<?php

namespace application\alexia;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Application;
use repository\ContentObjectForm;
use common\libraries\Request;
/**
 * $Id: editor.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia.alexia_manager.component
 */
class AlexiaManagerEditorComponent extends AlexiaManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Alexia')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Edit' ,null ,Utilities::COMMON_LIBRARIES)));
        $trail->add_help('alexia general');
        
        $publication = Request :: get(AlexiaManager :: PARAM_ALEXIA_ID);
        
        if (isset($publication))
        {
            $alexia_publication = $this->retrieve_alexia_publication($publication);
            $content_object = $alexia_publication->get_publication_object();
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_EDIT_PUBLICATION, AlexiaManager :: PARAM_ALEXIA_ID => $publication)));
            
            if ($form->validate() || Request :: get('validated'))
            {
                if (! Request :: get('validated'))
                {
                    $form->update_content_object();
                }
                
                if ($form->is_version())
                {
                    $alexia_publication->set_content_object($content_object->get_latest_version()->get_id());
                    $alexia_publication->update();
                }
                
                $publication_form = new AlexiaPublicationForm(AlexiaPublicationForm :: TYPE_SINGLE, $content_object, $this->get_user(), $this->get_url(array(AlexiaManager :: PARAM_ALEXIA_ID => $publication, 'validated' => 1)));
                $publication_form->set_publication($alexia_publication);
                
                if ($publication_form->validate())
                {
                    $success = $publication_form->update_content_object_publication();
                    $message = ($success ? 'ContentObjectUpdated' : 'ContentObjectNotUpdated');
                    
                    $this->redirect(Translation :: get($message, null, Utilities::COMMON_LIBRARIES), ! $success, array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS), array(AlexiaManager :: PARAM_ALEXIA_ID));
                }
                else
                {
                    $this->display_header($trail, true);
                    $publication_form->display();
                    $this->display_footer();
                }
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