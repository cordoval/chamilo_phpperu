<?php
/**
 * $Id: personal_messenger_block.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger
 */


/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class PersonalMessengerBlock extends Block
{

    /**
     * Constructor.
     * @param array $types The learning object types that may be published.
     * @param  boolean $email_option If true the publisher has the option to
     * send the published learning object by email to the selecter target users.
     */
    function PersonalMessengerBlock($parent, $block_info)
    {
        parent :: __construct($parent, $block_info);
    }

    /**
     * Create a new weblcms component
     * @param string $type The type of the component to create.
     * @param Weblcms $weblcms The weblcms in
     * which the created component will be used
     */
    static function factory($personal_messenger, $block)
    {
        $type = $block->get_component();
        $filename = dirname(__FILE__) . '/block/personal_messenger_' . $type . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" block');
        }
        $class = 'PersonalMessenger' . Utilities :: underscores_to_camelcase($type);
        require_once $filename;
        return new $class($personal_messenger, $block);
    }
}
?>