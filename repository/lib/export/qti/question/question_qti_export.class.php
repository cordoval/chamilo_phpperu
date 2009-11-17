<?php
/**
 * $Id: question_qti_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.qti.question
 */
require_once dirname(__FILE__) . '/../qti_export.class.php';

abstract class QuestionQtiExport extends QtiExport
{
    private $question;

    function QuestionQtiExport($question)
    {
        $this->question = $question;
        parent :: __construct($question);
    }

    static function factory_question($question)
    {
        $type = $question->get_type();
        
        $file = dirname(__FILE__) . '/question_types/' . $type . '_qti_export.class.php';
        
        if (! file_exists($file))
        {
            die('file does not exist: ' . $file);
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'QtiExport';
        $exporter = new $class($question);
        return $exporter;
    
    }

    function create_qti_file($xml)
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $temp_dir = Path :: get(SYS_TEMP_PATH) . $this->get_content_object()->get_owner_id() . '/export_qti/';
        
        if (! is_dir($temp_dir))
        {
            mkdir($temp_dir, '0777', true);
        }
        
        $xml_path = $temp_dir . 'question_qti_' . $this->get_content_object()->get_id() . '.xml';
        $doc->save($xml_path);
        return $xml_path;
    }

    function include_question_images($text)
    {
        $tags = Text :: fetch_tag_into_array($text, '<img>');
        $temp_dir = Path :: get(SYS_TEMP_PATH) . $this->get_content_object()->get_owner_id() . '/export_qti/images/';
        
        if (! file_exists($temp_dir))
        {
            mkdir($temp_dir, null, true);
        }
        
        foreach ($tags as $tag)
        {
            $parts = split('/', $tag['src']);
            $newfilename = $temp_dir . $parts[count($parts) - 1];
            $repl_filename = 'images/' . $parts[count($parts) - 1];
            $files[$newfilename] = $tag['src']; //str_replace($base_path, '', $tag['src']);
            $text = str_replace($tag['src'], $repl_filename, $text);
        }
        foreach ($files as $new => $original)
        {
            copy($original, $new);
        }
        return $text;
    }
}
?>