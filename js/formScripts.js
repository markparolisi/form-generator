// Prevents enter keypress from submitting form - need for additional validation event
jQuery("form").bind("keypress", function(e) {
    if (e.keyCode == 34) return false;
});

// Attach this to the reset button in form
function reset_button(form_id){
    jQuery(form_id)[0].reset();
    //if we use the staged for this will reset our position.
    if(jQuery(form_id+' fieldset.activeFieldset')){
        jQuery(form_id+' fieldset.activeFieldset').removeClass('activeFieldset').hide();
        jQuery(form_id+' fieldset:first').addClass('activeFieldset').show();
    }
}

// Hide default value onFocus, restore onBlur no value present. Element must have a title attribute
function toggleFieldValues(form_id){
    jQuery(form_id+' input:not("#submit_form"),'+form_id+' input:not("#reset_form"), '+form_id+' textarea').each(function(){
        jQuery(this).focus(function(){
            var orig_val = jQuery(this).attr('title');
            var input_val = jQuery(this).val();
            if(input_val == orig_val){
                jQuery(this).val('');
            }
        });
        jQuery(this).blur(function(){
            var orig_val = jQuery(this).attr('title');
            var input_val = jQuery(this).val();
            if(jQuery(this).val() == ''){
                jQuery(this).val(orig_val);
            }
        });
    });
}

// Show/Hide successive fieldsets
function useStages(form_id){
    //make sure we pull out the btns from any fieldsets
    jQuery(form_id+' input[type="submit"], '+form_id+' input[type="button"] ').appendTo(form_id); 
    jQuery(form_id+ ' fieldset:first').addClass('activeFieldset');
    //remove any blank fieldsets
    jQuery(form_id+' fieldset').each(function(){
        if(jQuery(this).html() == '' || jQuery(this).text() == '' || jQuery(this).is(':empty') || jQuery(this).html().length < 1){
            jQuery(this).remove();
        }
    });
    //hide all fieldsets but our active one
    jQuery(form_id+' fieldset:not(.activeFieldset)').hide();
    //bind to the mouseup event on the submit button
    jQuery(form_id+' #submit_form').bind({
        mouseup: function(){
            stepStages(form_id);
            return false;
        },
        submit: function(){
            return false;
        },
        click: function(){
            return false;
        }
    });
    jQuery(form_id+ ' fieldset:last').addClass('lastFieldset');
    return false;
}

function isValid(form_id){
    var hasErrors = 0;

    jQuery(form_id +' fieldset.activeFieldset .invalid').each(function(){
        if(jQuery(this).css('display') == 'none' || jQuery(this).length == 0){
        //do nothing right now
        } else {
            hasErrors++;
        }
        return hasErrors;
    });

    if(hasErrors >= 1 ){
        return false;
    } else {
        return true;
    }
}

function stepStages(form_id){

    if(isValid(form_id) === true){
        var current_fieldset = jQuery(form_id+' fieldset.activeFieldset');
        var last_fieldset = jQuery(form_id+' fieldset:last');
        if(last_fieldset.hasClass('activeFieldset')){
            send_ajax(form_id);
        } else {
            current_fieldset.removeClass('activeFieldset').hide();
            current_fieldset.next('fieldset').addClass('activeFieldset').show();
            //add a new class to the submit button so we can change it's style on staged forms
            if(jQuery(form_id+' fieldset.activeFieldset').hasClass('lastFieldset')){
                jQuery(form_id+' #submit_form').addClass('final_stage');
            }
        }
    }else{
        send_ajax_errors(form_id);
        return false;
    }
    return false;
}

function send_ajax(form_id){
    action_url = jQuery(form_id).attr('action');
    form_data = get_form_data(form_id);
    jQuery.ajax({
        type: "POST",
        url: action_url,
        data: form_data,
        success: callback(form_id)
    });
    return false;

}

