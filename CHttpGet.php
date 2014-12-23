<?php
/**
 * Get a image from a remote server using HTTP GET and If-Modified-Since.
 *
 */
class CHttpGet
{
    private $request  = [];
    private $response = [];
    
    
    
    /**
    * Constructor
    *
    */
    public function __construct()
    {
        $this->request['header'] = [];
    }    
    


    /**
     * Set the url for the request.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->request['url'] = urlencode($url);
        return $this;
    }    



    /**
     * Set header fields for the request.
     *
     * @param string $field
     * @param string $value
     *
     * @return $this
     */
    public function setHeader($field, $value)
    {
        $this->request['header']['field'] = $value;
        return $this;
    }    



    /**
     * Perform the request.
     *
     * @return boolean
     */
    public function get()
    {
        $status;

        $options = [
            CURLOPT_URL => $this->request['url'],
            CURLOPT_HEADER => 1,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_RETURNTRANSFER => true,
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        
        //curl_setopt($ch, CURLOPT_URL, $this->request['url']);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        
        //curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        
        
        
    
    


        
        
        return $status;
    }    
}
