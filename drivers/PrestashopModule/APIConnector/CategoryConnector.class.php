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

require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'APIConnectorIncludes.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'Categories.class.php');

class CategoryConnector extends APIConnector
{
	private $category_list;

	public function __construct()
	{
		if (func_num_args() == 1)
		{
			call_user_func_array('parent::__construct', func_get_args());

			// def prop
			$this->path = '/categories';
			$this->category_list = array ();
		}
	}

	/**
	 * get Categories
	 *
	 * @return array Category
	 */
	public function getCategory()
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result[0]) && !Category::isJsonValid($this->last_result[0]))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		// clear the tab
		$this->category_list = array ();
		foreach ($this->last_result as $cat)
			$this->category_list[] = new Category($cat);

		return $this->category_list;
	}

	/**
	 * renvoie la list de Category (ne requette le serveur qu'a la premiere utilisation)
	 *
	 * @return array Category
	 */
	public function getCategoryList()
	{
		if (count($this->category_list) <= 0)
			return $this->getCategory();

		return $this->category_list;
	}

	/**
	 * get a category by Id
	 *
	 * @param int $id
	 * @return Category ou null en cas d'erreur
	 */
	public function getCategoryById($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path.'/'.$id);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!Category::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new Category($this->last_result);
	}

	/**
	 * update the category
	 * warning: don't change the id
	 *
	 * @param Category $Category
	 * @return Renvoie null en cas d'erreur ou la Category modifié
	 */
	public function updateCategory(Category $category)
	{
		// array to send the model corectly
		$content = array (
				'name' => $category->name,
				'description' => $category->description
		);

		list($this->last_result, $this->last_error) = $this->rest_client->put($this->path.'/'.$category->id, $content);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!Category::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new Category($this->last_result);
	}

	/**
	 * delete the Category
	 *
	 * @param int $id
	 */
	public function deleteCategoryById($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->delete($this->path.'/'.$id);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return false;

		return true;
	}

	/**
	 * delete Category
	 *
	 * @param Category $Category
	 * @return mixed
	 */
	public function deleteCategory(Category $category)
	{
		return $this->deleteCategoryById($category->id);
	}

	/**
	 * create a category
	 *
	 * @param Category $Category
	 * @return \Category
	 */
	public function createCategoryByCategory(Category $category)
	{
		return $this->createCategory($category->name, $category->description);
	}

	/**
	 * create a category
	 *
	 * @param string $name
	 * @param string $description
	 * @return \Category
	 */
	public function createCategory($name, $description)
	{
		// array to send the model corectly
		$content = array (
				'name' => $name,
				'description' => $description
		);

		list($this->last_result, $this->last_error) = $this->rest_client->post($this->path, $content);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!Category::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}
		return new Category($this->last_result);
	}
}