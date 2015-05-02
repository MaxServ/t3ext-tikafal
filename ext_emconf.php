<?php
$EM_CONF['tikafal'] = array(
	'title' => 'Tika FAL Extractor',
	'description' => 'Provides a Tika FAL extractors.',
	'category' => 'services',
	'version' => '0.1.0',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearcacheonload' => 0,
	'author' => 'Michiel Roos',
	'author_email' => 'michiel@maxserv.com',
	'author_company' => 'MaxServ',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.3-5.5.99',
			'typo3' => '6.2.0-6.2.99',
			'filemetadata' => '0.0.0-0.0.0'
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
);
