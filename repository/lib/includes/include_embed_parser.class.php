<?php
/**
 * $Id: include_flash_parser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.includes
 */
class IncludeEmbedParser extends ContentObjectIncludeParser
{

    function parse_editor()
    {
        $form = $this->get_form();
        $form_type = $form->get_form_type();
        $values = $form->exportValues();
        $content_object = $form->get_content_object();

        $base_path = Path :: get(WEB_REPO_PATH);
        $html_editors = $form->get_html_editors();

        foreach ($html_editors as $html_editor)
        {
            if (isset($values[$html_editor]))
            {
                $tags = Text :: parse_html_file($values[$html_editor], 'embed');

                foreach ($tags as $tag)
                {
                    $source = $tag->getAttribute('src');
                    $matches = preg_match(HtmlEditorProcessor :: get_repository_document_display_matching_url(), $source);

                    if ($matches === 1)
                    {
                        $source_components = parse_url($source);
                        $source_query_components = Text :: parse_query_string($source_components['query']);
                        $content_object_id = $source_query_components[RepositoryManager :: PARAM_CONTENT_OBJECT_ID];

                        if ($content_object_id)
                        {
                            $included_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object_id);

                            if ($included_object->is_flash() || $included_object->is_video() || $included_object->is_audio())
                            {
                                $content_object->include_content_object($included_object->get_id());
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
