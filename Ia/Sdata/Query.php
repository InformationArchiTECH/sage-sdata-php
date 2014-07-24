<?php
namespace Ia\Sdata;

/**
 *
 * @author Aaron Lozier <aaron@informationarchitech.com>
 */

class Query
{

	protected $_conn = null;

	protected $_resourceKind = null;

	protected $_rawResult = null;

	protected $_entries = null;

	protected $_queryParams = array();

	protected $_httpCode = null;

	public function __construct(Conn $conn)
	{
		$this->_conn = $conn;
		return $this;
	}

	public function reset()
	{
		$this->_rawResult = null;
		$this->_entries = null;
		return $this;
	}

	public function setResourceKind($kind=null)
	{
		if($kind===null)
			throw new \Exception('Resource kind is required');
		
		$this->reset();
		$this->_resourceKind = $kind;
		return $this;
	}

	public function getResourceKind()
	{
		return $this->_resourceKind;
	}

	public function getXmlResult()
	{
		libxml_use_internal_errors(true);
		$xmlResult = new \SimpleXMLElement(trim($this->getRawResult()));
		
		//error handling
		if(!$xmlResult){

			$errArr = array();

			$errors = libxml_get_errors();

		    foreach ($errors as $error) {
		        $errArr[] = display_xml_error($error, $xml);
		    }

		    libxml_clear_errors();

		    throw new \Exception(implode(', ',$errArr));
		}

		//error handling
		if(isset($xmlResult->diagnosis->severity) && $xmlResult->diagnosis->severity=='error'){
			throw new \Exception($xmlResult->diagnosis->message);
		}

		return $xmlResult;
	}

	protected $_curlUrl = null;

	protected function _configureCurl()
	{
		$ch = curl_init();
		$url = $this->_curlUrl = $this->getUrl($this->_conn->getUrlPrefix()) . $this->getQueryParams(true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		return $ch;
	}

	public function getCurlUrl()
	{
		return $this->_curlUrl;
	}

	public function getRawResult()
	{
		if($this->_rawResult===null)
		{
			$ch = $this->_configureCurl();
			
			$content = curl_exec ($ch);
			$content = str_replace($this->_conn->getOption('website').':','',$content);

			$err = curl_errno ( $ch );
			$errmsg = curl_error ( $ch );
			$header = curl_getinfo ( $ch );
			$httpCode = $this->_httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );

			curl_close($ch);

			if($errmsg)
				throw new \Exception($errmsg);

			$this->_rawResult = $content;

		}
		return $this->_rawResult;
	}

	public function getPayload()
	{
		throw new Exception('Payload not set or available for this class instance.');
	}

	public function getUrl($prefix=null)
	{
		if($prefix===null)
			throw new \Exception('Prefix url is required.');
		if($this->_resourceKind===null)
			throw new \Exception('Resource kind is required.');
		return $prefix . $this->_resourceKind;
	}

	public function getQueryParams($queryString=false)
	{
		if($queryString){
			$str = '';
			if($this->_queryParams){
				$str = '?';
				$i=0;
				foreach($this->_queryParams as $key=>$value){
					$i++;
					$str .= $key.'='.rawurlencode($value);
					if($i<sizeof($this->_queryParams)){
						$str .= '&';
					}
				}
			}
			return ($str);
		} else {
			return $this->_queryParams;
		}
	}

	public function setQueryParam($key,$value)
	{
		$this->_queryParams[$key] = $value;
		return $this;
	}

	public function setQueryParams($values)
	{
		$this->_queryParams = $values;
		return $this;
	}

	public function unsetQueryParam($key)
	{
		$this->_queryParams = array();
		return $this;
	}

	public function setRawResult($rawResult)
	{
		$this->_rawResult = $rawResult;
		return $this;
	}

}