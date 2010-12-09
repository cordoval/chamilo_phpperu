<?php
namespace application\phrases;

use common\libraries\InCondition;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\Application;
use common\libraries\Request;
use common\libraries\Utilities;

use repository\ContentObject;
use repository\RepositoryDataManager;
use repository\content_object\adaptive_assessment\AdaptiveAssessment;

use common\extensions\repo_viewer\RepoViewer;
/**
 * $Id: phrases_publisher.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.publisher
 */
require_once dirname(__FILE__) . '/../forms/phrases_publication_form.class.php';

/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class PhrasesPublisher
{
    private $parent;

    function __construct($parent)
    {
        $this->parent = $parent;
    }

    function get_publications_form($ids)
    {
        if (is_null($ids))
            return '';

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        $html = array();

        if (count($ids) > 0)
        {
            $condition = new InCondition(ContentObject :: PROPERTY_ID, $ids, ContentObject :: get_table_name());
            $content_objects = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);
            //Utilities :: order_content_objects_by_title($content_objects);


            $html[] = '<div class="content_object padding_10">';
            $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects', null, 'repository') . '</div>';
            $html[] = '<div class="description">';
            $html[] = '<ul class="attachments_list">';

            while ($content_object = $content_objects->next_result())
            {
                $namespace = ContentObject :: get_content_object_type_namespace($content_object->get_type());
                $html[] = '<li><img src="' . Theme :: get_image_path($namespace) . 'logo/' . Theme :: ICON_MINI . '.png" alt="' . htmlentities(Translation :: get('TypeName', null, $namespace)) . '"/> ' . $content_object->get_title() . '</li>';
            }

            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $parameters = $this->parent->get_parameters();
        $parameters[RepoViewer :: PARAM_ID] = $ids;
        $parameters[RepoViewer :: PARAM_ACTION] = RepoViewer :: ACTION_PUBLISHER;

        $form = new PhrasesPublicationForm(PhrasesPublicationForm :: TYPE_MULTI, $ids, $this->parent->get_user(), $this->parent->get_url($parameters));
        if ($form->validate())
        {
            $publication = $form->create_content_object_publications();

            if (! $publication)
            {
                $message = Translation :: get('ObjectNotPublished', null, Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get('ObjectPublished', null, Utilities :: COMMON_LIBRARIES);
            }

            if (count($ids) == 1 && ! is_null(Request :: post('publish_and_build')))
            {
                $object = RepositoryDataManager :: get_instance()->retrieve_content_object($ids[0]);
                if ($object->get_type() == AdaptiveAssessment :: get_type_name())
                    $this->parent->redirect($message, (! $publication ? true : false), array(
                            Application :: PARAM_ACTION => PhrasesManager :: ACTION_BUILD_PHRASES,
                            PhrasesManager :: PARAM_PHRASES_PUBLICATION => $form->get_publication()->get_id()));
            }

            $this->parent->redirect($message, (! $publication ? true : false), array(
                    Application :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS));
        }
        else
        {
            $html[] = $form->toHtml();
            $html[] = '<div style="clear: both;"></div>';

            $this->parent->display_header();
            echo implode("\n", $html);
            $this->parent->display_footer();
        }
    }
}
?>