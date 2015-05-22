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
 

{* tab abandoned Cart *}
<div>
    <h2>{l s='Abandoned Cart Alert' mod='np6'}</h2>
	<blockquote>
		{l s='To create an abandoned cart alert you must create bindings with two special MailPerformance date fields' mod='np6'}
	</blockquote>

	{* abandoned Cart form *}

	<fieldset style="background : white;">
		<legend><h3>{l s='Abandoned Cart Alert' mod='np6'}</h3></legend>
		<form id="form-event" class="row" method="post" action="{$np6.form_action|escape}">
			{* option activate yes/no *}
			<div class="input-group" style="float:none !important;" >
				<div class="checkbox">
					<label>
						 <input type="checkbox" name="activateCart" 
						 {if isset($np6.eventCart) && isset($np6.eventCart.isValidate) &&  $np6.eventCart.isValidate}checked{/if}> 
						 {l s='Activate abandoned cart alert' mod='np6'}
					</label>
				</div>
			</div>
			<h4>{l s='MailPerformance fields to bind :' mod='np6'}</h4>

			{* foreach fields for this event *}
			{* bind mailforce field *}
			{foreach $np6.cart_hooks key=name item=itemArray}
				<div class="input-group group">
					<span class="input-group-addon" title="">{$itemArray.text|escape} </span>
					<select name="cart{$name|escape}">
						{foreach from=$np6.APIFields item=apiField}
							{if $apiField->type == 'date'}{* date type *}
								<option value="{$apiField->id|escape}" 
								{if isset($np6.eventCart) && isset($np6.eventCart.{$name}) && $apiField->id == $np6.eventCart.{$name}} selected {/if}
								>
									{$apiField->name|escape}
									{if $apiField->constraint} ( {$apiField->constraint.operator.string} {$apiField->constraint.value} ) {/if}
								</option>
							{/if}
						{/foreach}
					</select>
				</div>
			{/foreach}
			
			<button type="submit" value={l s='Save' mod='np6' } name="submitMailPerfCartEvent" class="btn btn-default">
				<i class="process-icon-save"></i> {l s='Save' mod='np6'} 
			</button>
		</form>
	</fieldset>
</div>