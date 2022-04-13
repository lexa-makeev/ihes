<?php
/**
 * The FreshmailAPI
 */

if( ! class_exists('FreshmailAPI') ):
class FreshmailAPI {

    private $api_secret   = null;
    private $api_key      = null;
    private $response    = null;
    private $rawResponse = null;
    private $httpCode    = null;
    private $contentType = 'application/json';

    const host   = 'https://api.freshmail.com/';
    const prefix = 'rest/';
    const defaultFilePath = '/tmp/';
    //--------------------------------------------------------------------------

	public function __construct( $key, $secret ) {

		$this->api_key = $key;
		$this->api_secret = $secret;
	}

    public function getErrors() {

		if ( isset( $this->errors['errors'] ) ) {
            return $this->errors['errors'];
        }

        return false;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getRawResponse() {
        return $this->rawResponse;
    }

    public function getHttpCode() {
        return $this->httpCode;
    }

    public function setContentType( $contentType = '' ) {
        $this->contentType = $contentType;
        return $this;
    }

    public function doRequest( $strUrl, $arrParams = array(), $boolRawResponse = false ) {

		if ( empty($arrParams) ) {
            $strPostData = '';
        }
		elseif ( $this->contentType == 'application/json' ) {
            $strPostData = json_encode( $arrParams );
        }
		elseif ( !empty($arrParams) ) {
            $strPostData = http_build_query( $arrParams );
        }

        $strSign = sha1( $this->api_key . '/' . self::prefix . $strUrl . $strPostData . $this->api_secret );

        $arrHeaders = array();
        $arrHeaders[] = 'X-Rest-ApiKey: ' . $this->api_key;
        $arrHeaders[] = 'X-Rest-ApiSign: ' . $strSign;

        if ($this->contentType) {
            $arrHeaders[] = 'Content-Type: '.$this->contentType;
        }

        $resCurl = curl_init( self::host . self::prefix . $strUrl );
        curl_setopt( $resCurl, CURLOPT_HTTPHEADER, $arrHeaders );
        curl_setopt( $resCurl, CURLOPT_HEADER, true);
        curl_setopt( $resCurl, CURLOPT_RETURNTRANSFER, true);

		if( defined('WP_DEBUG') && WP_DEBUG ) {
			curl_setopt( $resCurl, CURLOPT_SSL_VERIFYPEER, false );
		}

        if ($strPostData) {
            curl_setopt( $resCurl, CURLOPT_POST, true);
            curl_setopt( $resCurl, CURLOPT_POSTFIELDS, $strPostData );
        }

        $this->rawResponse = curl_exec( $resCurl );
        $this->httpCode = curl_getinfo( $resCurl, CURLINFO_HTTP_CODE );

        if ($boolRawResponse) {
            return $this->rawResponse;
        }

        $this->_getResponseFromHeaders($resCurl);

        if (is_array($this->response) == false) {
            throw new Exception('Connection error - curl error message: '.curl_error($resCurl).' ('.curl_errno($resCurl).')');
        }

        return $this->response;
    }

    private function _getResponseFromHeaders($resCurl) {

        $header_size = curl_getinfo($resCurl, CURLINFO_HEADER_SIZE);
        $header = substr($this->rawResponse, 0, $header_size);
        $TypePatern = '/Content-Type:\s*([a-z-Z\/]*)\s/';
        preg_match($TypePatern, $header, $responseType);
        if(strtolower($responseType[1]) == 'application/zip') {
            $filePatern = '/filename\=\"([a-zA-Z0-9\.]+)\"/';
            preg_match($filePatern, $header, $fileName);
            file_put_contents(self::defaultFilePath.$fileName[1], substr($this->rawResponse, $header_size));
            $this->response = array('path' =>self::defaultFilePath.$fileName[1]);
        } else {
            $this->response = json_decode( substr($this->rawResponse, $header_size), true );
        }
        return $this->response;
    }
}
endif;
