<?php

namespace Drupal\simplecontact;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides validator for simplecontacts
 */
class ContactsValidator {

    use StringTranslationTrait;

    /**
     * 
     * @param type $street
     * @return boolean
     */
    public function validateStreetNumber($street) {
        $return = FALSE;
        if (strlen($street) <= 9) {
            $return = TRUE;
        }
        return $return;
    }

    /**
     * 
     * @param type $zip
     * @return boolean
     */
    public function validateZip($zip) {
        $return = FALSE;
        if (preg_match('/^[0-9]{5,6}$/', $zip)) {
            $return = TRUE;
        }
        return $return;
    }

    /**
     * 
     * @param type $phone
     * @return boolean
     */
    public function validatePhoneNumber($phone) {
        $return = FALSE;
        if (preg_match('/^[0-9]{8,15}$/', $phone)) {
            $return = TRUE;
        }
        return $return;
    }

}
