<?php
namespace Ia\Sdata;

/**
 *
 * @author Aaron Lozier <aaron@informationarchitech.com>
 */

class Schema extends Query
{

    public function __construct(Conn $conn)
    {
        parent::__construct($conn);

        $schema_file = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' .
                        DIRECTORY_SEPARATOR . 'schema.xml';

        $this->setCommand('$schema');

        if(file_exists($schema_file)){
            $this->setRawResult(file_get_contents($schema_file));
        } else {
            $rawSchema = $this->getRawResult();
            file_put_contents($schema_file, $rawSchema);
        }

        return $this;
    }

    public function getRawResult()
    {
        $rawResult = parent::getRawResult();
        $rawResult = str_replace('xs:','',$rawResult);
        $rawResult = str_replace('sme:','',$rawResult);
        return $rawResult;
    }

    protected $_resourceKinds = null;

    public function getResourceKinds()
    {
        if($this->_resourceKinds===null){
            $this->_resourceKinds = array();
            foreach($this->getXmlResult()->element as $element){
                $this->_resourceKinds[] = $element;
            }
        }
        return $this->_resourceKinds;
    }

}