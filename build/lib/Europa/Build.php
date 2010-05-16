<?php

class Europa_Build
{
	protected $_xml;
	
	protected $_packages = array();
	
	protected $_basePath;
	
	protected $_zipBase;
	
	public function __construct($fromFile, $basePath = './', $zipBase = null)
	{
		if (!is_file($fromFile)) {
			throw new Europa_Build_Exception(
				'Build file '
				. $fromFile
				. ' could not be found.',
				Europa_Build_Exception::FILE_NOT_FOUND
			);
		}
		
		// the xml file defining the release schema
		$this->_xml = new pQuery($fromFile);
		
		// normalize base path
		if ($basePath) {
			$basePath = realpath($basePath);
			$basePath = $basePath . DIRECTORY_SEPARATOR;
		}
		
		// normalize zip base path
		if ($zipBase) {
			$zipBase = trim($zipBase, '/\\') . DIRECTORY_SEPARATOR;
		}
		
		// set the normalized base path
		$this->_basePath = $basePath;
		$this->_zipBase  = $zipBase;
	}
	
	public function addPackage($package)
	{
		// if it's already added, do nothing
		if (in_array($package, $this->_packages)) {
			return $this;
		}
		
		$package = $this->_xml->find('//package[@id="' . $package . '"]');
		
		if ($package->length) {
			// add to list of packages
			$this->_packages[] = $package->attr('id');
			
			// if this package has any dependencies, add them also
			foreach ($package->find('//dependency') as $dependency) {
				$dep = $dependency->text();
				
				// dependency on all packages
				if ($dep === '*') {
					foreach ($this->_xml->find('//package') as $comp) {
						$this->addPackage($comp->attr('id'));
					}
				} else {
					$this->addPackage($dep);
				}
			}
		}
		
		return $this;
	}
	
	public function save($as)
	{
		return $this->zip($as);
	}
	
	public function push($filename)
	{
		// temporary zip file name
		$tmpZipName = md5(rand() . microtime() . rand());
		
		$this->zip($tmpZipName);
		
		// output
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename=' . $filename);
		readfile($tmpZipName);
		
		// cleanup
		unlink($tmpZipName);
		
		exit;
	}
	
	protected function zip($fullpath)
	{
		// create a new zip file object
		$zip = new ZipArchive();
		
		// make sure the zip can be created and open it
		if (!$zip->open($fullpath, ZipArchive::OVERWRITE)) {
			throw new Europa_Build_Exception(
				'Cannot create file '
				. $fullpath
				. '</strong>.',
				Europa_Build_Exception::ZIP_CREATE_FAIL
			);
		}
		
		// add all package files to the zip file
		foreach ($this->_packages as $package) {
			// find all package files
			foreach (
				$this->_xml->find('//package[@id="' . $package . '"]/file') 
				as $file
			) {
				$file  = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $file->text());
				$file  = $this->_basePath . $file;
				$files = array();
				$parts = explode(DIRECTORY_SEPARATOR, $file);
				
				// allow wildcards
				if (end($parts) === '*') {
					array_pop($parts);
					$dirs = new RecursiveIteratorIterator(
						new RecursiveDirectoryIterator(implode(DIRECTORY_SEPARATOR, $parts)),
						RecursiveIteratorIterator::SELF_FIRST
					);
					// add each item if it's a file
					foreach ($dirs as $item) {
						if ($item->isDir()) {
							continue;
						}
						$files[] = $item->getPathname();
					}
				// or absolute file paths
				} else {
					$file = realpath($file);
					if (!$file) {
						continue;
					}
					$files = array($file);
				}
				
				// add each individual file to the zip file
				foreach ($files as $file) {
					if (is_file($file)) {
						// normalize the file so it appears in the intended path in the zip
						$file = substr($file, strlen($this->_basePath));
						$zip->addFile($file, $this->_zipBase . $file);
					}
				}
			}
		}
		
		// close the zip file
		$zip->close();
		
		return $this;
	}
}