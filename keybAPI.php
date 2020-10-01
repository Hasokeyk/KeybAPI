<?php
    
    class keybAPI{
        
        public $secretKey = null;
        public $routePath = null;
        public $tokenKey = 'KEYBAPI';
        
        public function route($route = []){
            global $mysqli;
            
            header("content-type: application/json");
            
            $sPath = $this->parseURL($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])['path'];
            if(isset($route[$sPath]) and $route[$sPath] != null){
                
                $this->routePath = $route[$sPath];
                
                if($_SERVER['REQUEST_METHOD'] !== $route[$sPath]['method']){
                    $this->throwError('300');
                }
                
                if(isset($route[$sPath]['auth']) and $route[$sPath]['auth'] == 'no'){
                    $validate = ['status' => true];
                }else{
                    $validate = $this->tokenValidate();
                }
                
                if(isset($validate['status']) and $validate['status'] == true){
                    
                    if(file_exists(ROOT.'/api'.$sPath.'.php')){
                        
                        require ROOT.'/api'.$sPath.'.php';
                        
                        $sPath = ltrim($sPath, '/');
                        if(class_exists($sPath)){
                            
                            try{
                                $query = file_get_contents('php://input');
                                $query = json_decode($query);
                                
                                if(!isset($query->query)){
                                    $this->throwError('305');
                                }
                                
                                try{
                                    if(is_callable([new $sPath, $query->query])){
                                        
                                        $call = call_user_func_array([
                                            new $sPath,
                                            $query->query,
                                        ], [
                                            $query->param??null,
                                        ]);
                                        
                                        if($call != null){
                                            if(isset($this->routePath['cache']) and $this->routePath['cache'] == 'no'){
                                                print_r(json_encode($call));
                                            }else{
                                                $filename = md5($sPath.$query->query.(http_build_query($query->param??[]))).'.json';
                                                if(file_exists(CACHE.$filename)){
                                                    echo file_get_contents(CACHE.$filename);
                                                    exit;
                                                }else{
                                                    $ac = fopen(CACHE.$filename, 'w+');
                                                    fwrite($ac, json_encode($call));
                                                    fclose($ac);
                                                    print_r(json_encode($call));
                                                }
                                            }
                                        }
                                    }else{
                                        $this->throwError('306');
                                    }
                                }
                                catch(Exception $err){
                                    $this->throwError('306');
                                }
                                
                            }
                            catch(Exception $err){
                                $this->throwError('303');
                            }
                            
                        }else{
                            $this->throwError('302');
                        }
                        
                    }else{
                        $this->throwError('301');
                    }
                    
                }else{
                    $this->throwError('502');
                }
                
            }else{
                $this->throwError('301');
            }
            
        }
        
        public function tokenValidate($key = null){
            if(isset($_SERVER['HTTP_'.$this->tokenKey])){
                $token = $_SERVER['HTTP_'.$this->tokenKey];
                try{
                    
                    $decrypt = $this->decrypt($this->secretKey, $token);
                    if($decrypt == false){
                        $payload = false;
                    }else{
                        $payload = true;
                    }
                    
                    return [
                        'status' => $payload,
                        'data'   => $decrypt??null,
                    ];
                }
                catch(\Exception $err){
                    $this->throwError('400');
                }
            }else{
                $this->throwError('501');
            }
        }
        
        public function throwError($code){
            header("content-type: application/json");
            $errorMsg = json_encode([
                'status'  => $code,
                'message' => $this->lang($code),
            ]);
            echo $errorMsg;
            exit;
        }
        
        public function lang($code){
            $codes = [
                '500' => 'JWT kütüphanesi bulunamadı',
                '501' => 'Token Gerekli',
                '502' => 'Geçersiz Token',
                '400' => 'Girilen Token Geçerli değil',
                '300' => 'Geçersiz Request İsteği POST/GET',
                '301' => 'Geçersiz API isteği',
                '302' => 'Geçersiz Class',
                '303' => 'API HATASI',
                '304' => '"name" parametresi gereklidir',
                '305' => '"query" parametresi gereklidir',
                '306' => 'Fonksiyon bulunamadı',
            ];
            return $codes[$code]??'Bilinmeyen Hata';
        }
        
        function searchArrayValue($array = [], $needle, $haystrack){
            
            foreach($array as $key => $value){
                if(is_array($value)){
                    $sub = $this->searchArrayValue($value, $needle, $haystrack);
                    if($sub){
                        return $sub;
                    }
                }else{
                    if($key == $needle){
                        if($value == $haystrack){
                            return $value;
                            break;
                        }
                    }
                }
            }
            
            return false;
        }
        
        function arrayValueLists($array = [], $keyName){
            
            foreach($array as $key => $value){
                
                if($key == $keyName){
                    $this->keys[] = $value;
                }
                
                if(is_array($value)){
                    $this->arrayValueLists($value, $keyName);
                }
                
            }
            
            return $this->keys;
        }
        
        function parseURL($url, $retdata = true){
            $url = substr($url, 0, 4) == 'http' ? $url : 'http://'.$url; //assume http if not supplied
            if($urldata = parse_url(str_replace('&', '&', $url))){
                $path_parts = pathinfo($urldata['host']);
                $tmp        = explode('.', $urldata['host']);
                $n          = count($tmp);
                if($n >= 2){
                    if($n == 4 || ($n == 3 && strlen($tmp[($n - 2)]) <= 3)){
                        $urldata['domain']    = $tmp[($n - 3)].".".$tmp[($n - 2)].".".$tmp[($n - 1)];
                        $urldata['tld']       = $tmp[($n - 2)].".".$tmp[($n - 1)]; //top-level domain
                        $urldata['root']      = $tmp[($n - 3)]; //second-level domain
                        $urldata['subdomain'] = ($n == 4 ? $tmp[0] : ($n == 3 && strlen($tmp[($n - 2)]) <= 3)) ? $tmp[0] : '';
                    }else{
                        $urldata['domain']    = $tmp[($n - 2)].".".$tmp[($n - 1)];
                        $urldata['tld']       = $tmp[($n - 1)];
                        $urldata['root']      = $tmp[($n - 2)];
                        $urldata['subdomain'] = $n == 3 ? $tmp[0] : '';
                    }
                }
                //$urldata['dirname'] = $path_parts['dirname'];
                $urldata['basename']  = $path_parts['basename'];
                $urldata['filename']  = $path_parts['filename'];
                $urldata['extension'] = $path_parts['extension'];
                $urldata['base']      = $urldata['scheme']."://".$urldata['host'];
                $urldata['abs']       = (isset($urldata['path']) && strlen($urldata['path'])) ? $urldata['path'] : '/';
                $urldata['abs']       .= (isset($urldata['Something is wrong']) && strlen($urldata['Something is wrong'])) ? '?'.$urldata['Something is wrong'] : '';
                //Set data
                if($retdata){
                    return $urldata;
                }else{
                    $this->urldata = $urldata;
                    return true;
                }
            }else{
                //invalid URL
                return false;
            }
        }
        
        function encrypt($key, $text){
            $cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', 'cbc', '');
            mcrypt_generic_init($cipher, $key, '12345678');
            $encrypted = mcrypt_generic($cipher, $text);
            mcrypt_generic_deinit($cipher);
            $encrypted = bin2hex($encrypted);
            return $encrypted;
        }
        
        function decrypt($key, $encrypted){
            $encrypted = hex2bin($encrypted);
            $cipher    = mcrypt_module_open(MCRYPT_BLOWFISH, '', 'cbc', '');
            mcrypt_generic_init($cipher, $key, '12345678');
            $decrypted = mdecrypt_generic($cipher, $encrypted);
            mcrypt_generic_deinit($cipher);
            return $decrypted;
        }
    }