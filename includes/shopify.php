<?php
class Shopify {
    public $api_key;
    public $password;
    public $shop_url;

    public function set_apikey($key) {
        $this->api_key = $key;
    }

    public function set_password($pw) {
        $this->password = $pw;
    }

    public function set_url($url) {
        $this->shop_url = $url;
    }

    public function rest_api($api_endpoint, $query = array(), $method = 'GET') {
        //$url = 'https://{apikey}:{password}@{hostname}/admin/api/{version}/{resource}.json';
        $url = 'https://' . $this->api_key . ':' . $this->password . '@' . $this->shop_url . $api_endpoint;

        if( in_array( $method, array('GET', 'DELETE') ) && !is_null($query) ) {
            $url = $url . '?' . http_build_query($query);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ( $method != 'GET' && in_array( $method, array( 'POST','PUT' ) ) ) {
            if( is_array($query) ) $query = http_build_query($query);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        }

        $response = curl_exec($ch);
        $error = curl_errno($ch);
        $errmsg = curl_error($ch);
        
        curl_close($ch);

        if($error) {
            return $errmsg;
        } else {
            $response = preg_split("/\r\n\r\n|\n\n|\r\r/",$response, 2);
            $headers = array();
            $content = explode("\n", $response[0]);
            $headers['status'] = $content[0];

            array_shift($content);

            foreach($content as $c) {
                $data = explode(":", $c);
                $headers[ trim($data[0]) ] = trim($data[1]);
            }

            return array( 'headers' => $headers, 'body' => $response[1]);
        }
    }
}