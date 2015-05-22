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

{* Display Error/warning/sucess information *}
<div id="mailperf" class="mailPerf-container bootstrap">
{if $np6.message}
	<div id="messagebloc" class="messagebloc {$np6.message.type|escape}">
		{if $np6.message.type == 'error'}
			<h4>Erreur</h4>
		{elseif $np6.message.type == 'warning'}
			<h4>Warning</h4>
		{else}
			<h4>Success</h4>
		{/if}
		<br/>
		{$messageArray = explode("<br>" , $np6.message.text)}
		{foreach $messageArray as $message}
			{if $message != "" }
				<p style="margin-left : 6px;">{$message|escape}</p>
			{/if}
		{/foreach}
	</div>
{/if}
	

	{* display tab *}
	<h1>{l s='MailPerformance' mod='np6'}</h1>
	<ul class="nav nav-tabs nav-justified" role="tablist">
		<li class="{if !isset($np6.tabIndex) || $np6.tabIndex == 0}active{/if}"><a class="{if  !isset($np6.tabIndex) || $np6.tabIndex == 0}active{/if}" href="#authMenu" role="tab" data-toggle="tab">{l s='Authentication' mod='np6'}</a></li>
		{if isset($np6.userSettings) && $np6.userSettings.alkey != ''}
			<li class="{if isset($np6.tabIndex) && $np6.tabIndex == 1}active{/if}"><a class="{if  isset($np6.tabIndex) && $np6.tabIndex == 1}active{/if}" href="#importMenu" role="tab" data-toggle="tab">{l s='Customer management' mod='np6'}</a></li>
			<li class="{if isset($np6.tabIndex) && $np6.tabIndex == 2}active{/if}"><a class="{if  isset($np6.tabIndex) && $np6.tabIndex == 2}active{/if}" href="#formMenu" role="tab" data-toggle="tab">{l s='MailPerformance forms' mod='np6'}</a></li>
			<li class="{if isset($np6.tabIndex) && $np6.tabIndex == 3}active{/if}"><a class="{if  isset($np6.tabIndex) && $np6.tabIndex == 3}active{/if}" href="#formEvents" role="tab" data-toggle="tab">{l s='Prestashop Events' mod='np6'}</a></li>
			<li class="{if isset($np6.tabIndex) && $np6.tabIndex == 4}active{/if}"><a class="{if  isset($np6.tabIndex) && $np6.tabIndex == 4}active{/if}" href="#formCart" role="tab" data-toggle="tab">{l s='Abandoned Cart Alert' mod='np6'}</a></li>
		{/if}
	</ul>
	 
	 {* display tab *}
	<div class="tab-content">
		<div class="tab-pane  {if !isset($np6.tabIndex) || $np6.tabIndex == 0}active{else}fade{/if}" id="authMenu">
			{include file="{$np6.admin_tpl_path}formulaireAuth.tpl"}
		</div>
		{if $np6.isConnected}
			<div class="tab-pane {if isset($np6.tabIndex) && $np6.tabIndex == 1}active{else}fade{/if}" id="importMenu">
				{include file="{$np6.admin_tpl_path}import.tpl"}
			</div>
			<div class="tab-pane {if isset($np6.tabIndex) && $np6.tabIndex == 2}active{else}fade{/if}" id="formMenu">
				{include file="{$np6.admin_tpl_path}formulaireInscription.tpl"}
			</div>
			<div class="tab-pane {if isset($np6.tabIndex) && $np6.tabIndex == 3}active{else}fade{/if}" id="formEvents">
				{include file="{$np6.admin_tpl_path}formulaireEvents.tpl"}
			</div>
			<div class="tab-pane {if isset($np6.tabIndex) && $np6.tabIndex == 4}active{else}fade{/if}" id="formCart">
				{include file="{$np6.admin_tpl_path}formulaireCartAbandonnement.tpl"}
			</div>
		{/if}
	</div>
</div>