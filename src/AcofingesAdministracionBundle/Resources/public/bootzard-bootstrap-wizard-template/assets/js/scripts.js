
function scroll_to_class(element_class, removed_height) {
	var scroll_to = $(element_class).offset().top - removed_height;
	if($(window).scrollTop() != scroll_to) {
		$('html, body').stop().animate({scrollTop: scroll_to}, 0);
	}
}

function bar_progress(progress_line_object, direction) {
	var number_of_steps = progress_line_object.data('number-of-steps');
	var now_value = progress_line_object.data('now-value');
	var new_value = 0;
	if(direction == 'right') {
		new_value = now_value + ( 100 / number_of_steps );
	}
	else if(direction == 'left') {
		new_value = now_value - ( 100 / number_of_steps );
	}
	progress_line_object.attr('style', 'width: ' + new_value + '%;').data('now-value', new_value);
}

jQuery(document).ready(function() {
	
    /*
        Fullscreen background
    */
    $.backstretch("assets/img/backgrounds/1.jpg");
    
    $('#top-navbar-1').on('shown.bs.collapse', function(){
    	$.backstretch("resize");
    });
    $('#top-navbar-1').on('hidden.bs.collapse', function(){
    	$.backstretch("resize");
    });
    
    /*
        Form
    */
    $('.f1 fieldset:first').fadeIn('slow');
    
    $('.f1 input[type="text"], .f1 input[type="password"], .f1 textarea, .f1 input[type="radio"]').on('focus', function() {
    	$(this).removeClass('input-error');
    });
     $('.f1 input[type="radio"]').on('change', function() {
         $('input[name=tipo]').parent().removeClass('input-error');
         $('input[name=empleo]').parent().removeClass('input-error');
         $('input[name=ingresos]').parent().removeClass('input-error');
    });
    // next step
    $('.f1 .btn-next').on('click', function() {
    	var parent_fieldset = $(this).parents('fieldset');
    	var next_step = true;
    	// navigation steps / progress steps
    	var current_active_step = $(this).parents('.f1').find('.f1-step.active');
    	var progress_line = $(this).parents('.f1').find('.f1-progress-line');
    	
    	// fields validation
        // 
    	parent_fieldset.find('input[type="text"], input[type="password"], textarea').each(function() {
    		if( $(this).val() == "") {
    			$(this).addClass('input-error');
    			next_step = false;
    		}
    		else {
    			$(this).removeClass('input-error');
    		}
    	});
        if(current_active_step.attr('id')=='paso1'){
            if (!parent_fieldset.find('input[name=tipo]:checked').val() ) {
                $('input[name=tipo]').parent().addClass('input-error');
                next_step = false;
            }
            else {
                $('input[name=tipo]').parent().removeClass('input-error');
            }

     }
     if(current_active_step.attr('id')=='paso2'){
        /*Agregando validacion de edad*/
        if ((parent_fieldset.find('input[id=f1-dui]').val()).length < 10 ) {
            BootstrapDialog.show({
                title: '<h4>Advertencia</h4>',
                message: '<h5 class="text-danger">El número de DUI debe contener 9 dígitos</h5>',
                type: BootstrapDialog.TYPE_DANGER,
                buttons: [{
                label: 'Ok',
                cssClass: 'btn-danger btn-previous',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
            });
            return false;
        }
        if ((parent_fieldset.find('input[id=f1-nit]').val()).length < 17 ) {
            BootstrapDialog.show({
                title: '<h4>Advertencia</h4>',
                message: '<h5 class="text-danger">El número de NIT debe contener 14 dígitos</h5>',
                type: BootstrapDialog.TYPE_DANGER,
                buttons: [{
                label: 'Ok',
                cssClass: 'btn-danger btn-previous',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
            });
            return false;
        }
        if (parent_fieldset.find('input[id=f1-edad]').val()> 65 ) {
                BootstrapDialog.show({
                title: '<h4>Advertencia</h4>',
                message: '<h5 class="text-danger">No aplica por condiciones de asegurabilidad, la edad máxima debe de ser 65 años. ¿Podemos ayudarle? Contáctenos al 2561-2400</h5>',
                type: BootstrapDialog.TYPE_DANGER,
                buttons: [{
                label: 'Ok',
                cssClass: 'btn-danger btn-previous',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
            });
            return false;
         }
     }   
        if(current_active_step.attr('id')=='paso3'){
            if (!parent_fieldset.find('input[name=empleo]:checked').val() ) {
                $('input[name=empleo]').parent().addClass('input-error');
                next_step = false;
            }
            else {
                $('input[name=empleo]').parent().removeClass('input-error');
            }
        }
        if(current_active_step.attr('id')=='paso4'){
            if (!parent_fieldset.find('input[name=ingresos]:checked').val() ) {
                $('input[name=ingresos]').parent().addClass('input-error');
                next_step = false;
            }
            else {
                $('input[name=ingresos]').parent().removeClass('input-error');
            }
            /*Realizando cálculos*/
            var valor = $("input[name='f1-ingresos']").val();
            if (parent_fieldset.find('input[id=sal]:checked').val() ) {
                var monto =valor*12;
                $("#td1").html(Math.round(monto*100)/100);
            }else if(parent_fieldset.find('input[id=neg]:checked').val() ){
                var monto =valor*9;
                $("#td1").html(Math.round(monto*100)/100);
            }else{
                var monto =valor*10;
                $("#td1").html(Math.round(monto*100)/100);
            }
        }

    	// fields validation
    	
    	if( next_step ) {
    		parent_fieldset.fadeOut(400, function() {
    			// change icons
    			current_active_step.removeClass('active').addClass('activated').next().addClass('active');
    			// progress bar
    			bar_progress(progress_line, 'right');
    			// show next step
	    		$(this).next().fadeIn();
	    		// scroll window to beginning of the form
    			scroll_to_class( $('.f1'), 20 );
	    	});
    	}
    	
    });
    
    // previous step
    $('.f1 .btn-previous').on('click', function() {
    	// navigation steps / progress steps
    	var current_active_step = $(this).parents('.f1').find('.f1-step.active');
    	var progress_line = $(this).parents('.f1').find('.f1-progress-line');
    	$('input[name=tipo]').parent().removeClass('input-error');
        $('input[name=empleo]').parent().removeClass('input-error');
        $('input[name=ingresos]').parent().removeClass('input-error');
    	$(this).parents('fieldset').fadeOut(400, function() {
    		// change icons
    		current_active_step.removeClass('active').prev().removeClass('activated').addClass('active');
    		// progress bar
    		bar_progress(progress_line, 'left');
    		// show previous step
    		$(this).prev().fadeIn();
    		// scroll window to beginning of the form
			scroll_to_class( $('.f1'), 20 );
    	});
    });
    
    // submit
    $('.f1').on('submit', function(e) {
    	
    	// fields validation
    	$(this).find('input[type="text"], input[type="password"], textarea, input[type="radio"],').each(function() {
    		if( $(this).val() == "" ) {
    			e.preventDefault();
    			$(this).addClass('input-error');
    		}
    		else {
    			$(this).removeClass('input-error');
    		}
    	});
    	// fields validation
    	
    });
    
    
});
