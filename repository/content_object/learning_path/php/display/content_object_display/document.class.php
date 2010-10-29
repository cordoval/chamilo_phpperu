<?php
namespace repository\content_object\learning_path;

use repository\RepositoryManager;
use common\libraries\Translation;
use repository\content_object\document\Document;
use repository\ComplexDisplay;

require_once dirname(__FILE__) . '/../learning_path_display_embedder.class.php';

/**
 * @package repository.content_object.learning_path
 */

class LearningPathDocumentContentObjectDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($content_object, $learning_path_item_attempt_data, $continue_url, $previous_url, $jump_urls)
    {
        $html = array();
        $html[] = $this->add_tracking_javascript();

        if ($content_object->is_showable())
        {
            $parameters = array();
            $parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = LearningPathDisplay :: ACTION_EMBED;
            $parameters[LearningPathDisplayEmbedder :: PARAM_EMBEDDER_ACTION] = LearningPathDisplayEmbedder :: ACTION_DOCUMENT;
            $parameters[self :: PARAM_EMBEDDED_CONTENT_OBJECT_ID] = $content_object->get_id();

            $html[] = $this->display_link($this->get_parent()->get_url($parameters));
        }
        else
        {
            $info = array();
            $info[] = sprintf(Translation :: get('LPDownloadDocument'), $content_object->get_filename(), $content_object->get_filesize());
            $info[] = '<br />';
            $info[] = '<a target="about:blank" href="' . RepositoryManager :: get_document_downloader_url($content_object->get_id()) . '">' . Translation :: get('Download') . '</a>';

            $html[] = '<h3>' . $content_object->get_title() . '</h3>';
            $html[] = $this->display_box(implode("\n", $info));
        }

        return implode("\n", $html);
    }
}
?>