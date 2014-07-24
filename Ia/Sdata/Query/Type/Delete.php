<?php
namespace Ia\Sdata\Query\Type;

/**
 *
 * @author Aaron Lozier <aaron@informationarchitech.com>
 */

class Delete extends Single
{

    protected function _configureCurl()
    {
        $ch = parent::_configureCurl();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");   
        return $ch;
    }

    public function getXmlResult()
    {
        $this->getRawResult();
        if($this->_httpCode=='200')
            $this->_rawResult = '<success>true</success>';
        return parent::getXmlResult();
    }


}