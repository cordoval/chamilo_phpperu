<?php
require_once dirname(__FILE__) . '/../forms/peer_assessment_publication_form.class.php';
/**
 *	@author Nick Van Loocke
 */

class PeerAssessmentPublicationPublisher
{
    private $parent;

    function PeerAssessmentPublicationPublisher($parent)
    {
        $this->parent = $parent;
    }

    function publish($object)
    {
        $author = $this->parent->get_user();
        
        if (! is_array($object))
        {
            $object = array($object);
        }
        
        $form = new PeerAssessmentPublicationForm(PeerAssessmentPublicationForm :: TYPE_CREATE, new PeerAssessmentPublication(), $this->parent->get_url(array('object' => $object)), $author);
        if ($form->validate())
        {
            $succes = $form->create_peer_assessment_publications($object);
            $message = $succes ? 'PeerAssessmentPublicationCreated' : 'PeerAssessmentPublicationNotCreated';
            $this->parent->redirect(Translation :: get($message), ! $succes, array(PeerAssessmentManager :: PARAM_ACTION => null));
        }
        else
        {
            return $form->toHtml();
        }
    }
}
?>