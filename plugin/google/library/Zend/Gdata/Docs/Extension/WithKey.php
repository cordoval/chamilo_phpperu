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
 * @version    $Id: ResourceId.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/**
 * @see Zend_Gdata_Extension
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * Represents the gAcl:withKey element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Docs
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Docs_Extension_WithKey extends Zend_Gdata_Extension
{

    protected $_rootElement = 'withKey';
    protected $_rootNamespace = 'gAcl';
    protected $_key = null;
    protected $_role = null;

    public function __construct($key = null, $role = null)
    {
        $this->registerAllNamespaces(Zend_Gdata_Docs::$namespaces);
        parent::__construct();
        $this->_key = $key;
        $this->_role = $role;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_key !== null) {
            $element->setAttribute('key', $this->_key);
        }
        if ($this->_role != null) {
            $element->appendChild($this->_role->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'key':
            $this->_key = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }
    
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('gAcl') . ':' . 'role':
            $role = new Zend_Gdata_Docs_Extension_Role();
            $role->transferFromDOM($child);
            $this->_role = $role;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * @return string|null
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * @param string|null $key
     * @return Zend_Gdata_Docs_Extension_WithKey Provides a fluent interface
     */
    public function setKey($key)
    {
        $this->_key = $key;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * @param string|null $role
     * @return Zend_Gdata_Docs_Extension_Role Provides a fluent interface
     */
    public function setRole($role)
    {
        $this->_role = $role;
        return $this;
    }

}
