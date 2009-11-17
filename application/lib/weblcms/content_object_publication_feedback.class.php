<?php
/**
 * $Id: content_object_publication_feedback.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */

/**
 * This class represents a learning object publication feedback.
 *
 * When publishing a learning object from the repository in the weblcms
 * application, attached to another learning object, a new object of this type is created.
 */
class ContentObjectPublicationFeedback extends ContentObjectPublication
{

    /**
     * Constructor
     * @param int $id The id of this learning object publiction
     * @param ContentObject $learningObject The learning object which is
     * published by this publication
     * @param string $course The course code of the course where this
     * publication is made
     * @param string $tool The tool where this publication is made
     * @param int $parent_id The id of this learning object publication parent
     * @param int $category The id of the learning object publication category
     * in which this publication is stored
     * @param array $targetUsers The users for which this publication is made.
     * If this array contains no elements, the publication is for everybody.
     * @param array $targetCourseGroups The course_groups for which this publication is made.
     * If this array contains no elements, the publication is for everybody.
     * @param int $fromDate The date on which this publication should become
     * available. If value is 0, publication is available forever.
     * @param int $toDate The date on which this publication should become
     * unavailable. If value is 0, publication is available forever.
     * @param int $repo_viewer The user id of the person who created this
     * publication.
     * @param int $publicationDate The date on which this publication was made.
     * @param int $modifiedDate The date on which this publication was updated.
     * @param boolean $hidden If true, this publication is invisible
     * @param int $displayOrder The display order of this publication in its
     * location (course - tool - category)
     */
    function ContentObjectPublicationFeedback($id, $learningObject, $course, $tool, $parent_id, $repo_viewer, $publicationDate, $modifiedDate, $hidden, $emailSent)
    {
        
        parent :: ContentObjectPublication($id, $learningObject, $course, $tool, 0, array(), array(), 0, 0, $repo_viewer, $publicationDate, $modifiedDate, $hidden, 0, $emailSent);
        $this->set_parent_id($parent_id);
        $this->set_modified_date(time());
        $this->set_email_sent();
    }

    /*
    * Sets a property of this learning object publication.
    * See constructor for detailed information about the property.
    * @see ContentObjectPublicationFeedback()
 	*/
    
    function set_category_id($category)
    {
        parent :: set_category(0);
    }

    function set_target_users($targetUsers)
    {
        parent :: set_target_users(array());
    }

    function set_target_course_groups($targetCourseGroups)
    {
        parent :: set_target_course_groups(array());
    }

    function set_from_date($fromDate)
    {
        parent :: set_from_date(0);
    }

    function set_to_date($toDate)
    {
        parent :: set_to_date(0);
    }

    function set_hidden($hidden)
    {
        parent :: set_hidden(0);
    }

    function set_display_order_index($displayOrder)
    {
        parent :: set_display_order_index(0);
    }

    function set_email_sent($emailSent)
    {
        parent :: set_email_sent(0);
    }

    function create()
    {
        if (Request :: get('pid'))
            $this->update_parent_modified_date();
        return parent :: create();
    }

    function update()
    {
        $this->update_parent_modified_date();
        return parent :: update();
    }

    function update_parent_modified_date()
    {
        $dm = WeblcmsDataManager :: get_instance();
        $parent_object = $dm->retrieve_content_object_publication($this->get_parent_id());
        $parent_object->set_modified_date(time());
        $parent_object->update();
    }
}
?>