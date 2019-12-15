<?php

namespace Drupal\simplecontact\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\simplecontact\Contacts;
use Drupal\simplecontact\ContactsValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provide simplecontact add form for normal users
 */
class SimplecontactForm extends FormBase {

    /**
     *
     * @var \Drupal\simplecontact\Contacts
     */
    protected $contacts;

    /**
     *
     * @var \Drupal\simplecontact\ContactsValidator 
     */
    protected $validator;

    public function __construct(Contacts $simplecontact, ContactsValidator $validator) {
        $this->contacts = $simplecontact;
        $this->validator = $validator;
    }

    public static function create(ContainerInterface $container) {
        return new static($container->get('simplecontact.contacts'), $container->get('simplecontact.contactsvalidator'));
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'simplecontact_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $item = ['country' => $this->contacts->defaultCountry, 'status' => $this->contacts->defaultStatus];
        $form['firstname'] = [
            '#type' => 'textfield',
            '#title' => $this->t('First Name'),
            '#size' => 60,
            '#maxlength' => 60,
            '#required' => TRUE,
        ];

        $form['lastname'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Last Name'),
            '#size' => 60,
            '#maxlength' => 60,
            '#required' => TRUE,
        ];
        
        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('Email'),
            '#size' => 60,
            '#maxlength' => 60,
            '#required' => TRUE,
        ];
        
        $form['phonenumber'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Phone Number'),
            '#size' => 60,
            '#maxlength' => 60,
            '#required' => TRUE,
        ];
        
        $form['country'] = [
            '#type' => 'select',
            '#title' => $this->t('Country'),
            '#options' => $this->contacts->countryManager->getList(),
            '#default_value' => $item['country'],
            '#required' => TRUE,
        ];
        
        $form['city'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City'),
            '#size' => 60,
            '#maxlength' => 60,
            '#required' => TRUE,
        ];
        
        $form['street'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Street Name'),
            '#size' => 60,
            '#maxlength' => 60,
            '#required' => TRUE,
        ];

        $form['streetnumber'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Street Number'),
            '#size' => 60,
            '#maxlength' => 10,
            '#required' => TRUE,
        ];

        $form['zip'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Zip'),
            '#size' => 60,
            '#maxlength' => 10,
            '#required' => TRUE,
        ];
        
        $form['actions'] = ['#type' => 'actions'];
        $form['actions']['submit'] = ['#type' => 'submit', '#value' => $this->t('Submit')];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        //email
        $email = $form_state->getValue('email');
        $contact = $this->contacts->contactsGetItemsbyEmail($email);
        if (!empty($contact)) {
            $form_state->setErrorByName('email', $this->t('Email adress already exist.'));
        }

        //phonenumber
        $phonenumber = $form_state->getvalue('phonenumber');
        if (!$this->validator->validatePhoneNumber($phonenumber)) {
            $form_state->setErrorByName('phonenumber', $this->t('Invalid Phone Number.'));
        }

        //streetnumber
        $streetnumber = $form_state->getvalue('streetnumber');
        if (!$this->validator->validateStreetNumber($streetnumber)) {
            $form_state->setErrorByName('streetnumber', $this->t('Invalid Street Number.'));
        }

        //zip
        $zip = $form_state->getvalue('zip');
        if (!$this->validator->validateZip($zip)) {
            $form_state->setErrorByName('zip', $this->t('Invalid Zip Code.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $uid = \Drupal::currentUser()->id();
        $fields = ['firstname' => $form_state->getValue('firstname'),
            'lastname' => $form_state->getValue('lastname'),
            'email' => $form_state->getValue('email'),
            'phonenumber' => $form_state->getValue('phonenumber'),
            'country' => $form_state->getValue('country'),
            'city' => $form_state->getValue('city'),
            'street' => $form_state->getValue('street'),
            'streetnumber' => $form_state->getValue('streetnumber'),
            'zip' => $form_state->getValue('zip'),
            'status' => $this->contacts->defaultStatus,
            'created' => REQUEST_TIME,
            'updated' => REQUEST_TIME,
            'uid' => $uid,
        ];
        $this->contacts->contactsAdd($fields);
        \Drupal::messenger()->addMessage('Contact added successfully.');
    }

}
