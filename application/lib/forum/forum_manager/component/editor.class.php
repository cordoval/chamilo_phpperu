<?php
/**
 * $Id: editor.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */

class ForumManagerEditorComponent extends ForumManager
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $pid = Request :: get(ForumManager :: PARAM_PUBLICATION_ID);
            
            $datamanager = ForumDataManager :: get_instance();
            $publication = $datamanager->retrieve_forum_publication($pid);
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($publication->get_forum_id());
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_EDIT, ForumManager :: PARAM_PUBLICATION_ID => $pid)));
            
            $trail = new BreadcrumbTrail();
            
            $trail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_EDIT, ForumManager :: PARAM_PUBLICATION_ID => $pid)), Translation :: get('Edit')));
            $trail->add_help('forum general');
            
            if ($form->validate())
            {
                $succes = $form->update_content_object();
                
                if ($form->is_version())
                {
                    $old_id = $publication->get_forum_id();
                    $publication->set_forum_id($content_object->get_latest_version()->get_id());
                    $publication->update();
                    
                    RepositoryDataManager :: get_instance()->set_new_clo_version($old_id, $publication->get_forum_id());
                }
                
                $message = $succes ? Translation :: get('ForumUpdated') : Translation :: get('ForumNotUpdated');
                $this->redirect($message, ! $succes, array(ForumManager :: PARAM_ACTION => null));
            }
            else
            {
                $this->display_header($trail, true);
                $form->display();
                $this->display_footer();
            }
        }
    }
}
?>