<?php
/**
 * $Id: wiki_pub_feedback_deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */

class WikiDisplayWikiPubFeedbackDeleterComponent extends WikiDisplayComponent
{
    private $cid;
    private $wiki_publication_id;

    function run()
    {
        if ($this->is_allowed(DELETE_RIGHT))
        {
            if (Request :: get('fid'))
                $feedback_ids = Request :: get('fid');
            else
                $feedback_ids = $_POST['fid'];
            
            if (Request :: get('selected_cloi'))
                $this->cid = Request :: get('selected_cloi');
            else
                $this->cid = $_POST['selected_cloi'];
            
            if (Request :: get('wiki_publication'))
                $this->wiki_publication_id = Request :: get('wiki_publication');
            else
                $this->wiki_publication_id = $_POST['wiki_publication'];
            
            if (! is_array($feedback_ids))
            {
                $feedback_ids = array($feedback_ids);
            }
            
            $datamanager = WikiDataManager :: get_instance();
            
            foreach ($feedback_ids as $index => $fid)
            {
                $condition = new EqualityCondition(WikiPubFeedback :: PROPERTY_FEEDBACK_ID, $fid);
                $feedbacks = $datamanager->retrieve_wiki_pub_feedbacks($condition);
                while ($feedback = $feedbacks->next_result())
                {
                    $feedback->delete();
                }
            }
            if (count($feedback_ids) > 1)
            {
                $message = htmlentities(Translation :: get('ContentObjectFeedbacksDeleted'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ContentObjectFeedbackDeleted'));
            }
            
            $this->redirect($message, '', array(Tool :: PARAM_ACTION => Request :: get('tool_action'), 'display_action' => 'discuss', 'wiki_publication' => $this->wiki_publication_id, 'selected_cloi' => $this->cid));
        }
    }

}
?>
