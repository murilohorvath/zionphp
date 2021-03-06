/*
 * Autor Vinicius Cesar Dias
 */

// variaveis globais
var ajaxFormRunning = false;

var zevents = new Array();
function addZeventListener(eventName,callback){
	var event = {
		name: eventName,
		callback: callback
	}
	zevents.push(event);
}

function fireZevent(eventName, obj){
	for(var i in zevents){
		if(zevents[i].name == eventName){
			zevents[i].callback(obj);
		}
	}
}

// carregamento da página
$(document).ready(function(){
	$(".button-delete").click(function(){
		var url = $(this).attr("data-url");
		
		// caso a url esteja vazia, usa a url atual
		
		swal({
		  title: "Você tem certeza?",
		  text: "Uma vez deletado, não há como desfazer",
		  icon: "warning",
		  buttons: true,
		  dangerMode: true
		})
		.then((willDelete) => {
		  if (willDelete) {
			  $.ajax({
				  url: url,
				  method: "DELETE",
				  cache: false
			  }).done(function(){
				  swal({
					text: "Registro removido", 
					icon: "success"
				  });
				  
				  $(".swal-button--confirm").click(function(){
					  window.close();
				  });
			  }).fail(function(){
				  swal("Erro em remover registro", { icon: "error",});
			  });
		  } else {
		    swal("Cancelado");
		  }
		});
	});
	
	$(".button-close").click(function(){
		window.close();
	});
	
	// efeito exibir / ocultar seta ir para o topo
	jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > 200) {
            jQuery('#go-to-top').fadeIn(200);
        } else {
            jQuery('#go-to-top').fadeOut(200);
        }
    });
	
	// upload de arquivos via ajax
	(function($) {
	$.fn.serializefiles = function() {
	    var obj = $(this);
	    /* ADD FILE TO PARAM AJAX */
	    var formData = new FormData();
	    $.each($(obj).find("input[type='file']"), function(i, tag) {
	        $.each($(tag)[0].files, function(i, file) {
	            formData.append(tag.name, file);
	        });
	    });
	    var params = $(obj).serializeArray();
	    $.each(params, function (i, val) {
	        formData.append(val.name, val.value);
	    });
	    return formData;
	};
	})(jQuery);
	
    // Script para clicar no link na mesma pagina e deslocar suavemente
    // Select all links with hashes
    $('a[href*="#"]')
      // Remove links that don't actually link to anything
	  .not('[href="#"]')
	  .not('[href="#0"]')
	  .click(function(event) {
	    // On-page links
	    if (
	      location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
	      && 
	      location.hostname == this.hostname
	    ) {
	      // Figure out element to scroll to
	      var target = $(this.hash);
	      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
	      // Does a scroll target exist?
	      if (target.length) {
	        // Only prevent default if animation is actually gonna happen
	        event.preventDefault();
	        $('html, body').animate({
	          scrollTop: target.offset().top
	        }, 1000, function() {
	          // Callback after animation
	          // Must change focus!
	          var $target = $(target);
	          $target.focus();
	          if ($target.is(":focus")) { // Checking if the target was focused
	            return false;
	          } else {
	            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
	            $target.focus(); // Set focus again
	          };
	        });
	      }
	    }
    });
    
    // mascaras
    loadMask();
    
    // bloqueando UI
    jQuery.ajaxSetup({
    	beforeSend: function() {
    		startLoading();
	    },
	    complete: function(){
	    	stopLoading();
	    },
	    success: function() {}
	});
});

