<?php

class cannadropApi {

    public function canna_api() {
        
        $curl = curl_init();

        $url = "https://fakestoreapi.com/products/";

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",    
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $productList = json_decode($response);

        $productfinal = array();

        foreach ($productList as $key => $product) {

            $imageurl = $product->image;
            $image = file_get_contents($imageurl); 
            $base64 = base64_encode($image);
            $imageid = substr($imageurl, strrpos($imageurl, '/') + 1);

            $newproduct = array ( 
                "product" => array(
                    "title"=> $product->title,
                    "body_html"=> $product->description,
                    "vendor"=> $product->category,
                    "product_type"=> $product->category,
                    "published"=> true,
                    "image" => array(
                        array(
                        'filename' => $imageid,
                        'attachment' => $base64
                        )
                    )
                )
            );
            array_push($productfinal, $newproduct);
        }
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $productfinal;
        }

    }
}

