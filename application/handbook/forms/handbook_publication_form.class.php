<?php
require_once dirname(__FILE__) . '/../handbook_publication.class.php';

/**
 * This class describes the form for a HandbookPublication object.
 * @author Sven Vanpoucke
 * @author Nathalie Blocry
 **/
class HandbookPublicationForm extends FormValidator
{
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;

	private $handbook_publication;
	private $user;

    function HandbookPublicationForm($form_type, $handbook_publication, $action, $user)
    {
    	parent :: __construct('handbook_publication_settings', 'post', $action);

    	$this->handbook_publication = $handbook_publication;
    	$this->user = $user;
		$this->form_type = $form_type;

		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}

		$this->setDefaults();
    }

    function build_basic_form()
    {
		$this->addElement('text', HandbookPublication :: PROPERTY_ID, Translation :: get('TEST'));
		
    }

    function build_editing_form()
    {
    	$this->build_basic_form();

    	//$this->addElement('hidden', HandbookPublication :: PROPERTY_ID);

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
    	$this->build_basic_form();

		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
		$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_handbook_publication()
    {
    	$handbook_publication = $this->handbook_publication;
    	$values = $this->exportValues();

    	$handbook_publication->set_id($values[HandbookPublication :: PROPERTY_ID]);
    	$handbook_publication->set_content_object_id($values[HandbookPublication :: PROPERTY_CONTENT_OBJECT_ID]);
    	$handbook_publication->set_owner_id($values[HandbookPublication :: PROPERTY_OWNER_ID]);
    	$handbook_publication->set_publisher_id($values[HandbookPublication :: PROPERTY_PUBLISHER_ID]);
    	$handbook_publication->set_published($values[HandbookPublication :: PROPERTY_PUBLISHED]);

    	return $handbook_publication->update();
    }

      function create_handbook_publications($object_ids, $owner_id = null)
    {
        $values = $this->exportValues();

        $success = true;

        foreach ($object_ids as $object_id)
        {

            $handbook_publication = new HandbookPublication();
            $handbook_publication->set_content_object_id($object_id);
            $handbook_publication->set_publisher_id($this->user->get_id());

            if($owner_id == null)
            {
                //owner is  the same user as publisher
               $owner_id =  $this->user->get_id();
            }

            $handbook_publication->set_owner_id($owner_id);
            $handbook_publication->set_published(time());
            $success &= $handbook_publication->create();


            //TODO: implement rights


        }
        return $success;
    }


    function create_handbook_publication()
    {
    	$handbook_publication = $this->handbook_publication;
    	$values = $this->exportValues();

    	$handbook_publication->set_id($values[HandbookPublication :: PROPERTY_ID]);
    	$handbook_publication->set_content_object_id($values[HandbookPublication :: PROPERTY_CONTENT_OBJECT_ID]);
    	$handbook_publication->set_owner_id($values[HandbookPublication :: PROPERTY_OWNER_ID]);
    	$handbook_publication->set_publisher_id($values[HandbookPublication :: PROPERTY_PUBLISHER_ID]);
    	$handbook_publication->set_published($values[HandbookPublication :: PROPERTY_PUBLISHED]);

   		return $handbook_publication->create();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$handbook_publication = $this->handbook_publication;

    	$defaults[HandbookPublication :: PROPERTY_ID] = $handbook_publication->get_id();
    	$defaults[HandbookPublication :: PROPERTY_CONTENT_OBJECT_ID] = $handbook_publication->get_content_object_id();
    	$defaults[HandbookPublication :: PROPERTY_OWNER_ID] = $handbook_publication->get_owner_id();
    	$defaults[HandbookPublication :: PROPERTY_PUBLISHER_ID] = $handbook_publication->get_publisher_id();
    	$defaults[HandbookPublication :: PROPERTY_PUBLISHED] = $handbook_publication->get_published();

		parent :: setDefaults($defaults);
	}
}
?>