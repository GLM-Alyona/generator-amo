<?php
function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);
  
    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
  }

  function post_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  
  $phone = $email = "";
  $phone = post_input($_POST["phone"]);
  $email = post_input($_POST["email"]);
  
  $subdomain = '124svetik12345'; //Поддомен нужного аккаунта
  $link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса


  /** Соберем данные для запроса */
  $data = [
      'client_id' => 'bac25e77-11f7-431f-bb64-94acde23e8da', // id нашей интеграции
      'client_secret' => 'nksUk7ZWLhB6YgjLfg1hlg1JFNaPCM2yVOdeyshLG2eILZYz19nR69z8TPv0SP0f', // секретный ключ нашей интеграции
      'grant_type' => 'authorization_code',
      'code' => 'def5020037b2367674157376c440c190ab624b7543b7a7193f3d79ed93c8b84da045ca4ed245f7f62957a73c38c82c82e0a5d1ac11be9037bed556b7f84afb7ef99318a2d0adabd4b5afbf186b3dac9e11b85a09a8629d1dabc233326ecc7fe6ae7c801c16a7cde945ff890a0a1d6b9e259f73d36f815153e1f187f1c686e0a134f8f3f9c780f7d09541b117efa748cfd4e854e38e8b8231f4f9ffd5154dab7ac6c0d8f5732f4afc0cce327237536ee365bbe9653df5191cacf52af7dafb609c7ea9457ad630795da9f48dbabb55a9d5b2d7347569b825d9d6f896054961bed58a5701ebabc968a5745a669b7996442660049ca241fc95d5e2060437f050f8b107899907e979c152f001ce6b7c93f1992e6f05edf5317717fceaac6191efe9d9049348f2ea4a563315f6d22bfea52d00520b1a1355c7c76d5daa5ce8670e8f0fd473d3d32eae918251dd4a17b23f9d71c92353e3d8d77835a1579dbd996923bb89cfb16c955625a29b64124bd502e60da31f83a3399c44af2f735c3e54d97fa891b82196e89fddece1f2cc27bf687404a3c8c78773fe5c7c9f54f694cdf904422f097233985e952a18060fa9e6e66584bc3f4e8f8c43c2b97cf0a5118c88785e0eca049fd219d95d7895bda6748a81e60efe9df0b27b9a03733a990241df898357b78253845bd3ceb133dbf6d5b7f6cad39cfbc5d5c690967219', // код авторизации нашей интеграции
      'redirect_uri' => 'https://example.com',// домен сайта нашей интеграции
  ];
  
  $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
  /** Устанавливаем необходимые опции для сеанса cURL  */
  curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
  curl_setopt($curl,CURLOPT_URL, $link);
  curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
  curl_setopt($curl,CURLOPT_HEADER, false);
  curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
  curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
  $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
  $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);


  /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
  $code = (int)$code;
  
  // коды возможных ошибок
  $errors = [
      400 => 'Bad request',
      401 => 'Unauthorized',
      403 => 'Forbidden',
      404 => 'Not found',
      500 => 'Internal server error',
      502 => 'Bad gateway',
      503 => 'Service unavailable',
  ];
  
  try
  {
      /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
      if ($code < 200 || $code > 204) {
          throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
         
      }
  }
  catch(\Exception $e)
  {
      die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
  }
  
  /**
   * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
   * нам придётся перевести ответ в формат, понятный PHP
   */
  
  $response = json_decode($out, true);
  
  $access_token = $response['access_token']; //Access токен
  $refresh_token = $response['refresh_token']; //Refresh токен
  $token_type = $response['token_type']; //Тип токена
  $expires_in = $response['expires_in']; //Через сколько действие токена истекает
  
  // выведем наши токены. Скопируйте их для дальнейшего использования
  // access_token будет использоваться для каждого запроса как идентификатор интеграции
  // var_dump($access_token);
  // debug_to_console($access_token);
  // var_dump($refresh_token);
  // debug_to_console($refresh_token);
  
  $arrContactParams = [
      // поля для сделки 
      "PRODUCT" => [
          "nameForm"	=> "Заявка - Алёна Гюлумян",
          "namePerson"	=> "Контакт Гюлумян Алёна",
          "phonePerson"	=> $phone,
          "emailPerson"	=> $email,
      ],
      // поля для контакта 
      "CONTACT" => [
          "namePerson"	=> "Контакт Гюлумян Алёна",
          "phonePerson"	=> $phone,
          "emailPerson"	=> $email,
      ]
  ];
  amoAddContact($access_token, $arrContactParams);
  function amoAddContact($access_token, $arrContactParams) {
  
    $contacts['request']['contacts']['add'] = array(
    [
      'name' => $arrContactParams["CONTACT"]["namePerson"],
      'tags' => 'авто отправка',
      'custom_fields'	=> [

          // EMAIL 
          [
              'id'	=> 493343,
              "values" => [
                  [
                      "value" => $arrContactParams["CONTACT"]["emailPerson"],
                  ]
              ]
          ],
        // ТЕЛЕФОН
           [
            'id'	=> 493345,
            "field_name" => "Телефон",
            "values" => [
                [
                    "value" => $arrContactParams["CONTACT"]["phonePerson"],
                ]
                    ]
          ]         
      ]
  ]
  );
  
  
      /* Формируем заголовки */
      $headers = [
          "Accept: application/json",
          'Authorization: Bearer ' . $access_token
      ];
      
      $link='https://124svetik12345.amocrm.ru/private/api/v2/json/contacts/set';
  
      $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
      /** Устанавливаем необходимые опции для сеанса cURL  */
      curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
      curl_setopt($curl,CURLOPT_URL, $link);
      curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
      curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($contacts));
      curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl,CURLOPT_HEADER, false);
      curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
      curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
      $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
      $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
      curl_close($curl);
      $Response=json_decode($out,true);
      $account=$Response['response']['account'];
      echo '<b>Данные о пользователе:</b>'; echo '<pre>'; print_r($Response); echo '</pre>';
  
      return $Response["response"]["contacts"]["add"]["0"]["id"];
  
  }
  //amoAddTask($access_token, $arrContactParams, false);
  function amoAddTask($access_token, $arrContactParams, $contactId = false) {
  
  
    $arrTaskParams = [  
    'add' => [
      0 => [
        'name'  => $arrContactParams["PRODUCT"]["nameForm"],
          //'price'         => $arrContactParams["PRODUCT"]["price"],
          'pipeline_id'   => '7326910',
          'tags'          => [
            'авто отправка',
            $arrContactParams["PRODUCT"]["nameForm"]
          ],
          'status_id'     => '10937736',
          'custom_fields'	=> [
            [
              'id'	=> 525741,
              "values" => [
                [
                  "value" => $arrContactParams["PRODUCT"]["namePerson"],
                ]
              ]
            ],
            /* ТЕЛЕФОН */
            [
              'id'	=> 493345,
              "values" => [
                [
                  "value" => $arrContactParams["PRODUCT"]["phonePerson"],
                ]
              ]
            ],
            /* EMAIL */
            [
              'id'	=> 493343,
              "values" => [
                [
                  "value" => $arrContactParams["PRODUCT"]["emailPerson"],
                ]
              ]
            ],
          ],
    
          'contacts_id' => [
            0 => $contactId,
          ],
        ],
      ],
    ];
    
    
      $link = "https://124svetik12345.amocrm.ru/api/v2/leads";
    
      $headers = [
            "Accept: application/json",
            'Authorization: Bearer ' . $access_token
      ];
    
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
      curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client-
      undefined/2.0");
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($arrTaskParams));
      curl_setopt($curl, CURLOPT_URL, $link);
      curl_setopt($curl, CURLOPT_HEADER,false);
      curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__)."/cookie.txt");
      curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__)."/cookie.txt");
      $out = curl_exec($curl);
      curl_close($curl);
      $result = json_decode($out,TRUE);
    
    }
    /* в эту функцию мы передаём текущий refresh_token */
  function returnNewToken($token) {
  
      $link = 'https://124svetik12345.amocrm.ru/oauth2/access_token';
  
      /** Соберем данные для запроса */
      $data = [
      'client_id' => 'bac25e77-11f7-431f-bb64-94acde23e8da',
      'client_secret' => 'nksUk7ZWLhB6YgjLfg1hlg1JFNaPCM2yVOdeyshLG2eILZYz19nR69z8TPv0SP0f',
          'grant_type' => 'refresh_token',
          'refresh_token' => $token,
          'redirect_uri' => 'https://example.com',
      ];
  
      /**
       * Нам необходимо инициировать запрос к серверу.
       * Воспользуемся библиотекой cURL (поставляется в составе PHP).
       * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
       */
      $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
      /** Устанавливаем необходимые опции для сеанса cURL  */
      curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
      curl_setopt($curl,CURLOPT_URL, $link);
      curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
      curl_setopt($curl,CURLOPT_HEADER, false);
      curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
      curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
      $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
      $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      curl_close($curl);
      /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
      $code = (int)$code;
      $errors = [
          400 => 'Bad request',
          401 => 'Unauthorized',
          403 => 'Forbidden',
          404 => 'Not found',
          500 => 'Internal server error',
          502 => 'Bad gateway',
          503 => 'Service unavailable',
      ];
  
      try
      {
          /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
          if ($code < 200 || $code > 204) {
              throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
          }
      }
      catch(\Exception $e)
      {
          die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
      }
  
      /**
       * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
       * нам придётся перевести ответ в формат, понятный PHP
       */
  
      $response = json_decode($out, true);
  
      if($response) {
  
          /* записываем конечное время жизни токена */
          $response["endTokenTime"] = time() + $response["expires_in"];
  
          $responseJSON = json_encode($response);
  
          /* передаём значения наших токенов в файл */
          $filename = "token.json";
          $f = fopen($filename,'w');
          fwrite($f, $responseJSON);
          fclose($f);
  
          $response = json_decode($responseJSON, true);
  
          return $response;
      }
      else {
          return false;
      }
  
  }
  //returnNewToken($refresh_token);
  function amoCRMScript($paramsTask) {
  
      /* получаем значения токенов из файла */
      $dataToken = file_get_contents("token.json");
      $dataToken = json_decode($dataToken, true);
  
      /* проверяем, истёкло ли время действия токена Access */
      if($dataToken["endTokenTime"] < time()) {
          /* запрашиваем новый токен */
          $dataToken = returnNewToken($dataToken["refresh_token"]);
          $newAccess_token = $dataToken["access_token"];
      }
      else {
          $newAccess_token = $dataToken["access_token"];
      }
  
      if($paramsTask["CONTACT"]) {
          $idContact = amoAddContact($newAccess_token, $paramsTask);
      }
  
      amoAddTask($newAccess_token, $paramsTask, $idContact);
  
  }
  amoCRMScript($arrContactParams);

/* ОТПРАВКА ПИСЬМА НА ПОЧТУ*/

$to ='glmalyona@gmail.com';
$email = clear_data($_POST['email']);
$phone = clear_data($_POST['phone']);
$subject ='Заявка с сайта - Гюлумян Алёна';

$headers = 'From: webmaster@example.com' . "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$message = 'Email: ' .$email."\n" . 'Телефон: ' .$phone "\n" .$headers;

function clear_data($val){
    $val = trim($val);
    $val = stripslashes($val);
    $val = htmlspecialchars($val);
    return $val;
}

mail($to, $subject, $message);
?>