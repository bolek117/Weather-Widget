<?php
$base = "";
require_once $base."includes.php";
require_once $base."include/admin.php";

$admin = new Admin;

if (!$admin->checkRights())
{
	header("Location: login.php");
	die();
}
else
{
	include $base."controlers/adminControler.php";
		
	$interface = new _Interface;
	$interface->setTitle("Administration panel");
	$interface->addHeaderContent("<script src=\"./ajax/jquery-1.10.1.min.js\"></script>");
	$interface->addHeaderContent("<script src=\"./ajax/fetchData.js\"></script>");
	$interface->addHeaderContent("<script src=\"./ajax/dynamicAdminData.js\"></script>");
	$interface->addBodyAttribute("class", "login");
	echo $interface->getHeader();
	?>
	<script>
		function confirm_delete()
		{
			return confirm("Are you sure? It cannot be undone!");
		}
	</script>
	<div class="admin">
		<p class="big bold">Administration panel (<a href="./login.php?logout=true" title="Logout">Logout</a>)</p>
		<?php echo $errorMsg; ?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<div class="padding5 textCenter">
				<fieldset>
					<legend class="textLeft">Set SOAP timeout</legend>
					<div class="padding5">
						<label class="small">SOAP Timeout (s):</label>
						<input type="text" name="soapTimeout" class="textbox" id="soapTimeout" />
					</div>
					<div class="padding5">
						<input type="submit" name="setTimeout" value="Set timeout" class="button" />
					</div>
				</fieldset>
				
				<br>
				
				<fieldset>
					<legend class="textLeft">Add new provider</legend>
					<div class="padding5">
						<label class="small">Displayed name:</label>
						<input type="text" name="newName" class="textbox" />
					</div>
					<div class="padding5">
						<label class="small">Service URI:</label>
						<input type="text" name="newUri" class="textbox" />
					</div>
					<div class="padding5">
						<input type="submit" name="addProvider" value="Add Provider" class="button" />
					</div>
				</fieldset>
				
				<br><br>
				
				<label class="small">Select existing provider:</label>
				<select name="provider" disabled="disabled" id="providersList" class="textbox">
					<option>Loading providers...</option>
				</select>

				<br>

				<fieldset>
					<legend class="textLeft">Edit provider</legend>
					<div class="padding5">
						<label class="small">Displayed name:</label>
						<input type="text" name="editDisplayedName" class="textbox" id="name" />
					</div>
					<div class="padding5">
						<label class="small">Service URI:</label>
						<input type="text" name="editUri" class="textbox" id="uri" />
					</div>
					<div class="padding5">
						<input type="submit" name="saveProvider" value="Save Provider" class="button" />
						<input type="submit" name="deleteProvider" onClick="return confirm_delete()" value="Delete provider" class="button" />
					</div>
				</fieldset>

				<br>
				
				<fieldset>					
					<legend class="textLeft">Mappings</legend>
					<div class="padding5">
						<label class="wide">Response "root" element XPath:</label><input type="text" name="mapping[list_element]" id="list_element" class="textbox" />
					</div>
					<div class="padding5">
						<label class="wide">Response "country" element markup:</label><input type="text" name="mapping[list_country]" id="list_country" class="textbox" />
					</div>
					<div class="padding5">
						<label class="wide">Response "city" element markup:</label><input type="text" name="mapping[list_city]" id="list_city" class="textbox" />
					</div>						
					<hr>						
					<div class="padding5">
						<label class="wide">SOAP "GetCitiesByCountry" call:</label><input type="text" name="mapping[GetCitiesByCountry]" id="GetCitiesByCountry" class="textbox" />
					</div>
					<div class="padding5">
						<label class="wide">"GetCitiesByCountry" result markup:</label><input type="text" name="mapping[GetCitiesByCountryResult]" id="GetCitiesByCountryResult" class="textbox" />
					</div>
					<div class="padding5">
						<label class="wide">SOAP "GetWeather" call:</label><input type="text" name="mapping[GetWeather]" id="GetWeather" vclass="textbox" />
					</div>
					<div class="padding5">
						<label class="wide">"GetWeather" result markup:</label><input type="text" name="mapping[GetWeatherResult]" id="GetWeatherResult" class="textbox" />
					</div>
					<hr>						
					<div class="padding5">
						<label class="wide">SOAP call "CityName" element name:</label><input type="text" name="mapping[list_argument_city]" id="list_argument_city" class="textbox" />
					</div>
					<div class="padding5">
						<label class="wide">SOAP call "CountryName" element name:</label><input type="text" name="mapping[list_argument_country]" id="list_argument_country" class="textbox" />
					</div>
					<div class="padding5">
						<input type="submit" name="saveMappings" value="Save mappings" class="button" />
					</div>
				</fieldset>
				
				<br>
				
				<fieldset>
					<legend class="textLeft">Add new country to list</legend>
					<div class="padding5">
						<label class="small">New country name:</label>
						<input type="text" name="newCountry" class="textbox" />
					</div>
					<div class="padding5">
						<input type="submit" name="addCountry" value="Add country" class="button" />
					</div>
				</fieldset>
				
				<br>
				
				<fieldset>
					<legend class="textLeft">Locations</legend>
						<div class="padding5">
							<label class="small ">Select Country:</label>
							<select name="country" disabled="disabled" id="countriesList" class="textbox">
								<option>Loading countries...</option>
							</select>
							<input type="submit" name="deleteCountry" value="Delete country" class="button" onClick="return confirm_delete()" />
							<table id="citiesList">
								<tr>
									<td class="bold">Enabled</td>
									<td class="bold wide">City</td>
								</tr>
							</table>
						</div>
				</fieldset>
			</div>
		</form>
	</fieldset>
	
	<?php
	echo $interface->getFooter();
}


?>
