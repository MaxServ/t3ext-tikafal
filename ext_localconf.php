<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()->registerExtractionService('MaxServ\\Tikafal\\Service\\Tika');

$extractorConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
if (isset($extractorConfiguration['auto_extract']) && (bool)$extractorConfiguration['auto_extract']) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_extfilefunc.php']['processData'][] = 'MaxServ\\Tikafal\\Hook\\FileUploadHook';
}
unset($extractorConfiguration);
