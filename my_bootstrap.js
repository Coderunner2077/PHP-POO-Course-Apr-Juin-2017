jQuery(document).ready(function() {
	
	if($('body div.container').length == 0) {
		$('body').find(":not(iframe)").addBack().contents().filter(function() {
			if(this.nodeType == 3 && /\n/.test(this.nodeValue)) {
				var newValue = this.nodeValue.replace(/(?:\n|\t)-/g, '<br />-').replace(/([._!?;{])\n/g, '$1<br/><br/>').replace(/\n\n/g, '<br /><br />');
				if(/\/\/|\*/.test(newValue))
					newValue = newValue.replace(/\n/g, '<br />');
				$(this).replaceWith(newValue);
			}
			else if(this.nodeName.toUpperCase() == 'STRONG' || this.nodeName.toUpperCase() == 'B') {
				if(/^\n/.test(this.textContent))
					$(this).wrap('<pre></pre>');
			}
		});
		$('body').wrapInner('<div class="container text-justify"></div>');
		$('div script').each(function() {
			$(this).appendTo('body');
		});
	}
	$('.aw').addClass('alert alert-warning');
	$('.ai').addClass('alert alert-info');
	$('a').attr('target', '_blank').css('color', '#ff1500');
	$('figure').addClass('text-center').css('margin', '30px');
	$('b').not('.alert b').css('background-color', '#dee').css('border-radius', '3px').css('padding', '2px 5px');
	$('img').each(function() {
		var src = this.src;
		if(/\/img\//.test(src) == false) {
			src = src.substr(0, src.lastIndexOf('/') + 1) + 'img' + src.substr(src.lastIndexOf('/'));
			this.src = src;
		}
	});
	$('table').addClass('table-bordered').find('td').css('border-width', '2px');
	$('.popoverS').popover({placement: 'top', trigger: 'hover'});
	$('.well').css('background-color', '#efe').css('box-shadow', '5px 5px 5px black');
	$('.well').hover(
		function() {
			$(this).css('background-color', '#aaa').css('box-shadow', '0 0 8px black').css('color', '#fff');
			$('b', this).css('color', '#444');
		},  
		function() { 
			$(this).css('background-color', '#efe').css('box-shadow', '5px 5px 5px black').css('color', '#444');
	});
	
});