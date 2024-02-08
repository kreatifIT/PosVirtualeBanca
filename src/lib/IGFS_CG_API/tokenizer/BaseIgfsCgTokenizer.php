<?php

namespace IGFS_CG_API\tokenizer;

use IGFS_CG_API\BaseIgfsCg;
use IGFS_CG_API\Exception\IgfsMissingParException;

/**
 * Class BaseIgfsCgTokenizer
 *
 * @package IGFS_CG_API\tokenizer
 * @author  N&TS Group <dev@netsgroup.com>
 */
abstract class BaseIgfsCgTokenizer extends BaseIgfsCg
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
        try {
            parent::checkFields();
        } catch (IgfsMissingParException $e) {
        }
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
        return "TokenizerGatewayPort";
    }
}
