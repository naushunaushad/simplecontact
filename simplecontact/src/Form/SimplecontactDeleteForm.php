<?php

namespace Drupal\simplecontact\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\simplecontact\Contacts;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides simplecontact delete contact form
 */
class SimplecontactDeleteForm extends ConfirmFormBase {

    /**
     *
     * Contact ID
     * @var INT
     */
    private $cid;
    
    /**
     *
     * @var \Drupal\simplecontact\Contacts
     */
    protected $contacts;

    public function __construct(Contacts $simplecontact) {

        $this->contacts = $simplecontact;
    }

    public static function create(ContainerInterface $container) {
        return new static($container->get('simplecontact.contacts'));
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return "simplecontact_delete_form";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $cid = NULL) {
        $this->cid = $cid;
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this->contacts->contactsDelete($this->cid);
        \Drupal::messenger()->addMessage('Contact deleted successfully.');
        $form_state->setRedirect('simplecontact.list.page');
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelUrl() {
        return new Url('simplecontact.list.page');
    }

    /**
     * {@inheritdoc}
     */
    public function getQuestion() {
        return $this->t('Do you want to delete this contact?');
    }

}
