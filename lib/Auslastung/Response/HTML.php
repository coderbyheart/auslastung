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
 * Displays a HTML page
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: HTML.php 182 2012-01-02 10:22:20Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Response
 */

/**
 * Displays a HTML page
 *
 * @author Markus Tacker <m@tacker.org>
 * @version $Id: HTML.php 182 2012-01-02 10:22:20Z m $
 * @copyright Markus Tacker
 * @package Auslastung
 * @subpackage Response
 */
class Auslastung_Response_HTML implements Auslastung_Response_Base
{
	private $app;

	public static $jsFiles = array(
		'mootools-core-nc.js',
		'mootools-more-nc.js',
		'Auslastung.js',
		'Auslastung/Model.js',
		'Auslastung/Model/User.js',
		'Auslastung/Util.js',
		'Auslastung/Clock.js',
		'Auslastung/PlanUpdater.js',
		'Auslastung/AuslastungTable.js',
		'Auslastung/Autocompleter.js',
		'Auslastung/FormRenderer.js',
		'Auslastung/FormRenderer/Assignment.js',
		'Auslastung/FormRenderer/Vacation.js',
        'Auslastung/FormRenderer/Appointment.js',
		'Auslastung/DatePicker.js',
		'Auslastung/actionMenu.js',
		'Auslastung/addMenu.js',
		'Auslastung/editMenu.js',
		'Auslastung/Dialog.js',
        'Auslastung/Dialog/Alert.js',
        'Auslastung/Dialog/Success.js',
		'Auslastung/checkResponse.js',
		'Auslastung/DatePicker.js',
		'Auslastung/DateHelper.js',
		'Auslastung/TabbedForm.js',
		'Auslastung/View.js',
		'Auslastung/View/Auth.js',
		'Auslastung/View/Main.js',
		'Auslastung/View/Organization.js',
		'Auslastung/View/Organization/Feed.js',
		'Auslastung/View/Profile.js',
	);

	public static $cssFiles = array(
		'reset-min.css',
		'styles.css',
		'datepicker.css'
	);

	public function __construct(Auslastung_App_Base $app)
	{
		$this->app = $app;
	}

	/**
	* Send response to client
	*/
	public function send()
	{
		header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>
    	<?php if ($this->app->getConfig()->isDeveloper()): ?>[DEV] <?php endif; ?>
		<?php echo htmlspecialchars($this->app->getConfig()->getName()); ?>
	</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="AUTHOR" content="Markus Tacker | http://coderbyheart.de/">
    <meta name="VERSION" content="<?php echo $this->app->getVersion(); ?>">
    <meta name="COPYRIGHT" content="&copy;2008-<?php echo strftime('%Y'); ?> Markus Tacker">
    <link rel="SHORTCUT ICON" type="image/x-icon" href="/img/favicon.ico">
    <?php if ($this->app->getConfig()->isDeveloper()): foreach(self::$cssFiles as $file): ?>
    <link rel="stylesheet" type="text/css" href="/css/<?php echo $file; ?>?<?php echo $this->app->getVersion(); ?>">
	<?php endforeach; else: ?>
	<link rel="stylesheet" type="text/css" href="/Auslastung.css?<?php echo $this->app->getVersion(); ?>">
	<?php endif; ?>
</head>
<body>
<!-- EOF: $Id: HTML.php 182 2012-01-02 10:22:20Z m $ -->
	<div id="header">
		<h1><?php echo htmlspecialchars($this->app->getConfig()->getName()); ?></h1>
	</div>
	<div id="main">
		<div id="about" class="unbox">
			<p>Mit <em><?php echo htmlspecialchars($this->app->getConfig()->getName()); ?></em> verwaltest Du die Aufgaben und Urlaube deines Teams um die Übersicht darüber zu behalten, <em>wer</em> <em>was</em> und <em>wann</em> macht.</p>

			<p>Bitte <a href="<?php echo $this->app->getConfig()->getTracUrl(); ?>/newticket">melde</a> Bugs, die du findest. Dies ist Version <a href="<?php echo $this->app->getConfig()->getTracUrl(); ?>/log/trunk?rev=<?php echo $this->app->getVersion(); ?>"><?php echo $this->app->getVersion(); ?></a> von <em><?php echo htmlspecialchars($this->app->getConfig()->getName()); ?></em>. </p>
			<p>Bereits gemeldete Fehler und Verbesserungsvorschläge findest Du <a href="<?php echo $this->app->getConfig()->getTracUrl(); ?>/wiki/ToDo">hier</a>. Was sich bereits so getan hat, ist im <a href="<?php echo $this->app->getConfig()->getTracUrl(); ?>/log/trunk?rev=<?php echo $this->app->getVersion(); ?>">Changelog</a> nach zu lesen.</p>

			<p><em><?php echo htmlspecialchars($this->app->getConfig()->getName()); ?></em> wird entwickelt von <a href="http://coderbyheart.de/">Markus Tacker</a>.</p>
		</div>
	</div>
	<div id="footer">
		<ul>
			<li id="clock"></li>
			<li>&copy;2008-<?php echo strftime('%Y'); ?> <a href="http://coderbyheart.de/">Markus Tacker</a></li>
			<li><a href="<?php echo $this->app->getConfig()->getTracUrl(); ?>/wiki/ToDo">TODO</a></li>
			<li><a href="<?php echo $this->app->getConfig()->getTracUrl(); ?>/log/trunk?rev=<?php echo $this->app->getVersion(); ?>">Changelog</a></li>
			<li><?php echo htmlspecialchars($this->app->getConfig()->getName()); ?> <?php echo $this->app->getVersion(); ?></li>
		</ul>
	</div>
	<!-- Despite AJAX beeing the de-facto standard for RIAs we need this form to have Firefox store your login information. Sigh. -->
	<form id="credentials" action="/" method="post">
		<fieldset>
			<input type="text" name="email" id="credentials_email" value="" />
			<input type="password" id="credentials_password" name="password" value="" />
		</fieldset>
	</form>
	<?php if ($this->app->getConfig()->isDeveloper()): foreach(self::$jsFiles as $file): ?>
	<script type="text/javascript" src="/js/<?php echo $file; ?>?<?php echo $this->app->getVersion(); ?>"></script>
	<?php endforeach; else: ?>
	<script type="text/javascript" src="/Auslastung.js?<?php echo $this->app->getVersion(); ?>"></script>
	<?php endif; ?>
</body>
</html>
<?php
	}
}