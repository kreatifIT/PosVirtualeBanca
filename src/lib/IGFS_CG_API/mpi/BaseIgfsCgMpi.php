<?php

namespace IGFS_CG_API\mpi;

use IGFS_CG_API\BaseIgfsCg;
use IGFS_CG_API\Exception\IgfsMissingParException;
use IGFS_CG_API\IgfsUtils;

/**
 * Class BaseIgfsCgMpi
 *
 * @package IGFS_CG_API\mpi
 * @author  N&TS Group <dev@netsgroup.com>
 */
abstract class BaseIgfsCgMpi extends BaseIgfsCg
{

    public $shopID; // chiave messaggio

    public $xid;

    function __construct()
    {
        parent::__construct();
    }

    protected function resetFields()
    {
        parent::resetFields();
        $this->shopID = null;

        $this->xid = null;
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
        return "MPIGatewayPort";
    }

    protected function parseResponseMap($response)
    {
        parent::parseResponseMap($response);
        // Opzionale
        $this->xid = IgfsUtils::getValue($response, "xid");
    }

}