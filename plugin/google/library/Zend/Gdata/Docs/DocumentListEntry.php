<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Docs
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DocumentListEntry.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @see Zend_Gdata_EntryAtom
 */
require_once 'Zend/Gdata/Entry.php';

/**
 * @see Zend_Gdata_Docs_Extension_ResourceId
 */
require_once 'Zend/Gdata/Docs/Extension/ResourceId.php';

/**
 * @see Zend_Gdata_Docs_Extension_LastModifiedBy
 */
require_once 'Zend/Gdata/Docs/Extension/LastModifiedBy.php';

/**
 * @see Zend_Gdata_Docs_Extension_LastViewed
 */
require_once 'Zend/Gdata/Docs/Extension/LastViewed.php';

/**
 * Represents a Documents List entry in the Documents List data API meta feed
 * of a user's documents.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Docs
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Docs_DocumentListEntry extends Zend_Gdata_Entry
{

    /**
     * The resource id of the document
     *
     * @var Zend_Gdata_Docs_Extension_ResourceId|null
     */
    protected $_resourceId = null;

    /**
     * The account that last modified this document
     *
     * @var Zend_Gdata_Docs_Extension_LastModifiedBy|null
     */
    protected $_lastModifiedBy = null;

    /**
     * gd:lastViewed element
     *
     * @var Zend_Gdata_Docs_Extension_LastViewed|null
     */
    protected $_lastViewed = null;

    /**
     * Create a new instance of an entry representing a document.
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Zend_Gdata_Docs::$namespaces);
        parent::__construct($element);
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     * child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);

        if ($this->_resourceId != null) {
            $element->appendChild($this->_resourceId->getDOM(
                $element->ownerDocument));
        }

        if ($this->_lastModifiedBy != null) {
            $element->appendChild($this->_lastModifiedBy->getDOM(
                $element->ownerDocument));
        }

        if ($this->_lastViewed != null) {
            $element->appendChild($this->_lastViewed->getDOM(
                $element->ownerDocument));
        }

        return $element;
    }

    /**
     * Creates individual Entry objects of the appropriate type and
     * stores them in the $_entry array based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;

        switch ($absoluteNodeName) {
        case $this->lookupNamespace('gd') . ':' . 'resourceId':
            $resourceId = new Zend_Gdata_Docs_Extension_ResourceId();
            $resourceId->transferFromDOM($child);
            $this->_resourceId = $resourceId;
            break;
        case $this->lookupNamespace('gd') . ':' . 'lastModifiedBy':
            $lastModifiedBy = new Zend_Gdata_Docs_Extension_LastModifiedBy();
            $lastModifiedBy->transferFromDOM($child);
            $this->_lastModifiedBy = $lastModifiedBy;
            break;
        case $this->lookupNamespace('gd') . ':' . 'lastViewed':
            $lastViewed = new Zend_Gdata_Docs_Extension_LastViewed();
            $lastViewed->transferFromDOM($child);
            $this->_lastViewed = $lastViewed;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Sets the resource id.
     *
     * @param Zend_Gdata_Docs_Extension_ResourceId $resourceId The id of the document
     * @return Zend_Gdata_Docs_DocumentListEntry Provides a fluent interface
     */
    public function setResourceId($resourceId = null)
    {
        $this->_resourceId = $resourceId;
        return $this;
    }

    /**
     * Gets the resource id.
     *
     * @return Zend_Gdata_Docs_Extension_ResourceId|null
     */
    public function getResourceId()
    {
        return $this->_resourceId;
    }

    /**
     * Sets the last account to modify this document.
     *
     * @param Zend_Gdata_Docs_Extension_LastModifiedBy $lastModifiedBy The last account to modify the document
     * @return Zend_Gdata_Docs_DocumentListEntry Provides a fluent interface
     */
    public function setLastModifiedBy($lastModifiedBy = null)
    {
        $this->_lastModifiedBy = $lastModifiedBy;
        return $this;
    }

    /**
     * Gets the last account to modify this document.
     *
     * @return Zend_Gdata_Docs_Extension_LastModifiedBy|null
     */
    public function getLastModifiedBy()
    {
        return $this->_lastModifiedBy;
    }

    /**
     * Sets the last view date of this document.
     *
     * @param Zend_Gdata_Docs_Extension_LastViewed $lastViewed The last view date of this document
     * @return Zend_Gdata_Docs_DocumentListEntry Provides a fluent interface
     */
    public function setLastViewed($lastViewed = null)
    {
        $this->_lastViewed = $lastViewed;
        return $this;
    }

    /**
     * Gets the last view date of this document.
     *
     * @return Zend_Gdata_Docs_Extension_LastViewed|null
     */
    public function getLastViewed()
    {
        return $this->_lastViewed;
    }

}
