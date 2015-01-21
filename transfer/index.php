<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);
require_once('UploadHandler.php');
require_once('DownloadHandler.php');
require_once('../classes/Permissions.php');

session_start();

if(!empty($_SESSION["group_id"]) AND hasGroupFlag('u')) {
	$upload_handler = new UploadHandler();
	$download_handler = new DownloadHandler();
}

