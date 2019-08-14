<?php

/**
 * Facade for handling file create, upload and delete
 *
 * @package other
 * @subpackage facade
 *
 */
class Pure360_Package_Other_File extends Pure360_Entity
{

	public $__FACADE = "bus_facade_file";

	/**
	 * Create file request
	 * 
	 * @param string $fileCategory
	 * @param string $fileName
	 * @param integer $totalChunks
	 * @param string $overwriteExistingInd
	 * @return array
	 */
	public function create($fileCategory, $fileName, $totalChunks, $overwriteExistingInd)
	{	
		// Category of the file. Up to 200 characters
		$fileInput["fileCategory"] = $fileCategory;

		// Name of the file. Up to 200 characters
		$fileInput["fileName"] = $fileName;

		// Total number of chunks for this file
		$fileInput["totalChunks"] = $totalChunks;

		// Whether or not to overwrite an existing file.
		// If Y, any existing file data will be deleted.
		// If N, and a file exists, an error will be returned
		$fileInput["overwriteExistingInd"] = $overwriteExistingInd;

		$outputData = $this->call("create", $fileInput, null);

		return $outputData;
	}
	
	/**
	 * Call this method to upload a chunk of data to a file.
	 * 
	 * @param string $fileCategory
	 * @param string $fileName
	 * @param string $transferEncoding
	 * @param integer $chunk
	 * @param string $chunkData
	 * @return array
	 */
	public function upload($fileCategory, $fileName, $transferEncoding, $chunk, $chunkData)
	{
		// Category of the file
		$fileInput["fileCategory"] = $fileCategory;

		// Name of the file to upload
		$fileInput["fileName"] = $fileName;

		// Encoding used to transfer the file data:
		//	"plain" if the contents of the fileData element should be saved directly to a file
		//			(the encoding and line endings of the XML request will be used;
		//			NOTE: leading and trailing white space will be stripped, and a single
		//			newline character appended to the end)
		//	"base64" if the contents of the fileData element are base64-encoding
		//			(this allows any kind of text or binary data to be included in the chunk)
		$fileInput["transferEncoding"] = $transferEncoding;

		// Number of the chunk currently being uploaded.
		// They do not need to be uploaded in sequence, but attempting to upload a chunk that has
		// already been received will result in an error
		$fileInput["chunk"] = $chunk;

		// Actual file data (must be text or base64
		// Must be wrapped in CDATA section ie: <![CDATA[ data ]]>
		$fileInput["chunkData"] = trim($chunkData);

		$outputData = $this->call("upload", $fileInput, null);

		return $outputData;
	}

}
