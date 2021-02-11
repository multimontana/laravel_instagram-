<?php


namespace App\Services;


class InstagramService
{
    public function getUserInfo($data)
    {
        $ch = curl_init();

        curl_setopt(
            $ch,
            CURLOPT_URL,
            "https://www.instagram.com/web/search/topsearch/?context=blended&rank_token=0.6738022034184186&include_reel=true&limit=5"
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            "query=" . $data->username
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        //cors error
        // dd(json_decode($server_output));

        return $server_output;
    }
}
