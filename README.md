sage-sdata-php
==============

PHP library used to interact with SAGE accounting software using the SDATA extension.

* Read more about SDATA here: http://sdata.sage.com/
* SDATA v1.1 core specification: http://interop.sage.com/daisy/sdata/Introduction.html


    /*
     * CONNECTION
     */
    $sdata = new \Ia\Sdata\Conn(array(
        'hostname'=>'https://www.yourhostname.com/',
        'username'=>'username',
        'password'=>'password',
        'company'=>'companyname'
    ));

    /*
     * UPDATE EXAMPLE
     */
    try{

        $query = new \Ia\Sdata\Query\Type\Update($sdata);
        $query->setResourceKind('SO_SalesOrderDetail');
        $query->setResourceSelector('\'0000060;000001\'');
        $query->setFieldValues(array(
                'ItemCode' => '/3251',
                'CostOfGoodsSoldAcctKey' => '403001000',
                'QuantityOrdered' => 5
                'SalesAcctKey' => '9550000W2',
            )
        );
        $query->getXmlResult();            

    } catch (Exception $e) {
        echo 'Exception: '.$e->getMessage();
    }
    
    /*
     * CREATE LINE ITEM EXAMPLE
     */
    try{

        $query = new \Ia\Sdata\Query\Type\Create($sdata);
        $query->setResourceKind('SO_SalesOrderDetail');
        //$query->setChildResourceKind('SO_SalesOrderHeaderSPECIALSECOND');
        $query->setFieldValues(
            array(
                    'SalesOrderNo' => '0000060',
                    'ItemCode' => 'C-10X15.3-A36',
                    'CostOfGoodsSoldAcctKey' => '9550000X4',
                    'SalesAcctKey' => '9550000W2',
                    'QuantityOrdered' => 5
                )
        );
        $query->getXmlResult();            

    } catch (Exception $e) {
        echo 'Exception: '.$e->getMessage();
    }    

    /*
     * CREATE EXAMPLE
     */
    try{

        $query = new \Ia\Sdata\Query\Type\Create($sdata);
        $query->setResourceKind('SO_SalesOrderHeaderSPECIAL');
        $query->setChildResourceKind('SO_SalesOrderHeaderSPECIALSECOND');
        $query->setFieldValues(
            array(
                'ARDivisionNo'=>'00',
                'CustomerNo'=>'CUST01',
                'Comment'=>'test',
            )
        );
        $query->setChildFieldValues(array(
                        'ItemCode' => 'C-10X15.3-A36',
                        'CostOfGoodsSoldAcctKey' => '9550000X4',
                        'SalesAcctKey' => '9550000W2',
                        'QuantityOrdered' => 5
                    ),
                    0 //child sequence
            );
        $query->getXmlResult();            

    } catch (Exception $e) {
        echo 'Exception: '.$e->getMessage();
    }        

    /*
     * DELETE EXAMPLE 
     */
    try{   
        $query = new \Ia\Sdata\Query\Type\Delete($sdata);
        $query->setResourceKind('SO_SalesOrderHeader');
        $query->setResourceSelector('\'0000048\'');
        $query->getXmlResult();
    } catch (Exception $e) {
        echo 'Exception: '.$e->getMessage();
    }

    /*
     * RETRIEVE COLLECTION EXAMPLE
     */
    try{   
        //handle collection
        $query = new \Ia\Sdata\Query\Type\Collection($sdata);
        $query->setResourceKind('SO_SalesOrderHeader');
        $query->setQueryParam('count',1);
        $query->setQueryParam('startIndex',1);
        $entries = $query->getEntries();           
    } catch (Exception $e) {
        echo 'Exception: '.$e->getMessage();
        exit;
    }
    
    /*
     * RETRIEVE SINGLE ENTRY EXAMPLE
     */

    try
        $query = new \Ia\Sdata\Query\Type\Collection($sdata);
        $query->setResourceKind('SO_SalesOrderDetail');
        $query->setQueryParam('where','SalesOrderNo eq \''.$salesOrderNo.'\'');
        $entries = $query->getEntries(); 
    } catch (Exception $e) {
        echo 'Exception: '.$e->getMessage();
    }
