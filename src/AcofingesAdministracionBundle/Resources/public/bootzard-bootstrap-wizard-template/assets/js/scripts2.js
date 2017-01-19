
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
         $('input[name=codeudor]').parent().removeClass('input-error');
    });
    function validarEmail( email ) {
        expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if ( !expr.test(email) ){
            return 0; /*Email incorrecto*/
        }else{
            return 1;/*Email correcto*/
        }
    }
   /* function tipoIngreso(){
        var tipoIngresos=$('input:radio[name="ingresos"]').val();
        return tipoIngresos;
    }*/
    $('input:radio[name="codeudor"]').change(
    function(){
        if (this.checked && this.value == 'si') {
            $("#parentesco").show("slow");
            $("#ingresosCodeudor").show("slow");
        }
        if (this.checked && this.value == 'no') {
            $("#parentesco").hide("slow");
            $("#ingresosCodeudor").hide("slow");
        }
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
        if(current_active_step.attr('id')!='paso5'){
            	parent_fieldset.find('input[type="text"], input[type="password"], textarea').each(function() {
                            if( $(this).val() == "") {
                                $(this).addClass('input-error');
                                next_step = false;
                            }
                            else {
                                $(this).removeClass('input-error');
                            }
            	});
        }
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
        if(validarEmail(parent_fieldset.find('input[id=f1-correo]').val())==0){
            BootstrapDialog.show({
                title: '<h4>Advertencia</h4>',
                message: '<h5 class="text-danger">El correo ingresado no es valido, debe ingresarlo con el formato ####@####.###</h5>',
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

        if ((parent_fieldset.find('input[id=f1-telefono]').val()).length < 9 ) {
            BootstrapDialog.show({
                title: '<h4>Advertencia</h4>',
                message: '<h5 class="text-danger">El número de teléfono debe contener 8 dígitos</h5>',
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
                /*Agregando validacion de edad*/
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
        /*Agregando validacion de tiempo de edad*/
        if (parent_fieldset.find('input[id=f1-tiempo]').val() <= 6 ) {
                BootstrapDialog.show({
                title: '<h4>Advertencia</h4>',
                message: '<h5 class="text-danger">No aplica, debe poseer estabilidad laboral mayor a 6 meses. ¿Podemos ayudarle? Contáctenos al 2561-2400</h5>',
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
        }

        if(current_active_step.attr('id')=='paso5'){
            if (!parent_fieldset.find('input[name=codeudor]:checked').val() ) {
                $('input[name=codeudor]').parent().addClass('input-error');
                next_step = false;
            }
            else {
                $('input[name=codeudor]').parent().removeClass('input-error');
            }

            if(parent_fieldset.find('input[id=si]:checked').val()){
            parent_fieldset.find('input[type="text"], input[type="password"], textarea').each(function() {
                if( $(this).val() == "") {
                    $(this).addClass('input-error');
                    next_step = false;
                }
                else {
                    $(this).removeClass('input-error');
                }
            });
            }else{
                 $(this).removeClass('input-error');
            }
            /*Realizando cálculos*/
            var ingresos = $("input[name='f1-ingresos']").val();
            var otros = $("input[name='f1-otros']").val();
            var ingresoTotal=parseInt(ingresos)+parseInt(otros);
            if ($('input:radio[name="ingresos"]:checked').val()=='sal') {
                var monto1 =parseInt(ingresoTotal)*45;
            }else if($('input:radio[name="ingresos"]:checked').val()=='hon'){
                var monto1 =parseInt(ingresoTotal)*35;
            }else{
                var monto1 =parseInt(ingresoTotal)*40;
            }
            //Calculos codeudor
            if (parent_fieldset.find('input[id=si]:checked').val() ) {
                var ingresosCodeudor = $("input[name='f1-ingresosCodeudor']").val();
                var monto2=(parseInt(ingresosCodeudor)*45)-parseInt(monto1);
            }else{
                var monto2=0;
            }
            var montoMax=parseInt(monto1)+parseInt(monto2);
            $("#td1").html(Math.round(montoMax*100)/100);
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
        $('input[name=codeudor]').parent().removeClass('input-error');
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
