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
 * @see Zend_Gdata_Docs_Extension_Role
 */
require_once 'Zend/Gdata/Docs/Extension/Role.php';

/**
 * @see Zend_Gdata_Docs_Extension_Scope
 */
require_once 'Zend/Gdata/Docs/Extension/Scope.php';

/**
 * @see Zend_Gdata_Docs_Extension_WithKey
 */
require_once 'Zend/Gdata/Docs/Extension/WithKey.php';

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
class Zend_Gdata_Docs_DocumentAclEntry extends Zend_Gdata_Entry
{

    /**
     * The role of the document permission
     *
     * @var Zend_Gdata_Docs_Extension_Role|null
     */
    protected $_role = null;

    /**
     * The scope of the document permission
     *
     * @var Zend_Gdata_Docs_Extension_Scope|null
     */
    protected $_scope = null;
    
    /**
     * The key of the document permission
     *
     * @var Zend_Gdata_Docs_Extension_WithKey|null
     */
    protected $_withKey = null;

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

        if ($this->_role != null) {
            $element->appendChild($this->_role->getDOM(
                $element->ownerDocument));
        }

        if ($this->_scope != null) {
            $element->appendChild($this->_scope->getDOM(
                $element->ownerDocument));
        }
        
        if ($this->_withKey != null) {
            $element->appendChild($this->_withKey->getDOM(
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
        case $this->lookupNamespace('gAcl') . ':' . 'role':
            $role = new Zend_Gdata_Docs_Extension_Role();
            $role->transferFromDOM($child);
            $this->_role = $role;
            break;
        case $this->lookupNamespace('gAcl') . ':' . 'scope':
            $scope = new Zend_Gdata_Docs_Extension_Scope();
            $scope->transferFromDOM($child);
            $this->_scope = $scope;
            break;
        case $this->lookupNamespace('gAcl') . ':' . 'withKey':
            $withKey = new Zend_Gdata_Docs_Extension_WithKey();
            $withKey->transferFromDOM($child);
            $this->_withKey = $withKey;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Sets the role.
     *
     * @param Zend_Gdata_Docs_Extension_Role $role The id of the document
     * @return Zend_Gdata_Docs_DocumentAclEntry Provides a fluent interface
     */
    public function setRole($role = null)
    {
        $this->_role = $role;
        return $this;
    }

    /**
     * Gets the role.
     *
     * @return Zend_Gdata_Docs_Extension_Role|null
     */
    public function getRole()
    {
        return $this->_role;
    }
    
    /**
     * Sets the scope.
     *
     * @param Zend_Gdata_Docs_Extension_Scope $scope The id of the document
     * @return Zend_Gdata_Docs_DocumentAclEntry Provides a fluent interface
     */
    public function setScope($scope = null)
    {
        $this->_scope = $scope;
        return $this;
    }

    /**
     * Gets the scope.
     *
     * @return Zend_Gdata_Docs_Extension_Scope|null
     */
    public function getScope()
    {
        return $this->_scope;
    }
    
    /**
     * Sets the scope.
     *
     * @param Zend_Gdata_Docs_Extension_WithKey $scope The id of the document
     * @return Zend_Gdata_Docs_DocumentAclEntry Provides a fluent interface
     */
    public function setWithKey($withKey = null)
    {
        $this->_withKey = $withKey;
        return $this;
    }

    /**
     * Gets the scope.
     *
     * @return Zend_Gdata_Docs_Extension_WithKey|null
     */
    public function getWithKey()
    {
        return $this->_withKey;
    }

}
