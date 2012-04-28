<?php
	/*
		Gatherer Content Management System
		Copyright © 2007-2012 by Eric Hokanson

		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.
		
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.
		
		You should have received a copy of the GNU Lesser General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/
    
    // Define path to application directory
    defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

    // Define application environment
    defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

    // Ensure library/ is on include_path
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../library'),
        get_include_path(),
    )));
    
    require_once 'Zend/Loader/Autoloader.php';
    
    $autoloader = Zend_Loader_Autoloader::getInstance();
    
    $ini = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
    $db = Zend_Db::factory($ini->resources->db);

	// Our old CMS DB
	try {
		$import_db = Zend_Db::factory('Pdo_Mysql', array(
			'host'     => 'localhost',
			'username' => 'olduser',
			'password' => 'oldpass',
			'dbname'   => 'olddb',
			'options'  => $options
		));
		$import_db->getConnection();
	} catch (Zend_Db_Adapter_Exception $e) {
		die($e->getMessage());
	}

	if ($_GET['type'] == 'mambo')
		require 'mambo.inc';
	elseif ($_GET['type'] == 'drupal')
	    require 'drupal.inc';
?>
