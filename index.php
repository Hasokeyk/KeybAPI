<?php

	require "48186.php";
	require "keybAPI.php";

	$keybAPI = new keybAPI();
	//$keybAPI->jwtServerKey = '48186hasokeyk';

	$keybAPI->route([
		"/device" => [
			'method'    => 'post',
			'auth'      => 'no',
		],
		"/question" => [
			'method'    => 'post',
			'cache'     => 'no',
		],
	]);