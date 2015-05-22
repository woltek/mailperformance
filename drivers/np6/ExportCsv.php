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

include_once ('../../config/config.inc.php');
include_once ('../../init.php');

require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'APIConnector'.DIRECTORY_SEPARATOR.'APIConnectorIncludes.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'PrestashopClasses'.DIRECTORY_SEPARATOR.'PrestashopClassesIncludes.php');
const DELIMITER = ';';

/**
 * Generate the csv export file
 */
function execute()
{

	// write on the output stream no file is created
	$output = fopen('php://output', 'w');
	$mperf = new MailPerformance();

	// get user settings and binding infos
	$import_bind = unserialize(Configuration::get(Constants::IMPORT_STTGS));

	$column_header_name = array ();
	$db_field_array = array ();

	// get all fields
	$fields = $mperf->fields->getIndexedFields();
	// if error we stop and show the error in the browser
	if ($fields == null)
	{
		echo $mperf->fields->erreur;
	}	
	else
	{

		// add http header to download the file
		header('Content-type: text/csv');
		header('Content-Type: application/force-download; charset=UTF-8');
		header('Cache-Control: no-store, no-cache');
		header('Content-disposition: attachment; filename="ExportPrestaNp6.csv"');

		// get the csv header from configuration files and the database field name
		foreach ($import_bind['fields'] as $dbname => $field_binding)
		{
			// if we import values
			if ($field_binding['apiFieldId'] != 0)
			{
				$column_header_name[] = $fields[$field_binding['apiFieldId']]->name;
				$db_field_array[] = $dbname;
			}
		}

		// add the csv header
		fputcsv($output, $column_header_name, DELIMITER);

		// sql request creation
		$fieldsql = '';
		foreach ($db_field_array as $dbfield)
			$fieldsql .= ', '.$dbfield;

		$fieldsql = ltrim($fieldsql, ',');
		$sql = 'SELECT '.$fieldsql.' FROM '._DB_PREFIX_.'customer';


		// loop over the sql results, outputting them
		if ($results = Db::getInstance()->ExecuteS($sql))
		{
			foreach ($results as $row)
			{
				foreach ($row as $key => $value)
				{
					// if data binding exist
					if (isset($import_bind['fields'][$key]['binding']))
						$row[$key] = $import_bind['fields'][$key]['binding'][$value];

						// check if date and try to format
					if ($fields[$import_bind['fields'][$key]['apiFieldId']]->type == TypeField::DATE)
						$row[$key] = date($import_bind['fields'][$key]['dateFormat'], strtotime($row[$key]));
				}
				// write the line
				fputcsv($output, $row, DELIMITER);
			}
		}
	}
}

execute();
