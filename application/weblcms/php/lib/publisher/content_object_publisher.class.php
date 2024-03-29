<?php
namespace application\weblcms;

use repository\ContentObject;
use repository\RepositoryDataManager;
use common\libraries\Theme;
use common\libraries\InCondition;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: content_object_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.publisher
 */
require_once dirname(__FILE__) . '/../content_object_publication_form.class.php';

//require_once Path :: get_common_extensions_path() . 'publisher/component/publication_candidate_table/publication_candidate_table.class.php';


/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class ContentObjectPublisher
{
    private $parent;

    function __construct($parent)
    {
        $this->parent = $parent;
    }

    function get_publications_form($ids)
    {
        $html = array();

        if (is_null($ids))
        {
            return '';
        }

        if (! is_array($ids))
        {
            $ids = array($ids);
        }

        if (count($ids) > 0)
        {
            $condition = new InCondition(ContentObject :: PROPERTY_ID, $ids, ContentObject :: get_table_name());
            $content_objects = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);

            $html[] = '<div class="content_object padding_10">';
            $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects', null, Utilities :: COMMON_LIBRARIES) . '</div>';
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

        $form = new ContentObjectPublicationForm(ContentObjectPublicationForm :: TYPE_MULTI, $ids, $this->parent, true, $this->parent->get_course());
        if ($form->validate())
        {
            $publication = $form->create_content_object_publications();

            $parameters = array();

            if (! $publication)
            {
                $message = Translation :: get('ObjectNotPublished', array(
                        'OBJECT' => Translation :: get('Object')), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get('ObjectPublished', array(
                        'OBJECT' => Translation :: get('Object')), Utilities :: COMMON_LIBRARIES);
            }

            $parameters['tool_action'] = null;

            $this->parent->redirect($message, (! $publication ? true : false), $parameters);
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