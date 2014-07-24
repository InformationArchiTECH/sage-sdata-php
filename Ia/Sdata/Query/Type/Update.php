<?php
namespace Ia\Sdata\Query\Type;

/**
 *
 * @author Aaron Lozier <aaron@informationarchitech.com>
 */

class Update extends Create
{

    protected $_resourceSelector = null;
    
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

    public function getPayload()
    {
        $payload = '<sdata:payload>' .
                    '<'.$this->getResourceKind().' sdata:uri="'.$this->getUrl($this->_conn->getUrlPrefix(false)).'" xmlns="">';

        foreach($this->getFieldValues() as $fieldName=>$fieldValue){
            $payload .= '<'.$fieldName.'>'.$fieldValue.'</'.$fieldName.'>';
        }

        if($this->getChildResourceKind()!==null){
            foreach($this->getChildFieldValues() as $child=>$childFieldValues){
                $payload .= '<'.$this->getChildResourceKind().'>';
                foreach($childFieldValues as $fieldName => $fieldValue){
                    $payload .= '<'.$fieldName.'>'.$fieldValue.'</'.$fieldName.'>';
                }
                $payload .= '</'.$this->getChildResourceKind().'>';
            }
        }

        $payload .= '</'.$this->getResourceKind().'></sdata:payload>';

        $xml = '<entry xmlns:atom="http://www.w3.org/2005/Atom"' .
                ' xmlns:xs="http://www.w3.org/2001/XMLSchema"' .
                ' xmlns:cf="http://www.microsoft.com/schemas/rss/core/2005"' .
                ' xmlns="http://www.w3.org/2005/Atom" xmlns:sdata="http://schemas.sage.com/sdata/2008/1"' .
                ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' .
                ' xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/" '. 
                ' xmlns:sync="http://schemas.sage.com/sdata/sync/2008/1" '.
                ' xmlns:sme="http://schemas.sage.com/sdata/sme/2007" '.
                ' xmlns:http="http://schemas.sage.com/sdata/http/2008/1">' .
                $payload .'</entry>';

        return $xml;
    }

    protected function _configureCurl()
    {
        $ch = parent::_configureCurl();
        $xml = $this->getPayload();
        curl_setopt($ch, CURLOPT_POST, false );        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");   
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/atom+xml;type=entry',
            'Content-Length: '.strlen($xml)
        ));    
        return $ch;
    }  

}