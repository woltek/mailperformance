/*
 * 2014-2014 NP6 SAS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author NP6 SAS <contact@np6.com>
 *  @copyright  2014-2014 NP6 SAS
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of NP6 SAS
 */

var moduleUrl = '../modules/np6/Ajax.php';
var arrayOfValueList = [ "7", "8", "9" ];
var allowSubmitAuth = false;
function checkboxShowForm() {
	if ($("#showFormCheckBox").attr('checked')) {
		$("#frameHauteurPixel").show(0);
		$("#textBouton").hide(0);
	} else {
		$("#frameHauteurPixel").hide(0);
		$("#textBouton").fadeIn(0);
	}
}

function checkboxShowSync() {
	if ($("#autoSyncCheckBox").attr('checked')) {
		$("#autoSyncToHide").fadeIn(600);
	} else {
		$("#autoSyncToHide").hide(400);
	}
}

function ajaxRequestToModule(id, methode, successFunc) {
	var query = $.ajax({
		type : 'POST',
		url : moduleUrl,
		data : 'ajax=true&methode=' + methode + '&id=' + id,
		success : function(val) {
			successFunc(val);
		}
	});
}

function getValueListFromFieldId(id, divId, first) {
	ajaxRequestToModule(id, 'getValueListFromFieldId', function(val) {
		var resultObj = $.parseJSON(val);
		var optionsStr = "<option value=\"-1\" >ne pas lier</option>";

		for (var i = 0; i < resultObj.values.length; i++) {
			optionsStr += "<option value=\"" + resultObj.values[i].value
					+ "\" ";
			optionsStr += ">" + resultObj.values[i].value + "</option>";
		}

		$("#link" + divId + " select").html(optionsStr);
		if (first) // si c'est la premiere execution
		{
			$("#link" + divId + " select")
					.each(
							function() {
								var selectedValue = $(this).attr(
										"data-selectedoption");
								$(this).children(
										"select option[value='" + selectedValue
												+ "']").attr("selected", "");
							});
		}

	});
}
function changeClearAll(allowSubmitAuth)
{
	if(allowSubmitAuth)
	{
		$("#clearAllValues").val(true);
	}
}
function checkAPIkey()
{
	var id = $("#alkey").val();
	//if the key is empty delete everything without ajax request
	if(id.trim() == "")
	{
		allowSubmitAuth = confirm(confirmAuthNewCustomerEmptyKey);
		changeClearAll(allowSubmitAuth);
		if(allowSubmitAuth){$("#form-auth-np6").submit();}
		return;
	}
	$("#alkey").prop('disabled', true);
	$("#loadingAlKey").show(0);
	ajaxRequestToModule(id, 'getCheckAutoLoginKey',
			function (val)
			{
				var resultObj = $.parseJSON(val);
				$("#loadingAlKey").hide(0);
				$("#alkey").prop('disabled', false);
				
				//result valid and change customer
				if(resultObj.success && resultObj.changeCustomer)
				{
					allowSubmitAuth = confirm(confirmAuthNewCustomer);
					changeClearAll(allowSubmitAuth);
				}
				//no customer new customer key and valid key
				else if(resultObj.success && resultObj.alkeyValid){allowSubmitAuth = true;}
				//unvalid key
				else if(resultObj.success) 
				{
					allowSubmitAuth = confirm(confirmAuthNewCustomerNotValidKey);
					changeClearAll(allowSubmitAuth);
				}
				//error
				else{allowSubmitAuth = confirm('Ajax Error! Do you want to continue?');}
				if(allowSubmitAuth)
				{
					$("#form-auth-np6").submit();
				}
			});
}

function checkSelectValueListSelection(obj, first) {
	// si on a bien un champ du type value list on fait la requete ajax et on
	// affiche la liste
	if ($.inArray($("#" + obj.id + " option:selected").attr("data-apitype"),
			arrayOfValueList) >= 0) {
		$("#link" + obj.id).fadeIn(400);
		getValueListFromFieldId(obj.value, obj.id, first);
	}
	// type date == 6
	else if ($("#" + obj.id + " option:selected").attr("data-apitype") == 6) {
		$("#dateFormat" + obj.id).fadeIn(400);
	} else {
		$("#link" + obj.id).hide(100);
		$("#dateFormat" + obj.id).hide(100);
	}
}

function resetFormsPositions()
{
	$("#positionsHooks").val([]);
}

$().ready(
	function() {
		checkboxShowForm();
		checkboxShowSync();
		$(".fields").each(function() {
			// selection des champs actuellement lier
			checkSelectValueListSelection(this, true);
		});
		$("#autoSyncCheckBox").change(function() {
			checkboxShowSync();
		});
		$("#showFormCheckBox").change(function() {
			checkboxShowForm();
		});
		$("#resetHooks").click(function() {
			resetFormsPositions();
		});
		$(".fields").change(function() {
			checkSelectValueListSelection(this, false);
		});
		$("#form-auth-np6").submit(function()
			{
				if(!allowSubmitAuth)checkAPIkey();
				return allowSubmitAuth;
			});
		$("#addSegmentBouton")
				.click(
						function() {

							$("#showOnAddSegment").fadeIn(600);
							$("#showOnAddSegment")
									.append(
											"<input type=\"hidden\" name=\"isNewSegment\" />");
							$("#addSegmentBouton").hide(0);
							$(".hideOnNewSegment").hide(0);
						});
	});
