<?php
namespace Ia\Sdata;

/**
 *
 * @author Aaron Lozier <aaron@informationarchitech.com>
 */

class Conn
{

	protected $_options = array(
			'hostname'		=> false,
			'website'		=> 'sdata',
			'application'	=> 'MasApp',
			'contract'		=> 'MasContract',
			'company'		=> false,
		);

	protected $_requiredOptions = array(
			'hostname','website','application','contract','company'
		);

	public function __construct($options)
	{
		$this->_options = array_merge($this->_options,$options);
		$this->_validateOptions();
		return $this;
	}

	public function getUrlPrefix($includeHostname = true)
	{
		$this->_validateOptions();
		$url = (($includeHostname) ? $this->_options['hostname'] : '').'/'.
			$this->_options['website'].'/'.
			$this->_options['application'].'/'.
			$this->_options['contract'].'/'.
			$this->_options['company'].'/';
		return $url;
	}

	public function getOption($key)
	{
		return (isset($this->_options[$key])) ? $this->_options[$key] : false;
	}

	protected function _validateOptions()
	{

		foreach($this->_requiredOptions as $key){
			if(!isset($this->_options[$key]) || !$this->_options[$key]){
				throw new \Exception('`'.$key.'` is a required configuration option.');
			}
		}
	}



}