<?php

class Form_Validation {

    public $validationArray = array();
    public $formId = 'default';

    public function __construct($validationArray, $formId) {
        $this->validationArray = $validationArray;
        $this->formId = $formId;
    }

    public function createJqueryValidationCode() {
        $validatable_fields = $this->validationArray;
        ksort($validatable_fields);
        $validation_rules = '';
        $validation_msgs = '';
        foreach($validatable_fields as $validatable_field => $type) {
            if(end($validatable_fields) != $validatable_field) {
                $comma = ',';
            } else {
                $comma = '';
            }
            if($validatable_field == 'key3' || $validatable_field == 'state')
                $type = 'firstvalue';

            switch($type) {
                case 'firstname':
                    $rules = "\t\t\t\t minlength: 2,";
                    $error_msg = 'Please enter First name.';
                    break;
                case 'lastname':
                    $rules = "\t\t\t\t minlength: 2,";
                    $error_msg = 'Please enter Last name.';
                    break;
                case 'email':
                    $rules = "\t\t\t\t email: true,";
                    $error_msg = 'Please enter a valid E-Mail Address';
                    break;
                case 'phone':
                    $rules = "\t\t\t\t phoneUS: true,";
                    $error_msg = 'Please enter a valid Phone Number';
                    break;
                case 'phoneIntl':
                    $rules = "\t\t\t\t phoneIntl: true,";
                    $error_msg = 'Please enter a valid Phone Number';
                    break;
                case 'fullname':
                    $rules = "\t\t\t\t fullname: true,";
                    $error_msg = 'Please enter your Full Name';
                    break;
                case 'zipcode':
                    $rules = "\t\t\t\t digits: true,";
                    $error_msg = 'Please enter a valid Zip Code';
                    break;
                case 'alphanumeric':
                    $rules = "\t\t\t\t minlength: 2,";
                    $error_msg = 'This Field is Invalid';
                    break;
                case 'firstvalue':
                    $rules = "\t\t\t\t notEqualTo: jQuery('#$this->formId #$validatable_field option:first').val(),";
                    $error_msg = 'Please Select an Option';
                    break;
                case 'required':
                    $rules = "\t\t\t\t minlength: 1,";
                    $error_msg = 'This Field is Required';
                    break;
                default:
                    $rules = '';
                    $error_msg = 'This Field is Invalid';
                    break;
            }
            $validation_rules .= "\t\t\t\t\t\t{$validatable_field}: {
                    $rules
            \t\t\t\t\t notTitle: jQuery('#$this->formId #$validatable_field').attr('title'),
            \t\t\t\t\t required: true \n";
            $validation_rules .= "\t\t\t\t\t\t }{$comma} \n";
            $validation_msgs .= "\t\t\t\t\t\t{$validatable_field}: '" . $error_msg . "'{$comma}\n";
        }

        $jq_validation_code = <<<HEREDOC
        <script type='text/javascript'>
         jQuery(document).ready(function($){
         \t jQuery('#$this->formId #submit_form').bind('mousedown', function(){
         \t\t jQuery('.activeFieldset').children('input, select, checkbox').each(function(){
         \t\t\t var inputID = $(this).attr('id');
         \t\t\t jQuery('#$this->formId').validate({
         \t\t\t\t errorClass: 'invalid',
         \t\t\t\t rules: {
         \n $validation_rules
         \t\t\t\t }, //end rules
         \t\t\t\t messages: {
         \n $validation_msgs
         \t\t\t\t }, //end messages
         \t\t\t\terrorPlacement: function(error, element) {
                                \t\telement.before(error);
                                \t} //end moving of invalid labels
         \t\t\t }).element('#'+inputID); //end validate()
         \t\t }); //end each()
         \t }); //end bind()
         }); //end doc ready()
         </script>\n
HEREDOC;
        return $jq_validation_code;
    }

}

// End Validation Class