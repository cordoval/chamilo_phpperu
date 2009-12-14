<?php
/**
 * $Id: sticky.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */
require_once dirname(__FILE__) . '/../forum_display_component.class.php';

class ForumDisplayStickyComponent extends ForumDisplayComponent
{

    function run()
    {
        $rdm = RepositoryDataManager :: get_instance();
        
        $topic = $rdm->retrieve_complex_content_object_item(Request :: get(ComplexDisplay :: PARAM_SELECTED_CLOI_ID));
        
        if ($topic->get_type() == 1)
        {
            $topic->set_type(null);
            $message = 'TopicUnStickied';
        }
        else
        {
            $topic->set_type(1);
            $message = 'TopicStickied';
        }
        $topic->update();
        
        $params = array('pid' => Request :: get('pid'));
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
            
        if ( Request :: get('is_subforum'))
        	$params['forum'] = Request :: get('forum');
        
        $this->redirect($message, '', $params);
    }
}

?>