//function to be fired when ajax is successful
function callback(form_id){
    exe_analytics();
    thank_you_div = get_thank_you_div(form_id);
    thank_you_div_html = get_thank_you_div_html(thank_you_div, form_id);
    //this replaces the form element with the contents of the thank you div
    jQuery(form_id).html(thank_you_div_html);
    //allows the callback to be extended with another function
    if(typeof(extra_callback) == 'function'){
        extra_callback();
    }
    jQuery(document).trigger('formSuccess');

}

//Loop through our form elements and create an an associative array of each
function get_form_data(form_id){
    var form_values = {};
    jQuery(form_id+' input, select, textarea').each(function(){
        form_values[this.name] = this.value;
    });
    jQuery(form_id+' input[type="radio"]:checked, input[type="checkbox"]:checked').each(function(){
        form_values[this.name] = this.value;
    });
    //add additional data to request
    form_values['url']      = window.location.href;
    var qstring             = window.location.search.substring(1);
    var qstring_split       = qstring.split("&");
    for(i=0; i<qstring_split.length; i++){
        var value_split 	    = qstring_split[i].split("=");
        form_values[value_split[0]] = decodeURI(value_split[1]);
    }
    form_values['browser']  	= navigator.userAgent;
    form_values['submit_form'] 	= 1;
    form_values['resolution']   = screen.width+'x'+screen.height;
    delete form_values['submit_form'];
    delete form_values['reset_form'];
    return form_values;
}

function send_ajax_errors(form_id){
    var action_url = jQuery(form_id).attr('action');
    var form_data = get_form_data(form_id);
    form_data['error'] = 1;
    jQuery.ajax({
        type: "POST",
        url: action_url,
        data: form_data,
        success: function(html){

        }
    });
    if(typeof(extra_error) == 'function'){
        extra_error();
    }
}

function exe_analytics(){

    // All of the Google Tracker names we use
    trackers = new Array('pageTracker','firstTracker','secondTracker','thirdTracker','fourthTracker','fifthTracker','sixthTracker','seventhTracker','eighthTracker','ninthTracker','tenthTracker','eleventhTracker','twelfthTracker','thirteenthTracker','fourteenthTracker','fifteenthTracker');

    // Find  the value for the tracker(s) - thank, thankheader, thankpage, done1
    goal = '';
    hidden_goal = '';
    try{
        hidden_goal = jQuery('#goal_tracker').val();
    }catch(e){ }
    if(hidden_goal != ''){
        goal = hidden_goal;
    }

    // New Google trackers
    try{
        eval("_gaq.push(['_trackPageview','"+goal+"'])");
    }catch(e){ }

    // Old Google trackers
    for(var i = 0; i < trackers.length; i++){
        try{
            eval(trackers[i]+"._trackPageview('"+goal+"')");
        }catch(e){ }
    }

    return goal;
}

//Convert the value of the concentration/program to the ID of the thank you div
function get_thank_you_div(form_id){
    concVal 		= jQuery(form_id+' #concentration').val();
    key3Val 		= jQuery(form_id+' #key3').val();
    thank_you_div	= key3Val;
    if(typeof(concVal) != "undefined"){
        thank_you_div = concVal;
    }
    if(thank_you_div != "undefined" && thank_you_div.length > 0){
        thank_you_div = thank_you_div.replace(/\ /g,'_');
        return thank_you_div;
    } else {
        return false;
    }
}

//fetch the contents of the thank you div
function get_thank_you_div_html(thank_you_div, form_id){
    if(thank_you_div != false){
        thank_html = jQuery('#'+thank_you_div).html();
        if(typeof(thank_html) == "undefined" || thank_html == null || thank_html == ''){
            thank_html = jQuery('#default_thankyou').html();
        } else {
            thank_html = jQuery('#'+thank_you_div).html();
        }
        return thank_html;
    }else {
        return false;
    }
}

function ckgFormInit(id){
    useStages(id);
    toggleFieldValues(id);
}
