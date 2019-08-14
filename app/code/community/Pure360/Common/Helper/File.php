<?php

/**
 * @package   Pure360\Common
 * @copyright 2013 Pure360.com
 * @version   1.0.1
 * @author    Stewart Waller <stewart.waller@pure360.com>
 */
class Pure360_Common_Helper_File extends Mage_Core_Helper_Abstract
{

	private $_output_folder;

	private $delimiter;

	private $enclosure;

	public function __construct()
	{
		$this->_output_folder = Mage::getBaseDir('var') . DS . 'export' . DS . 'email';
		$this->delimiter = ','; // tab character
		$this->enclosure = '"';
	}

	public function getOutputFolder()
	{
		$this->pathExists($this->_output_folder);
		return $this->_output_folder;
	}

	public function getFilePath($filename)
	{
		return $this->getOutputFolder() . DS . $filename;
	}

	public function getFilenamesForSlug($slug)
	{
		$files = array();

		foreach(glob($this->getOutputFolder() . DS . $slug . "*.*") as $filename)
		{
			$files[] = $filename;
		}

		return $files;
	}

	public function cleanFiles($slug)
	{
		$files = array();

		foreach(glob($this->getOutputFolder() . DS . $slug . "*.*") as $filename)
		{
			$this->deleteFile($filename);
		}

		return $files;
	}

	public function readFile($filename, $base64 = false)
	{
		$contents = '';

		$handle = fopen($filename, "r");

		$contents .= fread($handle, filesize($filename));

		fclose($handle);

		return $base64 ? base64_encode($contents) : $contents;
	}

	public function deleteFile($fileName)
	{
		$tmp = dirname(__FILE__);
		
		if(!defined( "WINDOWS_SERVER" ))
		{
			if(strpos($tmp, '/', 0) !== false)
			{
				define('WINDOWS_SERVER', false);
				
			} else
			{
				define('WINDOWS_SERVER', true);
			}
		}
		
		$error = false;

		if(!WINDOWS_SERVER)
		{
			if(!unlink($fileName))
			{
				$error = true;
			}
		} else
		{
			$lines = array();
			exec("DEL /F/Q \"$fileName\"", $lines, $error);
		}

		return $error;
	}

	public function wipeFile($filename)
	{
		file_put_contents($filename, "");
	}

	function formatBytes($size, $sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'))
	{
		if ($size == 0) return('0 Bytes');
		return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i]);
	}

	public function pathExists($path)
	{
		if(!is_dir($path))
		{
			mkdir($path, 0774, true);
		}

		return;
	}

	public function readCsv($filename)
	{
		$contents = array();

		$handle = fopen($filename, "r");

		while(false !== ($row = fgetcsv($handle, 5000)))
		{
			$contents[] = $row;
		}

		fclose($handle);

		return $contents;
	}

	/**
	 * Output an array to the output file FORCING Quotes around all fields
	 * @param $filepath
	 * @param $csv
	 */
	public function outputForceQuotesCSV($filepath, $csv)
	{

		$fqCsv = $this->arrayToCsv($csv, chr(9), '"', true, false);
		// Open for writing only; place the file pointer at the end of the file. If the file does not exist, attempt to create it.
		$fp = fopen($filepath, "a");

		// for some reason passing the preset delimiter/enclosure variables results in error
		if(fwrite($fp, $fqCsv) == 0) //$this->delimiter $this->enclosure
		{
			Mage::throwException('Problem writing CSV file');
		}
		fclose($fp);
	}

	/**
	 * Output an array to the output file
	 * @param $filepath
	 * @param $csv
	 */
	public function outputCSV($filepath, $csv)
	{

		// Open for writing only; place the file pointer at the end of the file. If the file does not exist, attempt to create it.
		$handle = fopen($filepath, "a");

		// for some reason passing the preset delimiter/enclosure variables results in error
		if(fputcsv($handle, $csv, ',', '"') == 0) //$this->delimiter $this->enclosure
		{
			Mage::throwException('Problem writing CSV file');
		}

		fclose($handle);
	}

	protected function arrayToCsv(array $fields, $delimiter, $enclosure, $encloseAll = false, $nullToMysqlNull = false)
	{
		$delimiter_esc = preg_quote($delimiter, '/');
		$enclosure_esc = preg_quote($enclosure, '/');

		$output = array();
		foreach($fields as $field)
		{
			if($field === null && $nullToMysqlNull)
			{
				$output[] = 'NULL';
				continue;
			}

			// Enclose fields containing $delimiter, $enclosure or whitespace
			if($encloseAll || preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field))
			{
				$output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
			} else
			{
				$output[] = $field;
			}
		}

		return implode($delimiter, $output) . "\n";
	}

}
