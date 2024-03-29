<?php

namespace application\forum;

use common\extensions\repo_viewer\RepoViewer;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: forum_publication_publisher.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.publisher
 */
class ForumPublicationPublisher
{
    private $parent;

    function __construct($parent)
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
            $message = $succes ? Translation :: get('ObjectCreated' , array ('OBJECT' => Translation :: get ('Forum', null, 'repository\content_object\forum')) , Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotCreated' , array ('OBJECT' => Translation :: get ('Forum', null, 'repository\content_object\forum')) , Utilities :: COMMON_LIBRARIES);
            $this->parent->redirect($message, ! $succes, array(ForumManager :: PARAM_ACTION => null));
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