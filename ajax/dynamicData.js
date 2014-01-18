$( document ).ready(function() {
	$('#providersList').change(function() {
		$.when(
			loadCountries()
		).then(
			loadCities()
		)
	});

	$('#countriesList').change(function() {
		loadCities();
	});

	$('#citiesList').change(function() {
		loadWeater();
	});

	$(function() {
		
		$.when(
			loadProviders()
		).then(
			$.when(
				loadCountries()
			).then(function() {
				loadCities();
			})
		);
	});
});

function aquireData(element, i, item, additional)
{
	switch(additional)
	{
		case "asCity":
		{
			$.each( item, function( i, item )
			{
				aquireData(element, i, item['name'], "asOption");
			});
		}
		break;
		case "asOption":
		{
			$(element).append("<option value=\""+i+"\">"+item+"</option>");
		}
		case "asRow":
		{
			if (i == "Status" && item.indexOf("Failed", item) > -1)
				item = "<span class=\"red bold\">"+item+"</span>";
				
			$(element).append("<tr><td class=\"bold\">"+i+"</td><td class=\"value\">"+item+"</td></tr>\n");
		}
		break;
		default:
		{
			$(element).append(i+" "+item);
		}
		break;
	}
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

function loadProviders()
{
	var element = '#providersList';
	disableAndWrite(element, "Loading...");
	$(element).fetchData("ajax/getData.php", "getProvidersList", "", "asOption");
}

function loadCountries()
{
	var element = '#countriesList';
	disableAndWrite(element, "Loading...");
	$(element).fetchData("ajax/getData.php", "getCountriesList", $('#providersList option:selected').val(), "asOption");
}

function loadCities()
{
	var element = '#citiesList';
	disableAndWrite(element, "Loading...");
	var selectedProvider = $('#providersList option:selected').val();
	var selectedCountry = $('#countriesList option:selected').text();
	$.when(
		$(element).fetchData("ajax/getData.php", "getCitiesList", selectedProvider+ "|" + selectedCountry, "asCity")
	).then(
		loadWeater()
	)
}

function loadWeater()
{
	var element = '#weather';
	disableAndWrite(element, "Loading...");
	var selectedProvider = $('#providersList option:selected').val();
	var selectedCountry = $('#countriesList option:selected').text();
	var selectedCity = $('#citiesList option:selected').text();
	$(element).fetchData("ajax/getData.php", "getWeather", selectedProvider+"|"+selectedCountry+"|"+selectedCity, "asRow");
}
