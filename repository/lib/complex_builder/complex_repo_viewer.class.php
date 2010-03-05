<?php
/**
 * $Id: complex_repo_viewer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.complex_builder
 */

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class ComplexRepoViewer extends RepoViewer
{

    /**
     * The default learning objects, which are used for form defaults.
     */
    
    function ComplexRepoViewer($parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE)
    {
        parent :: __construct($parent, $types, $mail_option, $maximum_select, array(), false);
        $this->set_repo_viewer_actions(array('creator', 'browser'));
    }

    function redirect_complex($type)
    {
        return false;
    }

    function parse_input()
    {
        $this->parse_input_from_table();
    }
}
?>