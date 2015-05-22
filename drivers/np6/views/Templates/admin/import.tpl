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

{* tab data client link *}
<div>
	<fieldset>
    	<legend><h2>{l s='Customer management' mod='np6'}</h2></legend>

    	{* link form *}
        <form id="form-feed" class="row" role="form" method="post" action="{$np6.form_action|escape}">
    	<fieldset style="background : white;">
    		<legend style="background : white;">
    			<h2>
    				{l s='Automatically add on subscription' mod='np6'}
				</h2> 
			</legend>
			{* Auto add checkbox *}
            <div class="row">
            	<div class="row">	
	                <div class="input-group" >
	                    <div class="checkbox">
	                        <label>
	                            <input type="checkbox" onclick="checkboxShowSync()" id="autoSyncCheckBox" name="isAutoSync"
	                                   {if isset($np6.importSet) &&  $np6.importSet.isAutoSync}checked{/if}/>
	                            {l s='Automatically add' mod='np6'}
	                        </label>
	                    </div>
	                </div>
	            </div>
            </div>
            			
			<div id="autoSyncToHide" class="row">
				<div class="col-seg-form">	
					{* add user who do not wish to receive newsletters checkbox *}
					 <div class="row">
		           		<div class="input-group "  style="width : auto">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="isAddNoNews" 
										{if isset($np6.importSet) && $np6.importSet.isAddNoNews}checked{/if}>
									{l s='Add users who do not wish to receive newsletters' mod='np6'} 
								</label>
							</div>
						</div>
	            	</div>

					{* bind user segment *}
					<div class="input-group hideOnNewSegment row" >
						<span class="input-group-addon">{l s='Select a segment to add users to' mod='np6'}</span>
						<select name="choixSegment">
							<option value="-1">{l s='No segment' mod='np6'} </option>
							{if isset($np6.segmentsList)}
								{foreach $np6.segmentsList item=segment}
								<option value="{$segment->id}" {if isset($np6.importSet) && $segment->id == $np6.importSet.inSegmentId} selected {/if} >{$segment->name|escape} </option>
								{/foreach}
							{/if}
						</select>
					</div>

					{* Create new segment : open the configuration segment *}
					<div class="input-group row" id="addSegmentBouton">
						<button type="button" onclick="addNewSegment()" class="btn btn-default">
							<i class="process-icon-new "></i> {l s='Add a segment' mod='np6'} 
						</button>
					</div>

					{* configure new segment *}
					<div  class="row">
						<fieldset id="showOnAddSegment" style="background : white; width : 500px; margin : 5px;" >
							<legend>
									<h3>{l s='New segment ' mod='np6'}</h3>
							</legend>

							<div class="input-group row">
								<span class="input-group-addon">{l s='Name : ' mod='np6'} </span>
								<input class="form-control" type="text" name="newSegmentName"  placeholder="{l s='Segment name ' mod='np6'}"/>
							</div>
							
							<div class="input-group row">
								<span class="input-group-addon">{l s='Description : ' mod='np6'} </span>
								<textarea  class="form-control" name="newSegmentDesc"  placeholder="{l s='Segment description' mod='np6'}"></textarea>
							</div>
						
							<div class="input-group row">
								<span class="input-group-addon">{l s='Expiration : ' mod='np6'} </span>
								<input id="datepicker" class="form-control" type="text" name="newSegmentDate"  placeholder="format : AAAA-MM-JJ"  />
							</div>
							
							<div class="input-group row">
								<button type="submit" value={l s='Save' mod='np6' } name="submitMailPerfFormImportAddSegment" class="btn btn-default">
									<i class="process-icon-save"></i> {l s='Save' mod='np6'} 
								</button>
							</div>	
						</fieldset>		
					</div>		
				</div>
			</div>
    	</fieldset>
    	<fieldset style="background : white;">
    		<legend style="background : white;"><h2>{l s='Binding settings' mod='np6'}</h2></legend>
    		{* show bind fields *}
			{include file="{$np6.admin_tpl_path}partialFieldsOption.tpl"}
    	</fieldset>

    	
		
			{* save configuration *}
            <button type="submit" value={l s='Save' mod='np6' } name="submitMailPerfFormImport" class="btn btn-default">
                <i class="process-icon-save"></i> {l s='Save' mod='np6'} 
            </button>

			{* export client *}

			<button type="button" class="btn btn-default" onClick="javascript:location.href = '../modules/np6/ExportCsv.php';">
                	<i class="process-icon-export"></i> {l s='Export Prestashop customers' mod='np6'} 
        	</button>
    	</form>
	</fieldset> 
</div>