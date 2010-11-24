<?php
namespace repository;

/**
 * A class implements the <code>ComplexDisplaySupport</code> interface to
 * indicate that it will serve as a launch base for a ComplexDisplay.
 *
 * Typically this interface will never be used directly, but will be extended
 * by content object type specific interfaces.
 *
 * @author  Hans De Bisschop
 */
interface ComplexDisplaySupport
{

    /**
     * Determine the complex content object that should be displayed
     *
     * @return ContentObject
     */
    function get_root_content_object();

    /**
     * Determine whether a user has the necessary permissions
     *
     * @param int $right
     * @return boolean
     */
    function is_allowed($right);
}
?>