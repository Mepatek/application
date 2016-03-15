<?php
/**
 * Created by PhpStorm.
 * User: pepa
 * Date: 15.03.2016
 * Time: 12:23
 */

namespace Mepatek\Entity;

use Mepatek\FileSystem;
use Exception;

class File extends \SplFileInfo
{
	/** @var  FileSystem */
	protected $fileSystem;

	/**
	 * File constructor.
	 *
	 * @param string     $fileName
	 * @param FileSystem $fileSystem
	 * @param bool       $fileNameCpFs
	 */
	public function __construct($fileName, FileSystem $fileSystem, $fileNameCpFs = false)
	{
		$this->fileSystem = $fileSystem;
		if (!$fileNameCpFs) {
		}
		parent::__construct($fileName);
	}

	/**
	 * Get filename converted to UTF8
	 *
	 * @return string
	 */
	public function getFilenameUtf8()
	{
		return $this->fileSystem->cpFsToUtf8($this->getFilename());
	}

	/**
	 * Get basename converted to UTF8
	 *
	 * @return string
	 */
	public function getBasenameUtf8()
	{
		return $this->fileSystem->cpFsToUtf8($this->getBasename());
	}

	/**
	 * Get path converted to UTF8
	 *
	 * @return string
	 */
	public function getPathUtf8()
	{
		return $this->fileSystem->cpFsToUtf8($this->getPath());
	}

	/**
	 * Get pathname converted to UTF8
	 *
	 * @return string
	 */
	public function getPathnameUtf8()
	{
		return $this->fileSystem->cpFsToUtf8($this->getPathname());
	}

	/**
	 * Get realPath converted to UTF8
	 *
	 * @return string
	 */
	public function getRealPathUtf8()
	{
		return $this->fileSystem->cpFsToUtf8($this->getRealPath());
	}

	/**
	 * Move file
	 *
	 * @param string $destination (UTF8!)
	 *
	 * @return boolean
	 */
	public function move($destination)
	{
		if ($this->createFolders($destination)) {
			try {
				rename($this->getRealPath(), $this->fileSystem->utf8TocpFs($destination));
				return true;
			} catch (Exception $e) {
				return false;
			}
		}
		return false;
	}

	/**
	 * Create all folders in path if not exist
	 *
	 * @param string $fileName (UTF8)
	 *
	 * @return boolean
	 */
	protected function createFolders($fileName)
	{
		$fileName = $this->fileSystem->utf8TocpFs($fileName);
		try {
			$folder = dirname($fileName);
			if (file_exists($folder)) {
				if (is_dir($folder)) {
					return true;
				} else {
					return false;
				}
			}
			mkdir($folder, 0777, true);
			return file_exists($folder);
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Copy file
	 *
	 * @param string $destination (UTF8!)
	 *
	 * @return boolean
	 */
	public function copy($destination)
	{
		if ($this->createFolders($destination)) {
			try {
				copy($this->getRealPath(), $this->fileSystem->utf8TocpFs($destination));
				return true;
			} catch (Exception $e) {
				return false;
			}
		}
		return false;
	}

	/**
	 * Delete file
	 *
	 * @return boolean
	 */
	public function delete()
	{
		try {
			unlink($this->getRealPath());
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

}
