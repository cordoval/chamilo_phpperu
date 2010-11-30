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
 * Represents the gphoto:license element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Docs
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Photos_Extension_License extends Zend_Gdata_Extension
{

    protected $_rootElement = 'license';
    protected $_rootNamespace = 'gphoto';
    protected $_id = null;
    protected $_name = null;
    protected $_url = null;

    public function __construct($id = null, $name = null, $url = null)
    {
        $this->registerAllNamespaces(Zend_Gdata_Photos::$namespaces);
        parent::__construct();
        $this->_id = $id;
        $this->_name = $name;
        $this->_url = $url;
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_id !== null) {
            $element->setAttribute('id', $this->_id);
        }
        if ($this->_name !== null) {
            $element->setAttribute('name', $this->_name);
        }
        if ($this->_url !== null) {
            $element->setAttribute('url', $this->_url);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'id':
            $this->_id = $attribute->nodeValue;
            break;
        case 'name':
            $this->_name = $attribute->nodeValue;
            break;
        case 'url':
            $this->_url = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param string|null $id
     * @return Zend_Gdata_Docs_Extension_License Provides a fluent interface
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string|null $name
     * @return Zend_Gdata_Docs_Extension_License Provides a fluent interface
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * @param string|null $url
     * @return Zend_Gdata_Docs_Extension_License Provides a fluent interface
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

}
