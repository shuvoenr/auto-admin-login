<?php

    function encrypt($string, $key) {
        $result = '';
        for($i=0, $k= strlen($string); $i<$k; $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)+ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }

    function decrypt($string, $key) {
        $result = '';
        $string = base64_decode($string);
        for($i=0,$k=strlen($string); $i< $k ; $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)-ord($keychar));
            $result.=$char;
        }
        return $result;
    }


    $key_encry = "et007";
    $username = encrypt("admin", $key_encry);
    $password = encrypt("inadmin1", $key_encry);

    //-- url encode and decode used as example "oNPVlJ2g06U=" contain "=" which is divider in url parameter
    $username = urlencode($username);
    $password = urlencode($password);


    echo "<b>Username Original :</b>  admin"."<br>";
    echo "<b>Username Encrypt  :</b>  $username"."<br>";
    echo "<b>Password Original  :</b>  inadmin"."<br>";
    echo "<b>Password Encrypt   : </b> $password"."<br>";

    //-- our URL Will be like this

    echo "<b>So URL will Be like This :</b> http://site-url.com/admin_path/?uen=".$username."&pae=".$password."&key=".$key_encry;