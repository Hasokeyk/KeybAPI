<?php

	use Ahc\Jwt\JWT;

	class question extends keybAPI{

		function getNewQuestion($param){
			global $db;

			$tokenValide = parent::tokenValidate($param->deviceID);
			if($tokenValide){
				$sql = "SELECT * FROM questions WHERE level_raw = '".($param->level??'beginner')."' ORDER BY random() LIMIT 1";
				$question = $db->query($sql)->fetchArray(SQLITE3_ASSOC);
				if($question != null){
					$question['video_url'] = 'https://api.rit.im/videos/'.$question['file_name'];
					return [
						'status' => 'true',
						'data' => $question
					];
				}else{
					return [
						'status' => 'false',
						'message' => 'Kayıt çekilemedi!'
					];
				}

			}else{
				parent::throwError(301);
			}

		}

	}