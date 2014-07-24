<?php
namespace Ia\Sdata\Query\Type;

/**
 *
 * @author Aaron Lozier <aaron@informationarchitech.com>
 */

class Single extends \Ia\Sdata\Query
{

	protected $_resourceSelector = null;
	
	protected $_entry = null;

	public function setResourceSelector($selector)
	{
		$this->_resourceSelector = $selector;
		return $this;
	}

	public function getUrl($prefix=null)
	{
		if($prefix===null)
			throw new \Exception('Prefix url is required.');
		if($this->_resourceKind===null)
			throw new \Exception('Resource kind is required.');
		if($this->_resourceSelector===null)
			throw new \Exception('Resource selector is required for single resource queries.');
		$url = $prefix . ''. $this->_resourceKind . '('. $this->_resourceSelector .')';
		return $url;
	}

	public function getEntry()
	{
		if($this->_entry===null){
			if($this->_resourceKind===null)
				throw new \Exception('Resource kind is required.');			
			$this->_entry = array();
			if(isset($this->getXmlResult()->payload->{$this->_resourceKind})){
				return $this->getXmlResult()->payload->{$this->_resourceKind};
			}
		}
		return $this->_entries;
	}

}