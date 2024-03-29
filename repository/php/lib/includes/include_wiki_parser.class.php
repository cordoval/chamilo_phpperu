<?php
namespace repository;

use common\libraries\EqualityCondition;
use common\libraries\Path;
use common\libraries\Text;

use repository\ContentObject;
use repository\content_object\document\Document;

/**
 * $Id: include_wiki_parser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.includes
 */
class IncludeWikiParser extends ContentObjectIncludeParser
{

    function parse_editor()
    {
        $form = $this->get_form();
        $form_type = $form->get_form_type();
        $values = $form->exportValues();
        $content_object = $form->get_content_object();

        $base_path = Path :: get(WEB_REPO_PATH);
        $html_editors = $form->get_html_editors();

        if (! $html_editors)
        {
            return;
        }

        /*
         * need to be configured to work with wikitags
         */
        foreach ($html_editors as $html_editor)
        {
            if (isset($values[$html_editor]))
            {
                $tags = Text :: fetch_tag_into_array($values[$html_editor], '[wikilink=]'); //bvb wikilink


                if (! $tags)
                {
                    return;
                }

                foreach ($tags as $tag)
                {
                    $search_path = str_replace($base_path, '', $tag['src']);

                    $rdm = RepositoryDataManager :: get_instance();
                    $condition = new EqualityCondition('path', $search_path);

                    $search_objects = $rdm->retrieve_type_content_objects(Document :: get_type_name(), $condition);

                    while ($search_object = $search_objects->next_result())
                    {
                        $content_object->include_content_object($search_object->get_id());
                    }
                }
            }
        }
    }
}
?>