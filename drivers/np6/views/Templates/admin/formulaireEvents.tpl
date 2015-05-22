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

{* tab event prestashop *}

<div>

	<h2>{l s='Events binding' mod='np6'}</h2>
	<blockquote>
		{l s='Add a Prestashop Customer to a MailPerformance Segment and/or modify a MailPerformance Field on certain Prestashop Events' mod='np6'}
	</blockquote>

	{* event form *}
	<form id="form-event" class="row" method="post" action="{$np6.form_action|escape}">
		{* foreach event *}
		{foreach $np6.action_hooks key=hook item=hookdetails}
			<fieldset style="background : white;">
				{* Segment *}
				<legend><h3>{l s='Event: ' mod='np6'} {$hookdetails.help|escape}</h3></legend>

				<div class="group input-group" >
					<span class="input-group-addon" title="{$hookdetails.help|escape}">{l s='MailPerformance Segment' mod='np6'} </span>
					{* Segment binding *}
					<select name="{$hook|escape}choixSegment">
							<option value="-1">{l s='No segment' mod='np6'} </option>
						{foreach $np6.segmentsList item=segment}
							<option value="{$segment->id}" {if isset($np6.eventSet) && isset($np6.eventSet.{$hook}.segment) && $segment->id == $np6.eventSet.{$hook}.segment} selected {/if} >
							{$segment->name|escape} </option>
						{/foreach}
					</select>
				</div>

				{* all field link with current event *}
				<h41 class="eventtitle">{l s='MailPerformance fields to modify on event :' mod='np6'}</h41>
				<div class="eventfields">
					{* foreach fields for this event *}
					{foreach $hookdetails.fields key=name item=itemArray}
						<div class="input-group group">
							<span class="input-group-addon" title="">{$itemArray[0]|escape} </span>
							{* field binding *}
							<select name="{$hook|escape}champs{$name|escape}">
								<option value="-1">{l s='Don\'t import' mod='np6'}</option>
								{foreach from=$np6.APIFields item=apiField}
									{if $apiField->type == $itemArray[1]}{* $itemArray[1] is the field type *}
										<option value="{$apiField->id|escape}" 
										{if isset($np6.eventSet) && isset($np6.eventSet.{$hook}.champs.{$name}) && $apiField->id == $np6.eventSet.{$hook}.champs.{$name}} selected {/if}
										>
											{$apiField->name|escape}
											{if $apiField->constraint} ( {$apiField->constraint.operator.string} {$apiField->constraint.value} ) {/if}
										</option>
									{/if}
								{/foreach}
							</select>
						</div>
					{/foreach}
				</div>
			</fieldset>
		{/foreach}
	
		<div class="decal-left" > 
			<button type="submit" value={l s='Save' mod='np6' } name="submitMailPerfFormEvent" class="btn btn-default">
				<i class="process-icon-save"></i> {l s='Save' mod='np6'} 
			</button>
		</div>	
	</form>

</div>