<?php

$request = $_GET;
if(isset($request['getRate']))
{
    $format = strtolower($request['format']) == 'json' ? 'json' : 'xml';

    require_once("connection.php");

    $data = json_decode(base64_decode($request['getRate']),true);
    $username = $data['username'];
    $password = $data['password'];
    $license = $data['license'];

    $query = "SELECT * FROM users WHERE user_name='".$username."' AND password='".$password."' AND access_license='".$license."'";

    $result = mysqli_query($connection,$query);
    $user = mysqli_fetch_assoc($result);

    if(!$user){
        return false;
    }

    $query = "SELECT * FROM bbb_shipping_rate WHERE rate_id IN (".$user['services'].")";
    $rates = array();
    $result = mysqli_query($connection,$query);

    while ($rate = mysqli_fetch_assoc($result))
    {
        $rates[] = array('name'=>$rate['rate_name'],'value'=>$rate['rate_value']);
    }

    mysqli_close();

    if(empty($rates)) {
        return false;
    }

    if ($format == 'json') {
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        $response = json_encode(array('rates'=>$rates));
        echo $response;
        return $response;
    }else{
        // trả ra dữ liệu dưới dạng xml
        $response .=  '<rates>';
            foreach($rates as $rate) {
                $response .= '<rate>';
                $response .= '<name>'.$rate['name'].'</name>';
                $response .= '<value>'.$rate['value'].'</value>';
                $response .=  '</rate>';
            }
        $response .= '</rates>';
        return $response;
    }

}else{
    return false;
}
?>
