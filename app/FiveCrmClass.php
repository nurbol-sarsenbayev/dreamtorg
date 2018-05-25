<?php

class FiveCrmClass
{

    var $public_token;
    var $api_url = 'https://api.5crm.ru/ctm/form/send';
    //var $api_url = 'http://api2.5crm.ru/crm/api_new/public/ctm/form/send';

    var $post;

    public function __construct($public_token, $website, $post)
    {
        $this->public_token = $public_token;
        $this->website = $website;
        $this->post = $post;
    }

    public function send()
    {
        $curl = $this->sentCurl($this->api_url, $this->getAllData());
    }

    private function sentCurl($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);

        return $server_output;
    }

    private function getAllData()
    {
        $final_data = array_merge($this->getCustomData(), $this->getCookieData(), $this->getData(), array(
            'url' => $this->website,
            'public_token' => $this->public_token,
            'callback' => 'callback_001'
        ));

        return $final_data;
    }

    private function getCustomData()
    {
        $data = array();
        $count = 50;
        for ($i = 0; $i < $count; $i++) {
            $field = 'f_' . $i;
            if (isset($this->post[$field]) && $this->post[$field] != '') {
                $data[$field] = $this->post[$field];
            }
        }
        return $data;
    }

    private function getData()
    {

        $data = array();

        $fields = array(
            'name',
            'phone',
            'email',
            'description'
        );

        foreach ($fields as $field) {
            if (isset($this->post[$field]) && $this->post[$field] != '') {
                $data[$field] = $this->post[$field];
            }
        }

        return $data;
    }

    private function getCookieData()
    {
        $data = array();

        $prefix = '_five_crm_';

        $cookie_fields = array(
            'utm_source',
            'utm_campaign',
            'utm_medium',
            'utm_term',
            'utm_content',
            'utm_5crm',
            'keyword',
            'guid'
        );

        foreach ($cookie_fields as $field) {
            $field_full = $prefix . $field;
            if (isset($_COOKIE[$field_full]) && $_COOKIE[$field_full] != '') {
                $data[$field] = $_COOKIE[$field_full];
            }
        }

        return $data;
    }

}