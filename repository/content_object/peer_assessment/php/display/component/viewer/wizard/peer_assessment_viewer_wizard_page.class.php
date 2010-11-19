<?php
namespace repository\content_object\peer_assessment;

use common\libraries\FormValidatorPage;

abstract class PeerAssessmentViewerWizardPage extends FormValidatorPage
{
    /**
     * The parent in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     * @param string $name A unique name of this page in the wizard
     * @param Tool $parent The parent in which the wizard
     * runs.
     */
    public function __construct($name, $parent)
    {
        $this->parent = $parent;;
        parent :: FormValidatorPage($name, 'post');
        $this->updateAttributes(array('action' => $parent->get_parent()->get_url()));
    }

    /**
     * Returns the parent in which this wizard runs
     * @return Component
     */
    function get_parent()
    {
        return $this->parent;
    }
}
?>