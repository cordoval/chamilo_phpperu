<?php
namespace repository\content_object\learning_path;

/**
 * A class implements the <code>LearningPathComplexDisplaySupport</code> interface to
 * indicate that it will serve as a launch base for a WikiComplexDisplay.
 *
 * @author  Hans De Bisschop
 */
interface LearningPathComplexDisplaySupport
{
    function retrieve_learning_path_tracker();

    function retrieve_tracker_items($learning_path_tracker);

    function get_learning_path_tree_menu_url();
}
?>