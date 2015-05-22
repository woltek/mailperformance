{*
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
*}

{* tab mailperformance form *}
<div>
	{*form for main form*}
	<fieldset style="background : white;" >
		<legend ><h2>{l s='Form integrated on the pages' mod='np6'}</h2></legend>

		<div class="row">
			<form id="form-inscr" method="post" action="{$np6.form_action|escape}">
	
				{* column form position *}
				<div class="col-md-6">
					<div class="row col-seg-form">
						<h3>{l s='Form position' mod='np6'} :</h3>

						<div class="input-group row">
							<div class="input-group-addon">
								<span>
									<i class="icon-AdminParentModules"></i>
								</span>
							</div>
							<div class="form-group" style="margin : 0px;">
								<select class="selectpicker form-control"  name="hooks[]" id="positionsHooks" style="margin-bottom: 0px;height: 180px;" multiple="multiple">
									{foreach from=$np6.hooks item=hook}
										<option {if  isset($np6.data) && $np6.data.hooks && in_array($hook.hook, $np6.data.hooks)}selected{/if} value="{$hook.hook}">
											{$hook.text}
										</option>
									{/foreach}
								</select>	
							</div>
						</div>
	 
						{*unchecked all form position*}
						<div class="input-group row">
							<button type="button" onclick="resetFormsPositions()" id="resetHooks">
								{l s='Reset positions' mod='np6'}
							</button>
						</div>
					</div>
				</div>

			 	{* column form information *}
				<div class="col-md-6" >
					<div style="width : 440px;">
						{* form title *}
						<h3>{l s='Title' mod='np6'} :</h3>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="icon-AdminTools"></i>
							</span>
							<input class="form-control" type="text" {if  isset($np6.data.data.title) }value="{$np6.data.data.title}"{/if} placeholder="{l s='Title' mod='np6'}" name="formtitle" />
						</div>

						{* Form selection *}
						<div class="input-group">
							<h3>{l s='Select a form' mod='np6'} :</h3>
						</div>
						<div class="input-group">
							<span class="input-group-addon">{l s='forms' mod='np6'} :</span>
							<select class="form-control" name="formSelection">
							{if  isset($np6.formListType1) && $np6.formListType1}
								{foreach from=$np6.formListType1 item=testvar}
									<option {if isset($np6.data) && isset($np6.data.data.idForm) && $np6.data.data.idForm == $testvar->id}selected{/if}  value="{$testvar->id}">{$testvar->name}</option>
								{/foreach}
							{/if}
							</select>
						</div>

						{* form in a frame *}
						<div class="input-group" >
							<div class="checkbox">
								<label>
									 <input type="checkbox" onclick="checkboxShowForm()" name="showForm" id="showFormCheckBox" 
									 {if isset($np6.data) && isset($np6.data.data.showForm) &&  $np6.data.data.showForm}checked{/if}> 
									 {l s='Display the form in a frame (otherwise a link to the form is displayed)' mod='np6'}				 
								</label>
							</div>
						</div>
						
						{* form height *}
						<div class="input-group" id="frameHauteurPixel">
							<span class="input-group-addon">{l s='height of form' mod='np6'} :</span>
							<input class="form-control" type="text" {if  isset($np6.data) && isset($np6.data.data.hauteur) }value="{$np6.data.data.hauteur}"{/if} class="form-control" placeholder="{l s='height of form' mod='np6'}" name="hauteurFrame" />
						</div>
						
						{* form button text *}
						<div class="input-group" id="textBouton">
							<span class="input-group-addon">{l s='Button text' mod='np6'} :</span>
							<input class="form-control" type="text" {if  isset($np6.data) && isset($np6.data.data.textBouton) }value="{$np6.data.data.textBouton}"{/if}  class="form-control"  name="textBouton" />
						</div>

					</div>
				</div>
			</div>
		
			{* form save *}
			<div class="row">
				<button type="submit" name="submitMailPerfFormPosition" class="btn btn-default" >
					<i class="process-icon-save"></i> {l s='Save' mod='np6'}
				</button>
			</div>
	</form>

	</fieldset>

	<fieldset style="background : white;">
		
		<legend><h2> {l s='Form on a dedicated page' mod='np6'}</h2></legend>

		{* form for form on a dedicated page *}
		<form method="post" action="{$np6.form_action|escape}">
		
			<div class="row .col-seg-form">
				<div class="col-md-6">
					<h3>{l s='Add a new page' mod='np6'}</h3>

					{* form title *}
					<div class="input-group row">
						<span class="input-group-addon">{l s='Title' mod='np6'} :</span>
						<input type="text" class="form-control" name="CMStitre0">					
					</div>

					{* form selection *}
					<div class="input-group row">
						<span class="input-group-addon">{l s='Form' mod='np6'} :</span>
						<select class="form-control" name="CMSform0">
						{if $np6.formListTypeAll}
							{foreach from=$np6.formListTypeAll item=opt}
								<option  value="{$opt->id|escape}">{$opt->name|escape}</option>
							{/foreach}
						{/if}
						</select>
					</div>
				</div>
				
				{* Existing pages *}
				<div class="col-md-6">
					<h3>{l s='Existing pages' mod='np6'}</h3>
					{if $np6.listCmsPage}
						{foreach from=$np6.listCmsPage item=page}
						<div class="row">
							<div class="col-md-6">
								<p class="pageList">{$page.meta_title|escape}</p>
							</div>
							<div class="col-md-2">
								<p><a href="{$np6.link->getAdminLink('AdminCmsContent')|escape}&updatecms&id_cms={$page.id_cms|escape}">{l s='edit' mod='np6'}</a></p>
							</div>
							<div class="col-md-2">
								<p><a href="{$np6.link->getCMSLink({$page.id_cms})|escape}">{l s='show' mod='np6'}</a></p>
							</div>
						</div>
						{/foreach}
					{/if}
				</div>
				
			</div>
			<div class="row">
				<input type="submit" Value={l s='Add' mod='np6'} name="submitMailPerfFormPage" class="btn btn-default" />
			</div>
		</form>

	</fieldset>
	
	
	

	
	
</div>
