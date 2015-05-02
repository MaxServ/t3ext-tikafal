<?php
namespace MaxServ\Tikafal\Hook;

/**
 *  Copyright notice
 *
 *  ⓒ 2015 Michiel Roos <michiel@maxserv.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is free
 *  software; you can redistribute it and/or modify it under the terms of the
 *  GNU General Public License as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful, but
 *  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *  or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 *  more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\ExtractorRegistry;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtility;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtilityProcessDataHookInterface;

/**
 * This class allows metadata to be extracted automatically after
 * uploading a file.
 *
 * @category    Hook
 * @package     TYPO3
 * @subpackage  tikafal
 * @author      Michiel Roos <michiel@maxserv.com>
 * @license     http://www.gnu.org/copyleft/gpl.html
 */
class FileUploadHook implements ExtendedFileUtilityProcessDataHookInterface {

	/**
	 * @var \TYPO3\CMS\Core\Resource\Index\ExtractorInterface[]
	 */
	static protected $extractionServices = NULL;

	/**
	 * @param string $action The action
	 * @param array $cmdArr The parameter sent to the action handler
	 * @param array $result The results of all calls to the action handler
	 * @param ExtendedFileUtility $pObj The parent object
	 * @return void
	 */
	public function processData_postProcessAction($action, array $cmdArr, array $result, ExtendedFileUtility $pObj) {
		if ($action === 'upload') {
			/** @var File[] $fileObjects */
			$fileObjects = array_pop($result);
			if (!is_array($fileObjects)) {
				return;
			}

			foreach ($fileObjects as $fileObject) {
				$storageRecord = $fileObject->getStorage()->getStorageRecord();
				if ($storageRecord['driver'] === 'Local') {
					$this->runMetaDataExtraction($fileObject);
				}
			}
		}
	}

	/**
	 * Runs the metadata extraction for a given file.
	 *
	 * @param File $fileObject
	 * @return void
	 * @see \TYPO3\CMS\Core\Resource\Index\Indexer::runMetaDataExtraction
	 */
	protected function runMetaDataExtraction(File $fileObject) {
		if (static::$extractionServices === NULL) {
			$extractorRegistry = ExtractorRegistry::getInstance();
			static::$extractionServices = $extractorRegistry->getExtractorsWithDriverSupport('Local');
		}

		$newMetaData = array(
			0 => $fileObject->_getMetaData()
		);
		foreach (static::$extractionServices as $service) {
			if ($service->canProcess($fileObject)) {
				$newMetaData[$service->getPriority()] = $service->extractMetaData($fileObject, $newMetaData);
			}
		}
		ksort($newMetaData);
		$metaData = array();
		foreach ($newMetaData as $data) {
			$metaData = array_merge($metaData, $data);
		}
		$fileObject->_updateMetaDataProperties($metaData);
		$metaDataRepository = MetaDataRepository::getInstance();
		$metaDataRepository->update($fileObject->getUid(), $metaData);
	}

}
