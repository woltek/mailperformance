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

{* Partialfield ,use to bind field *}
<div class="row">
  <div style="width:400px;"><h3 style="text-align:center;">{l s='Prestashop Fields' mod='np6'} | {l s='MailPerformance Fields' mod='np6'}</h3></div>
</div>

{* if DBfield exist , foreach item of them *}
{if $np6.DBfield }
	{foreach name=field from=$np6.DBfield item=field}
		<div class="row">
			<div class="input-group col-md-6" >

				{* Display field name *}
				<span class="input-group-addon">{$field.name|escape}</span>
			
				{* Bind a field to the name *}
				<select name="dbSelect{$field.dbName|escape}"  id="field{$smarty.foreach.field.index|escape}" class="fields" >
					<option value="0" >{l s='Don\'t import' mod='np6'}</option>
					{if isset($np6.APIFields)}
						{foreach from=$np6.APIFields item=apiField}
							{if  in_array($apiField->type,$field.type)}
								<option value="{$apiField->id|escape}" 
								{if isset($np6.importSet) && isset($np6.importSet.fields.{$field.dbName}) &&  $np6.importSet.fields.{$field.dbName}.apiFieldId == $apiField->id } selected {/if}
								{if $apiField->is_unicity or $apiField->is_obligatory}style="color:red;"{/if}  
									data-apiType="{$apiField->type|escape}" 
									data-apiId="{$apiField->id|escape}"
								{if $apiField->value_list} data-apivalue_list="$apiField->value_list"{/if}
								>
									{$apiField->name|escape}
									{if $apiField->constraint} ( {$apiField->constraint.operator.string} {$apiField->constraint.value} ) {/if}
								</option>
							{/if}
						{/foreach}
					{/if}
				</select>
			</div> 
			{if isset($field.distinctValues) }
				<div class="col-md-4 linkfield" id="linkfield{$smarty.foreach.field.index|escape}" >
					{foreach from=$field.distinctValues item=name key=value}
                    <div class="input-group col-md-4" >

                    	{* Display field name *}
                        <span class="input-group-addon">{$name|escape}</span>

						{* Bind a field to the name *}
                        <select name="dbSelectLink{$field.dbName|escape}{$value|escape}"
                                data-selectedoption="{if isset($np6.importSet) && isset($np6.importSet.fields.{$field.dbName}) && isset($np6.importSet.fields.{$field.dbName}.binding)}{$np6.importSet.fields.{$field.dbName}.binding.{$value}|escape}{/if}">
                       	 </select>
                    	</div>
					{/foreach}
				</div>
			{/if}
			
			{* Si c'est une date *}
			{if  in_array(6,$field.type)}
				<div class="col-md-4 linkfield" id="dateFormatfield{$smarty.foreach.field.index|escape}" >
                    <div class="input-group col-md-4" >

                    	{* Display field name *}
                        <span class="input-group-addon">{l s='Date format for export' mod='np6'}  :</span>

						{* Bind a field to the name *}
                        <select name="dateFormat{$field.dbName|escape}">
							{foreach from=$np6.DateFormat key=MPFormat item=PHPFormat}
								<option value="{$PHPFormat|escape}" 
								{if isset($np6.importSet) && isset($np6.importSet.fields.{$field.dbName})  &&  $np6.importSet.fields.{$field.dbName}.dateFormat == $PHPFormat } selected{/if}
								>{$MPFormat|escape}</option>
							{/foreach}
						</select>
                    </div>
				</div>
			{/if}
		</div>
	{/foreach}
{/if}