<?php

namespace IGFS_CG_API\Init;

use IGFS_CG_API\BaseIgfsCg;
use IGFS_CG_API\Exception\IgfsMissingParException;

/**
 * Class BaseIgfsCgInit
 *
 * @package IGFS_CG_API\Init
 * @author  N&TS Group <dev@netsgroup.com>
 */
abstract class BaseIgfsCgInit extends BaseIgfsCg
{

    public $shopID; // chiave messaggio

    function __construct()
    {
        parent::__construct();
    }

    protected function resetFields()
    {
        parent::resetFields();
        $this->shopID = null;
    }

    protected function checkFields()
    {
        parent::checkFields();
        if ($this->shopID == null || "" == $this->shopID) {
            throw new IgfsMissingParException("Missing shopID");
        }
    }

    protected function buildRequest()
    {
        $request = parent::buildRequest();
        $request = $this->replaceRequest($request, "{shopID}", $this->shopID);
        return $request;
    }

    protected function getServicePort()
    {
        return "PaymentInitGatewayPort";
    }

}

