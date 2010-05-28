<?php
/**
 * $Id: important.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */

require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayImportantComponent extends ForumDisplay
{

    function run()
    {
        $rdm = RepositoryDataManager :: get_instance();
        
        $topic = $rdm->retrieve_complex_content_object_item(Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID));
        
        if ($topic->get_type() == 2)
        {
            $topic->set_type(null);
            $message = 'TopicUnImortant';
        }
        else
        {
            $topic->set_type(2);
            $message = 'TopicImportant';
        }
        $topic->update();
        
        $params = array('pid' => Request :: get('pid'));
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
            
        if ( Request :: get('is_subforum'))
        	$params['forum'] = Request :: get('forum');
        
        $this->redirect(Translation :: get($message), '', $params);
    }
}

?>