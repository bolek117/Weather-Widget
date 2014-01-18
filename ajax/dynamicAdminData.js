$( document ).ready(function() {
	$('#providersList').change(function() {
		$.when(
			loadMappings()
		).then(
			$.when(
				loadCountries()
			).then(
				loadCities()
			)
		);
	});

	$('#countriesList').change(function() {
		console.log("here");
		loadCities();
	});
	
	$.when(
		loadProviders()
	).then(
		$.when(
			loadMappings()
		).then(
			$.when(
				loadCountries()
			).then(
				loadCities()
			)
		)
	);
});

function aquireData(element, i, item, additional)
{
	switch(additional)
	{
		case "asOption":
		{
			$(element).append("<option value=\""+i+"\">"+item+"</option>");
		}
		break;
		case "asOptionWithText":
		{
			$(element).append("<option value=\""+item+"\">"+item+"</option>");
		}
		break;
		case "asMappings":
		{
			$('#'+i).val(item);
		}
		break;
		case "asRow":
		{
			var checked = ""
			if (item['enabled'] == true)
				checked = " checked=\"checked\"";
				
			$(element).append("<tr><td class=\"textCenter\"><input type=\"checkbox\" value=\""+item['enabled']+"\" name=\"location["+i+"]\""+checked+" /></td><td>"+item['name']+"</td>");
		}
		break;
		default:
		{
			$(element).append(i+" "+item);
		}
		break;
	}
}

function loadProviders()
{
	loadTimeout();
	var element = '#providersList';
	disableAndWrite(element, "Loading...");
	$(element).fetchData("ajax/getData.php", "getProvidersList", "", "asOption");
}

function loadMappings()
{
	var element = '#editDisplayedName';
	var selectedProvider = $('#providersList option:selected').val();

	$(element).fetchData("ajax/getAdminData.php", "getProviderDetails", selectedProvider, "asMappings");
	element = '#list_element';	
	$(element).fetchData("ajax/getAdminData.php", "getMappings", selectedProvider, "asMappings");
}

function loadCountries()
{	
	var element = '#countriesList';
	var selectedProvider = $('#providersList option:selected').val();
	$(element).fetchData("ajax/getData.php", "getCountriesList", selectedProvider, "asOptionWithText");
}

function loadCities()
{	
	var element = '#citiesList';
	var selectedProvider = $('#providersList option:selected').val();
	var selectedCountry = $('#countriesList option:selected').text();
	$(element).fetchData("ajax/getAdminData.php", "getCitiesList", selectedProvider+"|"+selectedCountry, "asRow");
	$(element).prepend("<tr><td class=\"bold\">Enabled</td><td class=\"bold\">City</td></tr>");
	$(element).append("<tr><td>&nbsp;</td><td><input type=\"submit\" name=\"saveStates\" value=\"Save states\" class=\"button\" /></td></tr>");
}

function loadTimeout()
{
	var element = '#soapTimeout';
	$(element).fetchData("ajax/getAdminData.php", "getSoapTimeout", "", "asMappings");
}

function disableAndWrite(id, msg, notAsOption)
{
	if (notAsOption == undefined)
		notAsOption = false;

	if (!notAsOption)
	{
		$(id).attr("disabled", "disabled");
		$(id).empty();
		$(id).append("<option>"+msg+"</option>");
	}
	else
	{
		$(id).empty();
		$(id).append("<tr><td>"+msg+"</td></tr>");
	}
}
