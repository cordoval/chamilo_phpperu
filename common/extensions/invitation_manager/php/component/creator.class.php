<?php
namespace common\extensions\invitation_manager;

use common\libraries\Translation;

class InvitationManagerCreatorComponent extends InvitationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $form = new InvitationForm($this, $this->get_url());

        if ($form->validate())
        {
            $success = $form->process();
        	$this->redirect(Translation :: get($success ? 'EmailSent' : 'EmailNotSent'), ($success ? false : true), array());
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
}
?>