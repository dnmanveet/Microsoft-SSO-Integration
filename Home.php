<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <?php 
        if(isset($_GET['action'])){
            if($_GET['action'] == 'login'){
                // echo "Clicked";
                $tenantId = // Replace this with your tenant id ;
                $login_url = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/authorize";

                //   
                // https://login.microsoftonline.com/da464968-10f5-4b7b-8217-d6ab822e3949/oauth2/v2.0/authorize?
                // client_id=1257f031-add1-48c4-9e8e-2f43e2e1b6af
                // &response_type=id_token
                // &redirect_uri=http://localhost/PHP_Assignment/user-signed-out.php
                // &scope=openid
                // &response_mode=fragment
                // &state=12345
                // &nonce=678910              "
                session_start ();
                $_SESSION['state']=session_id();
                $query_string = [
                    "client_id" => // Replace this with your client id;,
                    "response_type" => "code",
                    // "response_mode" => "query",
                    "redirect_uri" => "http://localhost/SSO/Logged_in.php",
                    "scope" => "openid offline_access https://graph.microsoft.com/user.read",
                    "state" => $_SESSION['state'],
                    "nonce"=>abcde,
                ];
                $login_url = $login_url.'?'.http_build_query($query_string);
                header("location: $login_url");
            }
            // header("Location: ./Logged_in.php");
            
        }
    ?>
</head>
<body>
    <!-- <form action="?action=login" method="post">
        <button type="submit">Login with Microsoft</button>
    </form> -->
    <a href="?action=login">Login with Microsoft</a>
</body>
</html>