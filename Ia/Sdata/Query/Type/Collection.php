<?php
namespace Ia\Sdata\Query\Type;

/**
 *
 * @author Aaron Lozier <aaron@informationarchitech.com>
 */

class Collection extends \Ia\Sdata\Query
{

	public function getEntries()
	{
		if($this->_entries===null){
			$this->_entries = array();
			foreach($this->getXmlResult()->entry as $entry){
				$this->_entries[] = $entry->payload->{$this->getResourceKind()};
			}
		}
		return $this->_entries;
	}	

}