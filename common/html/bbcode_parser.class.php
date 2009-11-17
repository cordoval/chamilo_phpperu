<?php
/**
 * $Id: bbcode_parser.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html
 */
require_once Path :: get_plugin_path() . 'stringparser/stringparser_bbcode.class.php';

class BbcodeParser
{
    
    private static $instance;
    
    private $bbcode;

    function BbcodeParser()
    {
        $this->bbcode = new StringParser_BBCode();
        //		$this->bbcode->addFilter (STRINGPARSER_FILTER_PRE, 'convertlinebreaks');
        

        //		$this->bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'htmlspecialchars');
        //		$this->bbcode->addParser (array ('block', 'inline', 'link', 'listitem'), 'nl2br');
        //		$this->bbcode->addParser ('list', 'bbcode_stripcontents');
        

        $this->bbcode->addCode('b', 'simple_replace', null, array('start_tag' => '<b>', 'end_tag' => '</b>'), 'inline', array('listitem', 'block', 'inline', 'link'), array());
        $this->bbcode->addCode('i', 'simple_replace', null, array('start_tag' => '<i>', 'end_tag' => '</i>'), 'inline', array('listitem', 'block', 'inline', 'link'), array());
        $this->bbcode->addCode('url', 'usecontent?', 'do_bbcode_url', array('usecontent_param' => 'default'), 'link', array('listitem', 'block', 'inline'), array('link'));
        $this->bbcode->addCode('link', 'callback_replace_single', 'do_bbcode_url', array(), 'link', array('listitem', 'block', 'inline'), array('link'));
        $this->bbcode->addCode('img', 'usecontent', 'do_bbcode_img', array(), 'image', array('listitem', 'block', 'inline', 'link'), array());
        $this->bbcode->setOccurrenceType('img', 'image');
        $this->bbcode->setMaxOccurrences('image', 2);
        //		$this->bbcode->addCode ('list', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
    //		                  'list', array ('block', 'listitem'), array ());
    //		$this->bbcode->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
    //		                  'listitem', array ('list'), array ());
    //		$this->bbcode->setCodeFlag ('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
    //		$this->bbcode->setCodeFlag ('*', 'paragraphs', true);
    //		$this->bbcode->setCodeFlag ('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
    //		$this->bbcode->setCodeFlag ('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
    //		$this->bbcode->setCodeFlag ('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);
    //		$this->bbcode->setRootParagraphHandling (true);
    }

    // Remove everything but the newline charachter
    function bbcode_stripcontents($text)
    {
        return preg_replace("/[^\n]/", '', $text);
    }

    function do_bbcode_url($action, $attributes, $content, $params, $node_object)
    {
        if (! isset($attributes['default']))
        {
            $url = $content;
            $text = htmlspecialchars($content);
        }
        else
        {
            $url = $attributes['default'];
            $text = $content;
        }
        if ($action == 'validate')
        {
            if (substr($url, 0, 5) == 'data:' || substr($url, 0, 5) == 'file:' || substr($url, 0, 11) == 'javascript:' || substr($url, 0, 4) == 'jar:')
            {
                return false;
            }
            return true;
        }
        return '<a href="' . htmlspecialchars($url) . '">' . $text . '</a>';
    }

    // Function to include images
    function do_bbcode_img($action, $attributes, $content, $params, $node_object)
    {
        if ($action == 'validate')
        {
            if (substr($content, 0, 5) == 'data:' || substr($content, 0, 5) == 'file:' || substr($content, 0, 11) == 'javascript:' || substr($content, 0, 4) == 'jar:')
            {
                return false;
            }
            return true;
        }
        return '<img src="' . htmlspecialchars($content) . '" alt="">';
    }

    function parse($source)
    {
        $bbcode = $this->bbcode;
        
        return $bbcode->parse($source);
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }
}
?>