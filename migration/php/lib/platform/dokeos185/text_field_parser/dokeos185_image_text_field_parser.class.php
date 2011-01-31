<?php

namespace migration;

use common\libraries\Text;
use repository\RepositoryDataManager;
use repository\RepositoryManager;
use repository\content_object\document\Document;

/**
 * Implementation of the dokeos 185 text field parser to parse images from the given field
 * @author Sven Vanpoucke
 *
 */
class Dokeos185ImageTextFieldParser extends Dokeos185TextFieldParser
{

    function parse($text_field)
    {
        $tags = Text :: parse_html_file($text_field, 'img');
        foreach ($tags as $tag)
        {
            $src = $tag->getAttribute('src');
            $filename = basename($src);
            $document = RepositoryDataManager :: get_document_by_filename($filename);
            if ($document instanceof Document)
            {
                $this->add_included_object($document->get_id());

                $url = RepositoryManager :: get_document_downloader_url($document->get_id());
                $text_field = str_replace($src, $url, $text_field);
            }
        }

        return $text_field;
    }

}