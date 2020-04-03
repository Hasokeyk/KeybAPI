<?php

	require (__DIR__)."/functions.php";
	require (__DIR__)."/vendor/autoload.php";

	//HATALARI GÖSTERME
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	//HATALARI GÖSTERME

	//YEREL SAAT
	date_default_timezone_set('Europe/Istanbul');
	//YEREL SAAT

	//DEĞİŞKENLER
	ini_set('memory_limit', '2048M');
	//DEĞİŞKENLER

	//VERİTABANI BAĞLANTISI
	global $db;
	//$db = new SQLite3('ziggi.db');
	$db = new mysqli('localhost','root','','dbname');
	try {
		//$db->prepareOn();
		$db->lastErrorMsg();
		//$db->query("SET NAMES 'UTF-8'");
	} catch (\Exception $th) {
		echo "Veritabanı hatası";
		exit;
	}
	//VERİTABANI BAĞLANTISI

	//CROSS DOMAİN
	/*
	$domainID = trim(strip_tags($_GET['ID']));
	$domains = $db->query("SELECT domain FROM domains WHERE domain_id = '".$domainID."'");
	$domains[]['domain'] = 'domain.com';

	$domainCheck = false;
	if(isset($_SERVER['HTTP_ORIGIN'])){
		$domainParse = parseURL($_SERVER['HTTP_ORIGIN']);
		foreach($domains as $domain){
			if($domainParse['domain'] == $domain['domain']){
			    header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
			    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
				header('Access-Control-Allow-Headers: Accept, Content-Type, Authorization, X-Requested-With, keybAPI');
				header('Access-Control-Allow-Credentials: true');
			    $domainCheck = true;
			    break;
			}
		}
	}
	if($domainCheck === false){
		//header("HTTP/1.1 403 Access Forbidden");
	    //header("Content-Type: text/plain");
	    echo 'Hatalı istek';
	    exit;
	}
	*/
	//CROSS DOMAİN

	//GEREKLİ DEĞİŞKENLER
	define('ROOT',(__DIR__));
	define('CACHE',ROOT.'/cache/');
	define('UPLOADDIR',ROOT.'/uploads/');
	//GEREKLİ DEĞİŞKENLER