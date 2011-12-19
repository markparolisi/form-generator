<?php

class Form_Generator {

    public $iniFile = '../ini/default.ini';
    public $iniArray = array ();
    public $formId = 'default';
    public $formMethod = 'post';
    public $cssClass = 'global';
    public $formAction = 'index.php';
    public $inputFields = 'No Fields';
    public $validationArray = array ();
    public $jqueryValidationString = 'No Validation Set';
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
        $this->formInputs ();
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

    private function formInputs() {
        if (class_exists ( 'Form_Inputs' ) && $this->iniArray) {
            $inputs = new Form_Inputs ( $this->iniArray );
            $this->inputFields = $inputs->getFormInputs();
            $this->validationArray = $inputs->formValidation;
        } else {
            $this->errors ['FormInputs'] = "Could not fetch inputs from FormInputs Class";
            return false;
        }
    }

    private function openForm() {
        return '<form id="' . $this->formId . '" action="' . $this->formAction . '" method="' . $this->formMethod . '" class="' . $this->cssClass . '">';
    }


    //instantiate the validation class to create the rules from the INI values
    public function renderJqueryFormValidation() {
        $formValidation = new Form_Validation ( $this->validationArray, $this->formId );
        $this->jqueryValidationString = $formValidation->createJqueryValidationCode ();
    }

    // concatenate the values into one complete form
    public function renderForm() {
        return  $this->openForm() . "\n" . $this->inputFields . "</form>\n";
    }

    public function generateFormJS() {
        $domain = JS_DIR;
        $formJS = <<<HEREDOC
                <script type="text/javascript" src="{$domain}jquery.min.js"></script>
                <script type="text/javascript" src="{$domain}jquery.validate.min.js"></script>
                $this->jqueryValidationString
                <script type="text/javascript" src="{$domain}formGenerator.js"></script>
                    <script type="text/javascript">
                    jQuery(document).ready(function($){
                        $('#{$this->formId}').formGenerator();
                    });
                    </script>
HEREDOC;
        return $formJS;
    }

    public function printFormJS() {
        echo $this->generateFormJS();
    }

}

// End Form Class
