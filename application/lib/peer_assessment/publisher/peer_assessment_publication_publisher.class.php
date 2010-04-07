<?php
/**
 * author: Nick Van Loocke
 */

require_once dirname(__FILE__) . '/../forms/peer_assessment_publication_form.class.php';

class PeerAssessmentPublicationPublisher
{
    private $parent;

    function PeerAssessmentPublicationPublisher($parent)
    {
        $this->parent = $parent;
    }

    function publish($object)
    {
        $author = $this->parent->get_user_id();
        $date = mktime(date());
        
        $pb = new PeerAssessmentPublication();
        $pb->set_content_object($object);
        $pb->set_parent_id($author);
        $pb->set_category(0);
        $pb->set_published($date);
        $pb->set_modified($date);
        
        if (! $pb->create())
        {
            $error = true;
        }
        
        if ($error)
        {
            $message = Translation :: get('ObjectNotPublished');
        }
        else
        {
            $message = Translation :: get('ObjectPublished');
        }
        
        $this->parent->redirect($message, false);
    }
}
?>