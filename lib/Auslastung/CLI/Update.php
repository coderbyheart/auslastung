<?php

//  +--------------------------------------------------+
//  | Copyright (c) Markus Tacker                      |
//  | All rights reserved.                             |
//  +--------------------------------------------------+
//  | This source code is protected by international   |
//  | copyright law and may not be distributed without |
//  | written permission by                            |
//  |   Markus Tacker                                  |
//  |   Senefelderstr. 63                              |
//  |   63069 Offenbach                                |
//  |   web:   http://coderbyheart.de                  |
//  |   email: m@tacker.org                            |
//  +--------------------------------------------------+

/**
 * Updates the app
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Update.php 183 2012-01-02 10:27:29Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage CLI
 */

/**
 * Updates the app
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: Update.php 183 2012-01-02 10:27:29Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage CLI
 */
class Auslastung_CLI_Update extends Auslastung_CLI_Base
{
	/**
	 * @see EngineRoom_CLI_IAppModule::getName()
	 */
	public function getName()
	{
		return 'Application update module';
	}

	/**
	 * Stores the current svn revision
	 */
	public function doSaveSvnRev()
	{
		$svnrev = exec('`which svnversion`');
		
		if (file_put_contents($this->app->getConfig()->getFile('data/svnrev.php'), '<?php $this->setSvnRev(\'' . $svnrev . '\');' )) {
			$this->cliApp->log('Saved svn revision information (' . $svnrev . ')');
		} else {
			throw new Auslastung_Exception('Failed to store svn revision information');
		}

	}

	/**
	 * compresses javascript and css
	 */
	public function doCompress()
	{
		$config = $this->app->getConfig();
		$yuicprog = $config->getFile('external/yuicompressor-2.4.2/build/yuicompressor-2.4.2.jar');
		// JavaScript
		$sourceJS = $config->getFile('htdocs/Auslastung.unpacked.js');
		$targetJS = $config->getFile('htdocs/Auslastung.js');
		$fp = fopen($sourceJS, 'w+');
		flock($fp, LOCK_EX);
		foreach(Auslastung_Response_HTML::$jsFiles as $js) {
			fputs($fp, file_get_contents($config->getFile('htdocs/js/' . $js)));
			fputs($fp, "\n");
		}
		flock($fp, LOCK_UN);
		fclose($fp);
		passthru('java -jar ' . $yuicprog . ' --type js --charset utf-8 -o ' . $targetJS . ' ' . $sourceJS);
		unlink($sourceJS);
		$this->cliApp->log('Compressed JavaScript');

		// CSS
		$sourceCSS = $config->getFile('htdocs/Auslastung.unpacked.css');
		$targetCSS = $config->getFile('htdocs/Auslastung.css');
		$fp = fopen($sourceCSS, 'w+');
		flock($fp, LOCK_EX);
		foreach(Auslastung_Response_HTML::$cssFiles as $css) {
			fputs($fp, file_get_contents($config->getFile('htdocs/css/' . $css)));
			fputs($fp, "\n");
		}
		flock($fp, LOCK_UN);
		fclose($fp);
		passthru('java -jar ' . $yuicprog . ' --type css --charset utf-8 -o ' . $targetCSS . ' ' . $sourceCSS);
		unlink($sourceCSS);
		$this->cliApp->log('Compressed CSS');
	}
}