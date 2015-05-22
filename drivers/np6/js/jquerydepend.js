
//this code need jquery to work

//listner
$().ready(
	function() {
		checkboxShowForm();
		checkboxShowSync();
		delayAndHide();
		
			//format pour la date yyyy-mm-dd
		$("#datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
			// selection des champs actuellement lier
		$(".fields").each(function() {
			checkSelectValueListSelection(this, true);
		});
		$(".fields").change(function() {
			checkSelectValueListSelection(this, false);
		});
		$("#form-auth-np6").submit(function()
			{
				if(!allowSubmitAuth)checkAPIkey();
				return allowSubmitAuth;
			});
	});

