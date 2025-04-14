$(document).ready(function()
{
	$('#resistors').change(function()
	{
	    $('.resistor-table').hide();
	    $('#'+$('#resistors').val()).show();
	});
	
	$('#'+$('#resistor-set-current').val()).show();
});
