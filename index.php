<?php 
	require_once 'TemplateEngine.php';
	
	$configs['title'] = '***Test page***';
	$configs['charset'] = 'utf8';
	$configs['site_slogan'] = 'Тестовая страница';
	$configs['a'] = '2';
	$configs['$a'] = '1';
	$configs['arr'] = array('foo', 'bar', 'hallo', 'world');
	$configs['$arr'] = array(1,2,3,4,5);
	$configs['$ar'] =  array("foo" => "bar","bar" => "foo");
	$configs['$v'] = '-=triumph=-';
	$configs['c'] = 5;
	
	$generator = new html_generator();
	$generator -> load_template('template.html');
	$generator -> mount_vars($configs);
	$generator -> if_compiler();
	$generator -> foreach_compiler();
	$generator->print_out();
?>