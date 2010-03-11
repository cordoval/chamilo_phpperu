<?php
/**
 * $Id: include_image_parser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.includes
 */
class IncludeImageParser extends ContentObjectIncludeParser
{

    function parse_editor()
    {
        $form = $this->get_form();
        $form_type = $form->get_form_type();
        $values = $form->exportValues();
        $content_object = $form->get_content_object();

        $base_path = Path :: get(WEB_REPO_PATH);
        $html_editors = $form->get_html_editors();

//        foreach ($html_editors as $html_editor)
//        {
//            if (isset($values[$html_editor]))
//            {
//                $tags = Text :: parse_html_file($values[$html_editor]);
////                $tags = Text :: fetch_tag_into_array($values[$html_editor], '<img>');
//
//                foreach ($tags as $tag)
//                {
//                    $source = $tag->getAttribute('src');
//                    dump($source);
//                    continue;
//
//                    $search_path = str_replace($base_path, '', $tag['src']);
//
//                    $rdm = RepositoryDataManager :: get_instance();
//                    $condition = new Equalitycondition('path', $search_path);
//
//                    $search_objects = $rdm->retrieve_type_content_objects('document', $condition);
//
//                    while ($search_object = $search_objects->next_result())
//                    {
//                        $content_object->include_content_object($search_object->get_id());
//                    }
//                }
//                exit;
//            }
//        }
    }
}
?>