$(document).on("submit",".ajaxform",function(e){
	e.preventDefault();
	
	if(ajaxFormRunning){
		return false;
	}
	ajaxFormRunning = true;
	
	var form = $(this);
	if(!form.length){
		ajaxFormRunning = false;
		alert("Nenhum formulário encontrado!");
		return false;
	}
	
	var callbackFunctionName = form.attr("data-callback");
	if(callbackFunctionName == "" || callbackFunctionName == undefined){
		ajaxFormRunning = false;
		alert("Função de callback 'data-callback' não definida!");
		return false;
	}
	
	// lista de callback
	var callbackList = callbackFunctionName.split(" ");
	
	startLoading();
	var formdata = form.serializefiles();
    $.ajax({
      type: form.attr('method'),
      url: form.attr('action'),
      data: formdata,
      cache: false,
      processData: false,
      contentType: false
    }).done(function(responseBody,statusText,responseObj) {
    	stopLoading();
    	ajaxFormRunning = false;
    	
    	// callback
    	for(var i in callbackList){
    		var func = callbackList[i];
    		try {
    			eval(func+"('done',responseBody,statusText,responseObj);");
    		}catch(e){
    			console.log("Função "+func+" não encontrada, crie a função com a assinatura: "+func+"(type,responseBody,statusText,responseObj)");
    		}
    	}
    }).fail(function(responseObj,statusText,responseBody) {
    	stopLoading();
    	ajaxFormRunning = false;
    	
    	// callback
    	for(var i in callbackList){
    		var func = callbackList[i];
    		try {
    			eval(func+"('fail',responseBody,statusText,responseObj);");
    		}catch(e){
    			console.log("Função "+func+" não encontrada, crie a função com a assinatura: "+func+"(type,responseBody,statusText,responseObj)");
    		}
    	}
    });
    
    return false;
});

// funções
function startLoading(){
	if(!$("#zion-loading").length){
		var code = "<div id='zion-loading'></div>";
		$("body").append(code);
	}
	$("#zion-loading").css("display","block");
}

function stopLoading(){
	$("#zion-loading").css("display","none");
}

function copyToClipboard(text) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val(text).select();
  document.execCommand("copy");
  $temp.remove();
}

function loadMask(){
	$(".type-float").keypress(function (evt) {
		var separators  = [46,44];
		var isNumber    = (evt.which >= 48 && evt.which <= 57);
		var isSeparator = separators.indexOf(evt.which) != -1;
		
		if(!isNumber && !isSeparator){
			evt.preventDefault();
		}
	});
	
	$(".type-integer").keypress(function (evt) { 
		if (evt.which < 48 || evt.which > 57){
	        evt.preventDefault();
	    }
	});
	
	$('.date,.type-date').mask('00/00/0000');
    $('.time,.type-time').mask('00:00:00');
    $('.datetime,.type-datetime').mask('00/00/0000 00:00:00');
    $('.cep').mask('00000-000');
    $('.phone').mask('0000-0000');
    $('.phone_with_ddd').mask('(00) 0000-0000');
    $('.phone_us').mask('(000) 000-0000');
    $('.mixed').mask('AAA 000-S0S');
    $('.cpf').mask('000.000.000-00', {reverse: true});
    $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
    $('.money').mask('000.000.000.000.000,00', {reverse: true});
    $('.money2').mask("#.##0,00", {reverse: true});
    $('.ip_address').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
      translation: {
        'Z': {
          pattern: /[0-9]/, optional: true
        }
      }
    });
    $('.ip_address').mask('099.099.099.099');
    $('.percent').mask('##0,00%', {reverse: true});
    $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
    $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
    $('.fallback').mask("00r00r0000", {
        translation: {
          'r': {
            pattern: /[\/]/,
            fallback: '/'
          },
          placeholder: "__/__/____"
        }
      });
    $('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});
    
    var SPMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },
    spOptions = {
        onKeyPress: function (val, e, field, options) {
            field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
    };
    $('.phone9').mask(SPMaskBehavior, spOptions);
}

function setCookie(name, value, days) {
    var d = new Date;
    d.setTime(d.getTime() + 24*60*60*1000*days);
    document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
}

function getCookie(name) {
    var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
    return v ? v[2] : null;
}

function deleteCookie(name) {
	if(getCookie(name) != null){
		setCookie(name, '', -1);
	}
}