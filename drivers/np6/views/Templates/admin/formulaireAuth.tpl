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
 
<script>
var confirmAuthNewCustomer = "{l s='You change the customer, all your old data will be delete. Do you want to continue?' mod='np6'}";
var confirmAuthNewCustomerNotValidKey = "{l s='Connection error or you auto login key isn\'t valid. All your old data will be delete. Do you want to continue? ' mod='np6'}";
var confirmAuthNewCustomerEmptyKey = "{l s='Your auto login key is empty. All your old data will be delete. Do you want to continue? ' mod='np6'}";
</script>

{* tab authentication *}
<div class="row">

    <fieldset style="background : white;">
        <legend><h2>{l s='Authentication' mod='np6'}</h2></legend>

        {* authentication form *}
        <form id="form-auth-np6" class="col-md-6" method="post" action="{$np6.form_action|escape}">
            <div class="input-group">
                {* Authentication key field *}       
                <div class="input-group">
                    <span class="input-group-addon">
                        Auto login key
                    </span>
                    <input type="text" id="alkey" name="alkey" {if isset($np6.userSettings)}value="{$np6.userSettings.alkey|escape}" {/if}class="form-control" placeholder="{l s='Auto login key' mod='np6'}">
                </div>

                {* appears durring key check *}
                <div id="loadingAlKey" class="input-group" style="display:none;">
                    <img src="../img/admin/ajax-loader.gif" alt="ajax loader" style="float:left;"/>
                    {l s='checking new auto login key' mod='np6' }
                </div>

                {* hidden input for ajax request *}
                <input type="hidden" name="submitMailPerfAuth" value="true" />
                <input type="hidden" id="clearAllValues" name="clearAllValues" value="false" />

                {* Save button *}
                <div class="input-group">
                    <button type="submit" id="submitMailPerfAuth" name="submitMailPerfAuth" class="btn btn-default">
                        <i class="process-icon-save"></i> {l s='Save' mod='np6' } 
                    </button>
                </div>
            </div>
        </form>

        {* Only  if connected, display user information *}
        {if isset($np6.userSettings) && isset($np6.userSettings.contact)}
        <div class="col-md-4" style="margin-top : 15px;">
            <p>{l s='You are logged in as MailPerformance contact: ' mod='np6'}  {$np6.userSettings.contact->login|escape}.</p>
            <p>
                {$np6.userSettings.contact->identity->first_name|escape}
                {$np6.userSettings.contact->identity->last_name|escape}
            </p>
            <p>
                <a href="mailto:{$np6.userSettings.contact->email|escape}">{$np6.userSettings.contact->email|escape}</a>
            </p>
            <p>{l s='Your contact expires: ' mod='np6'} {if $np6.userSettings.contact->expire} {$np6.userSettings.contact->expire|escape} {else} {l s='at account expiration' mod='np6'} {/if}</p>
        </div>
        {/if}
    </fieldset>


    


</div>
