<?php

class Form {

    public $iniFile = '../ini/default.ini';
    public $iniArray = array ();
    public $formId = 'default';
    public $formMethod = 'post';
    public $cssClass = 'global';
    public $formAction = 'index.php';
    public $inputFields = '';
    public $formOpenString = '<form>';
    public $formCloseString = '</form>';
    public $validationArray = array ();
    public $jqueryValidationString = '';
    public $iniPath = INI_DIR;
    public $errors = array ();

    function __construct($formId = 'default', $iniPath = INI_DIR) {
        $this->iniPath = $iniPath;
        $this->setIni ( $formId );
        if ($this->iniFile) {
            $this->setIniArray ();
            $this->setFormAttributes ();
        } else {
            $this->errors ['InvalidIni'] = "Could not find {$formId}.ini";
        }
        $this->openForm ();
        $this->formInputs ();
        $this->closeForm ();
        $this->renderJqueryFormValidation ();
    }

    public function setIni($formId) {
        //find the INI file based on the path and form ID or use the default
        if (file_exists ( $this->iniPath . $formId . '.ini' )) {
            $this->iniFile = $this->iniPath . $formId . '.ini';
        } elseif (file_exists ( $this->iniPath . 'default.ini' )) {
            $this->iniFile = $this->iniPath . 'default.ini';
            $this->errors ['DefaultIni'] = "Using the default.ini";
        } else {
            $this->iniFile = false;
        }
        if (strpos ($this->iniPath, 'http' ) === 0) {
            $this->iniFile = $this->iniPath;
        }

    }

    public function setIniArray() {
        $customIniArray = parse_ini_file ( $this->iniFile, true );
        if (! $customIniArray) {
            echo "Cannot parse " . $this->iniFile;
            die ();
        }
        if (is_array ( $customIniArray ) && count ( $customIniArray ) > 1) {
            $this->iniArray = $customIniArray;
        } else {
            $this->iniArray = false;
        }
    }

    public function setFormAttributes() {
        $form_attributes = get_object_vars ( $this ); // get current form attrs
        foreach ( $this->iniArray ['form_attributes'] as $key => $value ) {
            if (array_key_exists ( $key, $form_attributes )) {
                $this->$key = $value; //update our form object attr
            } else {
                unset ( $key );
            }
        }
    }

    // private because we are testing this in the FormInputs class
    private function formInputs() {
        if (class_exists ( 'FormInputs' ) && $this->iniArray) {
            $inputs = new Form_Inputs ( $this->iniArray );
            $this->inputFields = $inputs->getFormInputs ();
            $this->validationArray = $inputs->formValidation;
        } else {
            $this->errors ['FormInputs'] = "Could not fetch inputs from FormInputs Class";
            return false;
        }
    }

    //the standard HTML form open tag with our values
    private function openForm() {
        $this->formOpenString = '<form id="' . $this->formId . '" action="' . $this->formAction . '" method="' . $this->formMethod . '" class="' . $this->cssClass . '">';
    }

    private function closeForm() {
        $this->formCloseString = '</form>';
    }

    //instantiate the validation class to create the rules from the INI values
    public function renderJqueryFormValidation() {
        $formValidation = new Form_Validation ( $this->validationArray, $this->formId );
        $this->jqueryValidationString = $formValidation->createJqueryValidationCode ();
    }

    // concatenate the values into one complete form
    public function renderForm() {
        return $this->formOpenString . "\n" . $this->inputFields . $this->formCloseString . "\n";
    }

    public function generateFormJS($jquery = true) {
        $formJS = "\n";
        $domain = '';
        if (strpos ( $this->iniPath, 'http' ) === 0) {
            $domain = 'http://form.ldev.client-store.com';
        }
        if ($jquery) {
            $formJS .= "\n" . '<script type="text/javascript" src="' . $domain . JS_DIR . 'jquery.min.js"></script>' . "\n";
        }
        $formJS .= "\n" . '<script type="text/javascript" src="' . $domain . JS_DIR . 'jquery.validate.min.js"></script>' . "\n";
        $formJS .= $this->jqueryValidationString . "\n";
        $formJS .= '<script type="text/javascript" src="' . $domain . JS_DIR . 'formScripts.js"></script>' . "\n";
        return $formJS;
    }

    public function printFormJS($jquery = true) {
        if ($jquery == true) {
            echo $this->generateFormJS ();
        } else {
            echo $this->generateFormJS ( false );
        }
    }

}

// End Form Class
