<?php
/**
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
*  @author    NP6 SAS <contact@np6.com>
*  @copyright 2014-2014 NP6 SAS
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of NP6 SAS
*/

/**
 * Add a CMS page
 */
class AddCmsPage
{
	var $content;
	var $id_cms;
	var $title;

	public function __construct()
	{
		$this->content = '';
		$this->id_cms = 0;
		$this->title = 'MailPerformance';
	}

	public function addInDB()
	{
		$cms = new CMS();
		foreach (Language::getLanguages() as $lang)
		{
			$cms->meta_title[$lang['id_lang']] = $this->title;
			$cms->meta_description[$lang['id_lang']] = $this->title;
			$cms->meta_keywords[$lang['id_lang']] = '';
			$cms->content[$lang['id_lang']] = $this->content;
			$cms->link_rewrite[$lang['id_lang']] = pSQL(preg_replace('/-(-)+/', '',
					preg_replace('/([^a-z\-])+/',
					'', str_replace(' ', '-', Tools::strtolower($this->title)))));
		}
		$cms->id_cms_category = 1;
		$cms->indexation = 1;
		$cms->active = 1;
		$cms->add();
	}

}
