<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged In</title>
    <?php 
        echo "Great! You logged in with microsoft!!<br/><br/>";
        // echo "<pre>",var_dump($_POST),"</pre>";
        session_start();
        if(isset($_SESSION['access_token']) && isset($_SESSION['refresh_token'])){
            // echo "<h1>", $_SESSION['access_token'], "<br/><br/>", $_SESSION['refresh_token'],"</h1>","<br/>";
            getDetailsFromAccessToken();
        }
        else{
            getAccessRefershTokens();
        }

        function getAccessRefershTokens(){
            if(array_key_exists('code', $_GET)){
                $client_id = // Replace this with your client id;;
                $client_secret = // Replace this with your client secret id;
                $redirect = "http://localhost/SSO/Logged_in.php";
                
                $code = $_GET["code"];
            
                $curl = curl_init();
                $tenantId = // Replace this with your tenant id;;
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPHEADER => array(
                        "Content-type"=>"application/x-www-form-urlencoded",
                        "Content-Length"=>144
                    ),
                    CURLOPT_POSTFIELDS => array(
                                                "grant_type" => "authorization_code",
                                                "client_id" => $client_id,
                                                "client_secret" => $client_secret,
                                                "code" => $code,
                                                "redirect_uri" => $redirect,
                                                "prompt"=>"consent"),
                ));
            
                $response = json_decode(curl_exec($curl),1);
                $err = curl_error($curl);
                // $_POST['refresh_token'] = $response["refresh_token"];
                if (array_key_exists('error', $response)) {
                    echo "cURL Error #:" . $err;

                } else {
                    echo "<pre>",var_dump($response),"</pre>";
                    $_SESSION['access_token'] = $response["access_token"];
                    $_SESSION['refresh_token'] = $response["refresh_token"];
                    getDetailsFromAccessToken();
                }
                curl_close($curl);
                
            }
            else{
                echo "Didn't receive any code in url to generate Access and refresh Tokens<br/>";
            }
        }
        function getDetailsFromAccessToken(){
            if (array_key_exists ('access_token', $_SESSION)){
                $_SESSION['t'] = $_SESSION['access_token'];
                $t = $_SESSION['t'];
            
                $ch = curl_init ();
                curl_setopt ($ch, CURLOPT_HTTPHEADER, array ('Authorization: Bearer '.$t, 'Conent-type: application/json'));
            
                curl_setopt ($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/me/");
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            
                $rez = json_decode (curl_exec ($ch), 1);
            
                if (array_key_exists ('error', $rez)){  
                    generateNewAccessToken();
                }
                else{
                    echo "<pre>",var_dump($rez),"</pre>";
                }
                    curl_close($ch);
            }
            else{
                echo "There is no Access token is present in session";
            }
        } 

        function generateNewAccessToken(){
            $refresh_token_curl = curl_init();
            // echo "<pre>",var_dump($_POST),"</pre>";
            // echo "<h1>Hii</h1>";
            curl_setopt_array($refresh_token_curl, array(
                CURLOPT_URL => "https://login.microsoftonline.com/da464968-10f5-4b7b-8217-d6ab822e3949/oauth2/v2.0/token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => array(
                    "Content-type"=>"application/x-www-form-urlencoded",
                    "Content-Length"=>144
                ),
                CURLOPT_POSTFIELDS => array(
                                        'client_id'=>// // Replace this with your client id;,
                                        'scope'=>'https://graph.microsoft.com/user.read',
                                        'refresh_token'=>$_SESSION['refresh_token'],
                                        'grant_type'=>'refresh_token',
                                        'client_secret'=> // // Replace this with your client secret id;,
                                        ),
            ));
                
            $refresh_token_response = json_decode(curl_exec($refresh_token_curl),1);
            $_SESSION['access_token'] = $refresh_token_response["access_token"];
            $_SESSION['refresh_token'] = $refresh_token_response["refresh_token"];;
            curl_close($refresh_token_curl);
            getDetailsFromAccessToken();
        }
    
        if(isset($_GET['action'])){
            if($_GET['action'] == 'logout'){
                $tenantId = // Replace this with your client tenant id;
                $logout_page = "https://localhost/SSO/Logout.php";
                $logout_url = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/logout?post_logout_redirect_uri=$logout_page";
                header("Location: $logout_url");
            }
        }
           
    ?>
</head>
<body>
    <a href="?action=logout">Logout</a>
</body>
</html>