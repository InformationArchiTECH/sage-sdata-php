<?php
namespace Ia\Sdata\Query\Type;

/**
 *
 * @author Aaron Lozier <aaron@informationarchitech.com>
 */

class Create extends \Ia\Sdata\Query
{

    protected $_fieldValues = array();

    protected $_childFieldValues = array();

    protected $_childResourceKind = null;

    public function getFieldValues()
    {
        return $this->_fieldValues;
    }

    public function setFieldValue($key,$value)
    {
        $this->_fieldValues[$key] = $value;
        return $this;
    }

    public function setFieldValues($values)
    {
        $this->_fieldValues = $values;
        return $this;
    }

    public function setChildResourceKind($kind=null)
    {
        if($kind===null)
            throw new \Exception('Resource kind is required');
        
        $this->reset();
        $this->_childResourceKind = $kind;
        return $this;
    }

    public function getChildResourceKind()
    {
        return $this->_childResourceKind;
    }

    public function setChildFieldValue($key,$value,$child)
    {
        if(!isset($this->_childFieldValues[$child]))
            $this->_childFieldValues[$child] = array();
        $this->_childFieldValues[$child][$key] = $value;
        return $this;
    }

    public function setChildFieldValues($values,$child)
    {
        $this->_childFieldValues[$child] = $values;
        return $this;
    }

    public function getChildFieldValues()
    {
        return $this->_childFieldValues;
    }

    public function getPayload()
    {
        $payload = '<sdata:payload>' .
                    '<'.$this->getResourceKind().' sdata:uri="'.$this->getUrl($this->_conn->getUrlPrefix()).'" xmlns="">';

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

        $xml = '<entry xmlns:atom="http://www.w3.org/2005/Atom" '.
            'xmlns:xs="http://www.w3.org/2001/XMLSchema" '.
            'xmlns:cf="http://www.microsoft.com/schemas/rss/core/2005" '.
            'xmlns="http://www.w3.org/2005/Atom" '.
            'xmlns:sdata="http://schemas.sage.com/sdata/2008/1" '.
            'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
            'xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/" '.
            'xmlns:sync="http://schemas.sage.com/sdata/sync/2008/1" '.
            'xmlns:sme="http://schemas.sage.com/sdata/sme/2007" '.
            'xmlns:http="http://schemas.sage.com/sdata/http/2008/1">'.
            $payload .'</entry>';

        return $xml;
    }

    protected function _configureCurl()
    {
        $ch = parent::_configureCurl();
        $xml = $this->getPayload();
        curl_setopt($ch, CURLOPT_POST, true );        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/atom+xml;type=entry',
            'Content-Length: '.strlen($xml)
        ));    
        return $ch;
    }

    public function getUrl($prefix=null)
    {
        if($prefix===null)
            throw new \Exception('Prefix url is required.');
        if($this->_resourceKind===null)
            throw new \Exception('Resource kind is required.');
        $url = $prefix . ''. $this->_resourceKind . '()';
        return $url;
    }    

}