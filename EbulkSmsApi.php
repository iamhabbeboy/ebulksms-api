<?php

 class EbulkSmsApi {
    private $json_url, $username, $apikey;

    public function __construct( $api, $user )
    {
        $this -> json_url = "http://api.ebulksms.com:8080/sendsms.json";
        $this -> username = $user;
        $this -> apikey = $api;
    } 

    private function gsm_split( $phone_numbers )
    {
        $gsm = [];
        $country_code = '234';
        $arr_recipient = explode( ',', $phone_numbers );

        foreach( $arr_recipient as $recipient ) {
            $mobilenumber = trim( $recipient );
            if ( substr( $mobilenumber, 0, 1 ) === '0' ) {
                $mobilenumber = $country_code . substr( $mobilenumber, 1 );
            } else if ( substr( $mobilenumber, 0, 1 ) == '+' ) {
                $mobilenumber = substr( $mobilenumber, 1 );
            }
            $generated_id = uniqid( 'int_', false );
            $generated_id = substr( $generated_id, 0, 30 );
            $gsm['gsm'][] = array( 'msidn' => $mobilenumber, 'msgid' => $generated_id );
        }
      return $gsm;
    }

    public function send( $sendername, $msgtxt, $recipient )
    {
        $gsm = $this -> gsm_split( $recipient );
        $message = array(
            'sender' => $sendername,
            'messagetext' => $msgtxt
        );
        $request = array( 'SMS' => array(
            'auth' => array(
                'username' => $this -> username,
                'apikey' => $this -> apikey
            ),
            'message' => $message,
            'recipients' => $gsm
        ));
        $json_data = json_encode( $request );
        if ( $json_data ) {
            $response = $this->request_handler( $json_data, array('Content-Type: application/json'));
            $result = json_decode( $response );
            return $result -> response -> status;
        } else {
            return false;
        }
    }

    /*
     *@var $json_data json data, $headers array 
    */
    private function request_handler( $data, $headers = array() ) 
    {
        $php_errormsg = ''; 
        if ( is_array( $data ) )
        {
            $data = http_build_query( $data, '', '&' );
        }
        $params = array( 'http' => array(
            'method' => 'POST',
            'content' => $data
        ));

        if ( $headers !== null )
        {
            $params['http']['header'] = $headers;
        }
        $ctx = stream_context_create( $params );
        $fp = fopen( $this->json_url, 'rb', false, $ctx );
        if ( ! $fp )
        {
            return "Error: gateway is inaccessible";
        }
        try{
            $response = stream_get_contents( $fp );
            if ( $response === false )
            {
                throw new Exception( "Problem reading data from $url, $php_errormsg");
            }
            return $response;
        } catch( Exception $e ) {
            $response = $e -> getMessage();
            return $response;
        }
    }

 }