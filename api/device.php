<?php

	class device extends keybAPI{

		function regDevice($param){
			global $db;

			if(isset($param->deviceID) and !empty($param->deviceID)){
				$jwt = $this->jwt($param->deviceID);
				$token = $jwt->encode([
					'seldosTime' => time()
				]);
				if($token){

					$askDeviceID = $db->query("SELECT * FROM devices WHERE deviceID = '".$param->deviceID."'")->fetchArray();
					if($askDeviceID === false){
						$db->exec("INSERT INTO devices (deviceID,token) VALUES('".$param->deviceID."','".$token."')");
						return [
							'status' => 'true',
							'data' => $token
						];
					}else{
						return [
							'status' => 'true',
							'message' => 'Aygıt Zaten Kayıtlı',
							'data' => $token
						];
					}
				}else{
					return [
						'status' => 'false',
						'message' => 'Token oluşturulamadı'
					];
				}
			}else{
				return [
					'status' => 'false',
					'message' => 'deviceID parametresi eksik'
				];
			}

		}

	}