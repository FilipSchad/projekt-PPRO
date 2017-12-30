function showErrors(errors, focus)
{
        errors.forEach(function(error) {
		if (error.message) {
			$(error.element).closest('tr').addClass('has-error').find('.frmerror').remove();
			$('<span class=frmerror>').text(error.message).insertAfter(error.element);
		}
		if (focus && error.element.focus) {
			error.element.focus();
			focus = false;
		}
	});
}
function removeErrors(elem)
{
	if ($(elem).is('form')) {
		$('.has-error', elem).removeClass('has-error');
		$('.frmerror', elem).remove();
	} else {
		$(elem).closest('tr').removeClass('has-error').find('.frmerror').remove();
	}
}
Nette.showFormErrors = function(form, errors) {
	removeErrors(form);
	showErrors(errors, true);
};
$(function() {
	$(':input').keypress(function() {
		removeErrors(this);
	});
	$(':input').blur(function() {
		Nette.formErrors = [];
		Nette.validateControl(this);
		showErrors(Nette.formErrors);
	});
});
