<?php
/**
 * $Id: repository_dummy.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.block
 */
class RepositoryDummy extends RepositoryBlock
{

    /**
     * Runs this component and displays its output.
     * This component is only meant for use within the home-component and not as a standalone item.
     */
    function run()
    {
        return $this->as_html();
    }

    function as_html()
    {
        $html = array();
        $html[] = $this->display_header();
        $html[] = Translation :: get('PlaceholderBlockText');
        $html[] = $this->display_footer();
        
        return implode("\n", $html);
    }
}
?>