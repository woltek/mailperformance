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
 * CmsList Retrieve CMS page
 *
 */
class CmsList
{

	public function getCmsList()
	{
		$sql = 'SELECT * FROM '._DB_PREFIX_.'cms_lang WHERE content LIKE \'%id="iframeCms"%\' GROUP BY( `id_cms`)';
		if ($results = Db::getInstance()->ExecuteS($sql))
			return $results;
		return false;
	}

	public function deleteAllCmsList()
	{
		$values = $this->getCmsList();
		if ($values != false)
			foreach ($values as $val)
			{
				$sql = 'DELETE FROM '._DB_PREFIX_.'cms WHERE id_cms=\''.$val['id_cms'].'\'';
				Db::getInstance()->Execute($sql);
				$sql = 'DELETE FROM '._DB_PREFIX_.'cms_block_page WHERE id_cms=\''.$val['id_cms'].'\'';
				Db::getInstance()->Execute($sql);
				$sql = 'DELETE FROM '._DB_PREFIX_.'cms_lang WHERE id_cms=\''.$val['id_cms'].'\'';
				Db::getInstance()->Execute($sql);
				$sql = 'DELETE FROM '._DB_PREFIX_.'cms_shop WHERE id_cms=\''.$val['id_cms'].'\'';
				Db::getInstance()->Execute($sql);
			}
	}

}
