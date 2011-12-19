(function($) {
    $.fn.formGenerator = function(options) {
        var settings = $.extend( {
            'toggleFieldValues' : true,
            'activeClass' : 'active-fieldset',
            'isValid': false,
            'thankYouDiv': false
        }, options);

        var theForm = $(this);

        var methods = {
            init : function( ) {
                if(settings.toggleFieldValues === true){
                    methods.toggleFieldValues();
                }
                methods.removeEmptyFieldsets();
                methods.useStages();
            },
            resetButton : function( ) {
                theForm[0].reset();
                if($('fieldset.activeFieldset', theForm)){
                    $('fieldset', theForm).each(function(){
                        if($(this).hasClass(settings.activeClass)){
                            $(this).removeClass(settings.activeClass).hide();
                        }
                    })
                    $('fieldset:first', theForm).addClass(settings.activeClass).show();
                }
            },
            toggleFieldValues : function( ) {
                $('input', theForm).each(function(){
                    var orig_val = $(this).attr('title');
                    var input_val = $(this).val();
                    $(this).focus(function(){
                        if(input_val == orig_val){
                            $(this).val('');
                        }
                    });
                    $(this).blur(function(){
                        if($(this).val() == ''){
                            $(this).val(orig_val);
                        }
                    });
                });
            },
            removeEmptyFieldsets: function(){
                $('fieldset', this).each(function(e){
                    if(e.html() == '' || e.text() == '' || e.is(':empty') || e.html().length < 1){
                        e.remove();
                    }
                });
            },
            useStages : function() {
                $('input[type="submit"]', theForm).appendTo(theForm);
                $('fieldset:first', theForm).addClass(settings.activeClass);
                $('fieldset:not(.active-fieldset)', theForm).hide();
                $('input[type="submit"]', theForm).bind({
                    mouseup: function(){
                        methods.stepStages();
                        return false;
                    },
                    submit: function(){
                        return false;
                    },
                    click: function(){
                        return false;
                    }
                });
                $('fieldset:last', theForm).addClass('last-fieldset');
                return false;
            },
            stepStages: function(){
                if(settings.isValid === true){
                    var current_fieldset = $('fieldset.active-fieldset', theForm);
                    var last_fieldset = $('fieldset:last', theForm);
                    if(last_fieldset.hasClass('activeFieldset')){
                        methods.send_ajax();
                    } else {
                        current_fieldset.removeClass(settings.activeClass).hide();
                        current_fieldset.next('fieldset').addClass(settings.activeClass).show();
                        //add a new class to the submit button so we can change it's style on staged forms
                        if($('fieldset.activeFieldset', theForm).hasClass('last-fieldset')){
                            $('input[type="submit"]', theForm).addClass('final_stage');
                        }
                    }
                    return settings.isValid = true;
                }else{
                    send_ajax(true);
                    return settings.isValid = false;
                }
            },
            isValid : function(){
                var hasErrors = 0;
                $('fieldset', theForm).hasClass(settings.activeClass).hasClass('invalid').each(function(e){
                    if(e.css('display') == 'none' || e.length == 0){
                    } else {
                        hasErrors++;
                    }
                });

                if(hasErrors >= 1 ){
                    return false;
                } else {
                    return settings.isValid = true;
                }
            },
            sendAjax: function(errors){
                var formData = methods.get_form_data();
                formData['error'] = 1;
                $.ajax({
                    type: "POST",
                    url: theForm.attr('action'),
                    data: formData,
                    success: methods.callback()
                });
                return false;
            },
            getFormData: function(){
                var form_values = {};
                $(form_id+' input, select, textarea').each(function(){
                    form_values[this.name] = this.value;
                });
                $(form_id+' input[type="radio"]:checked, input[type="checkbox"]:checked').each(function(){
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
            },
            callback: function(){
                theForm.html(methods.getThankYouHTML());
                $(document).trigger('formSuccess');
            },
            getThankYouHTML: function(){
                if(settings.thankYou != false){
                    thank_html = settings.thankYou.html();
                    if(typeof(thank_html) == "undefined" || thank_html == null || thank_html == ''){
                        return false;
                    } else {
                        return thank_html;
                    }
                }else {
                    return false;
                }
            }
        };
        methods.init();
    };
})( $ );

// Prevents enter keypress from submitting form - need for additional validation event
$("form").bind("keypress", function(e) {
    if (e.keyCode == 34) return false;
});