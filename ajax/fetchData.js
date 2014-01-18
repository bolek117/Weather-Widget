$.fn.fetchData = function(addr, request, parameter, additional) {
	if (additional == undefined)
		additional = false;
		
	var element = this;
	if (parameter != "")
		parameter = "&parameter="+parameter;
		
	$.ajax({
		url: addr+"?request="+request+parameter,
		dataType: 'json',
		async: false,
		timeout: 5000,
		success: function (data) {
			if(data != undefined) {
				if (data == "null")
					disableAndWrite(element, "Empty record fetched.", additional);
				else if (data != "not_found")
				{
					$(element).removeAttr("disabled");
					$(element).empty();
					$.each( data, function( i, item ) {
						aquireData(element, i, item, additional);
					});
					return true;
				}
				else
				{
					disableAndWrite(element, "No elements found.", additional);
				}
			}
			else
			{
				disableAndWrite(element, "Unable to get data from server", additional);
				return false;
			}
		},
		error: function () {
			disableAndWrite(element, "Unable to get data from server, local copy not found.", additional);
			return false;
		}
	});
}
