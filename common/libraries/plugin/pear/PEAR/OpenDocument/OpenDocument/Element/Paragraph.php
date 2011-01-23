<?php
/**
* PEAR OpenDocument package
* 
* PHP version 5
*
* LICENSE: This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
* 
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
* 
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
* 
* @category File_Formats
* @package  OpenDocument
* @author   Alexander Pak <irokez@gmail.com>
* @license  http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version  CVS: $Id: Paragraph.php 283200 2009-06-30 20:24:11Z cweiske $
* @link     http://pear.php.net/package/OpenDocument
* @since    File available since Release 0.1.0
*/

require_once dirname(__FILE__) . '/../StyledElement.php';

/**
* Paragraph element
*
* @category File_Formats
* @package  OpenDocument
* @author   Alexander Pak <irokez@gmail.com>
* @license  http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @link     http://pear.php.net/package/OpenDocument
* @since    File available since Release 0.1.0
*/
class OpenDocument_Element_Paragraph extends OpenDocument_StyledElement
{
    /**
     * Node namespace
     */
    const nodeNS = OpenDocument::NS_TEXT;

    /**
     * Node namespace
     */
    const nodePrefix = 'text';
    
    /**
     * Node name
     */
    const nodeName = 'p';
    
    /**
     * Element style name prefix
     *
     */
    const styleNamePrefix = 'P';

    /**
     * Style family to use
     *
     * @var string
     */
    const styleFamily = 'paragraph';
    

    /**
     * Constructor
     *
     * @param DOMNode               $node     Node to add heading to
     * @param OpenDocument_Document $document Document to add heading to
     */
    public function __construct(DOMNode $node, OpenDocument_Document $document)
    {
        parent::__construct($node, $document);
        
        $this->allowedElements = array(
            'OpenDocument_Element_Span',
            'OpenDocument_Element_Hyperlink',
        );
    }

    /**
     * Create element instance
     *
     * @param mixed $object  Document or element to append paragraph to
     * @param mixed $content Contents of paragraph
     *
     * @return OpenDocument_Element_Paragraph
     *
     * @throws OpenDocument_Exception
     */
    public static function instance($object, $content)
    {
        if ($object instanceof OpenDocument_Document) {
            $document = $object;
            $node = $object->cursor;
        } else if ($object instanceof OpenDocument_Element) {
            $document = $object->getDocument();
            $node = $object->getNode();
        } else {
            throw new OpenDocument_Exception(
                OpenDocument_Exception::ELEM_OR_DOC_EXPECTED
            );
        }
        
        $element = new OpenDocument_Element_Paragraph(
            $node->ownerDocument->createElementNS(
                self::nodeNS, self::nodeName
            ),
            $document
        );
        $node->appendChild($element->node);

        if (is_scalar($content)) {
            $element->createTextElement($content);
        }
        return $element;
    }

    /**
     * Generate element new style name
     *
     * @return string
     */
    public function generateStyleName()
    {
        self::$styleNameMaxNumber ++;
        return self::styleNamePrefix . self::$styleNameMaxNumber;
    }

    /************** Elements ****************/
    
    /**
     * Create text element.
     *
     * @param string $text Content for text element.
     *
     * @return OpenDocument_Element_Text
     */
    public function createTextElement($text)
    {
        return OpenDocument_Element_Text::instance($this, $text);
    }

    /**
     * Create a hyperlink
     *
     * @param string $text     Content text for link
     * @param string $location URL
     * @param string $type     'simple'
     * @param string $target   Target frame
     * @param string $name     Name (id) of link
     *
     * @return OpenDocument_Element_Hyperlink
     */
    public function createHyperlink(
        $text, $location, $type = 'simple', $target = '', $name = ''
    ) {
        return OpenDocument_Element_Hyperlink::instance(
            $this, $text, $location, $type, $target, $name
        );
    }
    
    /**
     * Create OpenDocument_Span element
     *
     * @param string $text Content for span element
     *
     * @return OpenDocument_Element_Span
     */
    public function createSpan($text)
    {
        return OpenDocument_Element_Span::instance($this, $text);
    }
}
?>