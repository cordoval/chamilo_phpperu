<?php
/**
 * The renderer that uses HTML_Template_Sigma instance for menu output.
 * 
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category    HTML
 * @package     HTML_Menu
 * @author      Alexey Borzov <avb@php.net>
 * @copyright   2001-2007 The PHP Group
 * @license     http://www.php.net/license/3_01.txt PHP License 3.01
 * @version     CVS: $Id: SigmaRenderer.php 137 2009-11-09 13:24:37Z vanpouckesven $
 * @link        http://pear.php.net/package/HTML_Menu
 */

/**
 * Abstract base class for HTML_Menu renderers
 */ 
require_once 'HTML/Menu/Renderer.php';

/**
 * The renderer that uses HTML_Template_Sigma instance for menu output.
 *
 * @category    HTML
 * @package     HTML_Menu
 * @author      Alexey Borzov <avb@php.net>
 * @version     Release: 2.1.4
 */
class HTML_Menu_SigmaRenderer extends HTML_Menu_Renderer
{
   /**#@+
    * @access private
    */
   /**
    * Template object used for output
    * @var HTML_Template_Sigma
    */
    var $_tpl;

   /**
    * Mapping from HTML_MENU_ENTRY_* constants to template block names
    * @var array
    */
    var $_typeNames = array(
        HTML_MENU_ENTRY_INACTIVE    => 'inactive',
        HTML_MENU_ENTRY_ACTIVE      => 'active',
        HTML_MENU_ENTRY_ACTIVEPATH  => 'activepath',
        HTML_MENU_ENTRY_PREVIOUS    => 'previous',
        HTML_MENU_ENTRY_NEXT        => 'next',
        HTML_MENU_ENTRY_UPPER       => 'upper',
        HTML_MENU_ENTRY_BREADCRUMB  => 'breadcrumb'
    );

   /**
    * Prefix for template blocks and placeholders
    * @var string
    */
    var $_prefix;
    /**#@-*/

   /**
    * Class constructor.
    * 
    * Sets the template object to use and sets prefix for template blocks
    * and placeholders. We use prefix to avoid name collisions with existing 
    * template blocks and it is customisable to allow output of several menus 
    * into one template.
    *
    * @access public
    * @param  HTML_Template_Sigma   template object to use for output
    * @param  string                prefix for template blocks and placeholders
    */
    function __construct(&$tpl, $prefix = 'mu_')
    {
        $this->_tpl    =& $tpl;
        $this->_prefix =  $prefix;
    }

    function finishMenu($level)
    {
        if ('rows' == $this->_menuType && $this->_tpl->blockExists($this->_prefix . ($level + 1) . '_menu_loop')) {
            $this->_tpl->parse($this->_prefix . ($level + 1) . '_menu_loop');
        } elseif ($this->_tpl->blockExists($this->_prefix . 'menu_loop')) {
            $this->_tpl->parse($this->_prefix . 'menu_loop');
        }
    }
    
    function finishRow($level)
    {
        if ('rows' == $this->_menuType && $this->_tpl->blockExists($this->_prefix . ($level + 1) . '_row_loop')) {
            $this->_tpl->parse($this->_prefix . ($level + 1) . '_row_loop');
        } elseif ($this->_tpl->blockExists($this->_prefix . 'row_loop')) {
            $this->_tpl->parse($this->_prefix . 'row_loop');
        }
    }

    function renderEntry($node, $level, $type)
    {
        if (in_array($this->_menuType, array('tree', 'sitemap', 'rows'))
            && $this->_tpl->blockExists($this->_prefix . ($level + 1) . '_' . $this->_typeNames[$type])) {

            $blockName = $this->_prefix . ($level + 1) . '_' . $this->_typeNames[$type];
        } else {
            $blockName = $this->_prefix . $this->_typeNames[$type];
        }
        if (('tree' == $this->_menuType || 'sitemap' == $this->_menuType) &&
             $this->_tpl->blockExists($blockName . '_indent')) {

            for ($i = 0; $i < $level; $i++) {
                $this->_tpl->touchBlock($blockName . '_indent');
                $this->_tpl->parse($blockName . '_indent');
            }
        }
        foreach ($node as $k => $v) {
            if ('sub' != $k && $this->_tpl->placeholderExists($this->_prefix . $k, $blockName)) {
                $this->_tpl->setVariable($this->_prefix . $k, $v);
            }
        }
        $this->_tpl->parse($blockName);
        if ('rows' == $this->_menuType 
            && $this->_tpl->blockExists($this->_prefix . ($level + 1) . '_entry_loop')) {
            
            $this->_tpl->parse($this->_prefix . ($level + 1) . '_entry_loop');
        } else {
            $this->_tpl->parse($this->_prefix . 'entry_loop');
        }
    }
}
?>
