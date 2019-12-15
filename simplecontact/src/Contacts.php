<?php

namespace Drupal\simplecontact;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;
use Drupal\Core\Locale\CountryManager;
use Drupal\Core\Utility\LinkGenerator;

/**
 * Provide simplecontact functionalities
 */
class Contacts {

    use StringTranslationTrait;

    const TABLE = 'simplecontact';

    /**
     *
     * @var \Drupal\Core\Database\Connection 
     */
    private $db;

    /**
     *
     * @var \Drupal\Core\Locale\CountryManager
     */
    public $countryManager;

    /**
     *
     * @var \Drupal\Core\Utility\LinkGenerator
     */
    protected $linkGenerator;

    /**
     *
     * @var INT
     */
    protected $contactsPagelimit;

    /**
     *
     * @var String
     */
    public $adminEmail;

    /**
     *
     * @var INT
     */
    public $defaultStatus;

    /**
     *
     * @var String
     */
    public $defaultCountry;

    public function __construct(Connection $db, CountryManager $countryManager, LinkGenerator $link_generator, $contacts_pagelimit) {
        $this->db = $db;
        $this->countryManager = $countryManager;
        $this->linkGenerator = $link_generator;
        $this->contactsPagelimit = $contacts_pagelimit;
        $config = \Drupal::config('simplecontact.settings');
        $this->adminEmail = $config->get('admin_email');
        $this->defaultStatus = $config->get('default_status');
        $this->defaultCountry = $config->get('default_country');
    }

    /**
     * 
     * @param type $result
     * @return mixed
     */
    private function ContactsPrepare($result) {
        if (empty($result)) {
            return NULL;
        }
        $date_format = 'd/m/Y h:i:s a';
        $result->status_text = $result->status ? $this->t('Active') : $this->t('Inactive');
        $result->created_date = date($date_format, $result->created);
        $result->updated_date = date($date_format, $result->updated);
        $result->owner = empty($result->owner) ? $this->t('Anonymous') : $result->owner;
        $country_list = $this->countryManager->getList();
        $result->country_name = $country_list[$result->country];
        return $result;
    }

    /**
     * 
     * @param type $fields
     * @return mixed
     */
    public function contactsAdd($fields) {
        $query = $this->db->insert(self::TABLE);
        $query->fields($fields);
        $result = $query->execute();
        return $result;
    }

    /**
     * 
     * @param type $fields
     * @param INT $cid
     * @return mixed
     */
    public function contactsUpdate($fields, $cid) {
        $query = $this->db->update(self::TABLE);
        $query->fields($fields);
        $query->condition('cid', $cid);
        $result = $query->execute();
        return $result;
    }

    /**
     * 
     * @param INT $cid
     * @return mixed
     */
    public function contactsDelete($cid) {
        $query = $this->db->delete(self::TABLE);
        $query->condition('cid', $cid);
        $result = $query->execute();
        return $result;
    }

    /**
     * 
     * @param INT $cid
     * @return mixed
     */
    public function contactsGetItem($cid) {
        $query = $this->db->select(self::TABLE, 's');
        $query->innerJoin('users_field_data', 'u', 's.uid = u.uid');
        $query->fields('s');
        $query->addField('u', 'name', 'owner');
        $query->condition('s.cid', $cid);
        $result = $query->execute()->fetchObject();
        return $this->ContactsPrepare($result);
    }

    /**
     * 
     * @return mixed
     */
    public function contactsGetItems() {
        $rows = [];
        $query = $this->db->select(self::TABLE, 's');
        $query->innerJoin('users_field_data', 'u', 's.uid = u.uid');
        $query->fields('s');
        $query->addField('u', 'name', 'owner');
        $result = $query->execute()->fetchAll();
        foreach ($result as $item) {
            $rows[] = $this->ContactsPrepare($item);
        }
        return $rows;
    }

    /**
     * 
     * @param String $email
     * @return mixed
     */
    public function contactsGetItemsbyEmail($email) {
        $query = $this->db->select(self::TABLE, 's');
        $query->innerJoin('users_field_data', 'u', 's.uid = u.uid');
        $query->fields('s');
        $query->addField('u', 'name', 'owner');
        $query->condition('s.email', $email);
        $result = $query->execute()->fetchObject();
        return $this->ContactsPrepare($result);
    }

    /**
     * 
     * @param INT $uid
     * @return mixed
     */
    public function contactsGetItembyOwner($uid) {
        $rows = [];
        $query = $this->db->select(self::TABLE, 's');
        $query->innerJoin('users_field_data', 'u', 's.uid = u.uid');
        $query->fields('s');
        $query->addField('u', 'name', 'owner');
        $query->condition('s.uid', $uid);
        $result = $query->execute()->fetchAll();
        foreach ($result as $item) {
            $rows[] = $this->ContactsPrepare($item);
        }
        return $rows;
    }

    /**
     * 
     * @param INT $uid
     * @return mixed
     */
    public function contactsCountByOwner($uid) {
        $query = $this->db->select(self::TABLE, 's');
        $query->fields('s', ['sid']);
        $query->condition('s.uid', $uid);
        $result = $query->countQuery()->execute()->fetchField();
        return $result;
    }

    /**
     * 
     * @return array
     */
    public function contactsGetList() {
        $rows = [];
        $header = [
            ['data' => $this->t('First Name'), 'field' => 's.firstname'],
            ['data' => $this->t('Last Name'), 'field' => 's.lastname'],
            ['data' => $this->t('Email'), 'field' => 's.email'],
            ['data' => $this->t('Phone Number')],
            ['data' => $this->t('Country'), 'field' => 's.country'],
            ['data' => $this->t('City')],
            ['data' => $this->t('Street')],
            ['data' => $this->t('Street Number')],
            ['data' => $this->t('Zip')],
            ['data' => $this->t('Status'), 'field' => 's.status'],
            ['data' => $this->t('Created By'), 'field' => 'u.name'],
            ['data' => $this->t('Created'), 'field' => 's.created'],
            ['data' => $this->t('Updated'), 'field' => 's.updated'],
            ['data' => $this->t('Edit')],
            ['data' => $this->t('Delete')],
        ];
        $query = $this->db->select(self::TABLE, 's');
        $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit($this->contactsPagelimit);
        $query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);
        $query->innerJoin('users_field_data', 'u', 's.uid = u.uid');
        $query->fields('s');
        $query->addField('u', 'name', 'owner');
        $result = $query->execute()->fetchAll();
        foreach ($result as $item) {
            $row = $this->ContactsPrepare($item);
            $edit_url = Url::fromRoute('simplecontact.adminentry.form', ['cid' => $item->cid]);
            $delete_url = Url::fromRoute('simplecontact.delete.form', ['cid' => $item->cid]);
            $rows[] = [
                'firstname' => $row->firstname,
                'lastname' => $row->lastname,
                'email' => $row->email,
                'phonenumber' => $row->phonenumber,
                'country' => $row->country_name,
                'city' => $row->city,
                'street' => $row->street,
                'streetnumber' => $row->streetnumber,
                'zip' => $row->zip,
                'status' => $row->status_text,
                'owner' => $row->owner,
                'created' => $row->created_date,
                'updated' => $row->updated_date,
                'edit' => $this->linkGenerator->generate('edit', $edit_url),
                'delete' => $this->linkGenerator->generate('delete', $delete_url),
            ];
        }
        $build['tablesort_table'] = [
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows,
        ];
        $build['pager'] = array(
            '#type' => 'pager',
            '#tags' => array(),
        );
        return $build;
    }

}
