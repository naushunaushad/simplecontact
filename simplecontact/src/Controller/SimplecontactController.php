<?php

namespace Drupal\simplecontact\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\simplecontact\Contacts;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * simplecontact main controller
 */
class SimplecontactController extends ControllerBase {

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
     * 
     * @return themed output
     */
    public function simplecontactList() {
        return $this->contacts->contactsGetList();
    }

    /**
     * 
     * export contacts in csv file
     */
    public function simplecontactExport() {
        $head = [$this->t('First Name'),
            $this->t('Last Name'),
            $this->t('Email'),
            $this->t('Phonenumber'),
            $this->t('Country'),
            $this->t('City'),
            $this->t('Sreet Name'),
            $this->t('Street Number'),
            $this->t('Zip'),
            $this->t('Status'),
            $this->t('Created'),
            $this->t('Updated'),
            $this->t('Created By')];
        $result = $this->contacts->contactsGetItems();
        $export = implode(",", $head) . "\r\n";
        if (!empty($result)) {
            foreach ($result as $items) {
                $export.= $items->firstname . "," . $items->lastname . "," . $items->email . "," . $items->phonenumber . "," . $items->country_name . "," . $items->city . "," . $items->street . "," . $items->streetnumber . "," . $items->zip . "," . $items->status_text . "," . $items->created_date . "," . $items->updated_date . "," . $items->owner . "\r\n";
            }
        }
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename=" . "simplecontact_export_" . date("Y-m-d") . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $export;
        exit;
    }

}
