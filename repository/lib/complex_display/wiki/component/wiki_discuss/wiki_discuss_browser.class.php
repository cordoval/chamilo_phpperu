<?php
/**
 * $Id: wiki_discuss_browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component.wiki_discuss
 */

require_once dirname(__FILE__) . '/../../../../content_object_pub_feedback_browser.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/announcement/announcement.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/description/description.class.php';

/**
 * Browser to allow the user to view the published feedback on a wiki page
 */
class WikiDiscussBrowser extends ContentObjectPubFeedbackBrowser
{
    private $feedbacks;

    function WikiDiscussBrowser($parent)
    {
        parent :: __construct($parent, 'wiki');
        
        $renderer = new ListContentObjectPublicationListRenderer($this);
        $actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected'), Tool :: ACTION_HIDE => Translation :: get('Hide'), Tool :: ACTION_SHOW => Translation :: get('Show'));
        $renderer->set_actions($actions);
        
        $this->set_publication_list_renderer($renderer);

        function get_publications($from, $count, $column, $direction)
        {
            if (empty($this->feedbacks))
            {
                $datamanager = RepositoryDataManager :: get_instance();
                $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_PUBLICATION_ID, $this->publication_id);
                $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_CLOI_ID, $this->cid);
                if ($this->get_parent()->get_condition())
                    $conditions[] = $this->get_parent()->get_condition();
                $condition = new AndCondition($conditions);
                $feedbacks = $datamanager->retrieve_content_object_pub_feedback($condition);
                while ($feedback = $feedbacks->next_result())
                {
                    // If the feedback is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
                    if (! ($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
                    {
                        continue;
                    }
                    $visible_feedbacks[] = $feedback;
                }
                
                $this->feedbacks = $visible_feedbacks;
            }
            
            return $this->feedbacks;
        
        }

        function get_publication_count()
        {
            return count($this->get_publications());
        }
    }
}
?>