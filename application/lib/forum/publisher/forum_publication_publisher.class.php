<?php
/**
 * $Id: forum_publication_publisher.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.publisher
 */
require_once dirname(__FILE__) . '/../forms/forum_publication_form.class.php';

class ForumPublicationPublisher
{
    private $parent;

    function ForumPublicationPublisher($parent)
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

        $form = new ForumPublicationForm(ForumPublicationForm :: TYPE_CREATE, new ForumPublication(), $this->parent->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $object)), $author);
        if ($form->validate())
        {
            $succes = $form->create_forum_publications($object);
            $message = $succes ? 'ForumPublicationCreated' : 'ForumPublicationNotCreated';
            $this->parent->redirect(Translation :: get($message), ! $succes, array(ForumManager :: PARAM_ACTION => null));
        }
        else
        {
            $this->parent->display_header();
            echo $form->toHtml();
            $this->parent->display_footer();
        }
    }
}
?>