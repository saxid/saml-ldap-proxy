$(function() {
	$('section#manage-passwords li h2 i.fa').click(function() {
		$(this).parent().next('form').slideToggle();
	});
});