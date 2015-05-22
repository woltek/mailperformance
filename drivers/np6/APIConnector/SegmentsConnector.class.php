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
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models/Segment.class.php');

class SegmentsConnector extends APIConnector
{
	private $list_segment;

	/**
	 * constructor of SegmentsConnector
	 */
	public function __construct()
	{
		if (func_num_args() == 1)
		{
			call_user_func_array('parent::__construct', func_get_args());

			// def prop
			$this->path = '/segments';
			$this->list_segment = array ();
		}
	}

	/**
	 * Retrieve all segments
	 *
	 * @return mixed
	 */
	public function getSegments()
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (isset($this->last_result[0]) && !Segment::isJsonValid($this->last_result[0]))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		foreach ($this->last_result as $segment)
			$this->list_segment[] = new Segment($segment);

		return $this->list_segment;
	}

	/**
	 * Retrieve all segments by type
	 *
	 * @param $type (Static,Dynamic,Split)
	 * @param bool $includefor_test
	 *        	= false
	 * @return mixed
	 */
	public function getSegmentByTypes($type, $includefor_test = false)
	{
		if (count($this->list_segment) <= 0)
			$this->getSegments();

		$return_list = array ();
		foreach ($this->list_segment as $segment)
			if ($segment->type == $type && ($includefor_test || !$segment->for_test))
				$return_list[] = $segment;

		return $return_list;
	}

	/**
	 * Retrieve Segment by ID
	 *
	 * @param mixed $id
	 * @return \Segment or null if error
	 */
	public function getSegmentById($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path.'/'.$id);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!Segment::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new Segment($this->last_result);
	}

	/**
	 * create a new Segment
	 *
	 * @param Segment $segment
	 * @return Segment
	 */
	public function createSegment(Segment $segment)
	{
		// convert the object to the array that will transform into json
		$content = array (
				'type' => $segment->type,
				'id' => 0,
				'name' => $segment->name,
				'description' => $segment->description,
				'creation' => Field::getFormatDate(time()),
				'expiration' => Field::getFormatDate(strtotime($segment->expiration_date)),
				'isTest' => $segment->for_test,
				'targetsCount' => 0
		);

		list($this->last_result, $this->last_error) = $this->rest_client->post($this->path, $content);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!Segment::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new Segment($this->last_result);
	}

	/**
	 * update a Segment
	 *
	 * @param Segment $segment
	 * @return Segment
	 */
	public function updateSegment(Segment $segment)
	{
		// convert the object to the array that will transform into json
		$content = array (
				'type' => $segment->type,
				'id' => $segment->id,
				'name' => $segment->name,
				'description' => $segment->description,
				'creation' => Field::getFormatDate(strtotime($segment->creation_date)),
				'expiration' => Field::getFormatDate(strtotime($segment->expiration_date)),
				'isTest' => $segment->for_test
		);

		list($this->last_result, $this->last_error) = $this->rest_client->put($this->path.'/'.$segment->id, $content);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		if (!Segment::isJsonValid($this->last_result))
		{
			$this->erreur = $this->jsonErrorMessage();
			return null;
		}

		return new Segment($this->last_result);
	}

	/**
	 * retrieve 30 first targets id from segment
	 *
	 * @param Segment $segment
	 * @return array of target Id
	 */
	public function getTargetFromSegment(Segment $segment)
	{
		return $this->getTargetFromSegmentId($segment->id);
	}

	/**
	 * retrieve 30 first targets id from segment id
	 *
	 * @param int $id
	 * @return array of target Id
	 */
	public function getTargetFromSegmentId($id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->get($this->path.'/'.$id.'/targets');

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		return $this->last_result;
	}

	/**
	 * Add a Target to a segment
	 *
	 * @param mixed $segment_id
	 *        	segment Id where add the target
	 * @param mixed $target_id
	 *        	target id from the targets in the segment
	 */
	public function setTargetInSegment($segment_id, $target_id)
	{
		list($this->last_result, $this->last_error) = $this->rest_client->post($this->path.'/'.$segment_id.'/targets/'.$target_id,null);

		$this->erreur = $this->getError($this->last_result, $this->last_error);
		if (isset($this->erreur))
			return null;

		return $this->last_result;
	}
}
