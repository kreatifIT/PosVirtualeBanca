<?php

namespace IGFS_CG_API\paybymail;

use IGFS_CG_API\Exception\IgfsMissingParException;
use IGFS_CG_API\IgfsUtils;

/**
 * Class IgfsCgPayByMailVerify
 *
 * @package IGFS_CG_API\paybymail
 * @author  N&TS Group <dev@netsgroup.com>
 */
class IgfsCgPayByMailVerify extends BaseIgfsCgPayByMail
{

    public $mailID;

    public $tranID;
    public $status;
    public $addInfo1;
    public $addInfo2;
    public $addInfo3;
    public $addInfo4;
    public $addInfo5;

    function __construct()
    {
        parent::__construct();
    }

    protected function resetFields()
    {
        parent::resetFields();
        $this->mailID = null;

        $this->tranID = null;
        $this->status = null;
        $this->addInfo1 = null;
        $this->addInfo2 = null;
        $this->addInfo3 = null;
        $this->addInfo4 = null;
        $this->addInfo5 = null;
    }

    protected function checkFields()
    {
        parent::checkFields();
        if ($this->mailID == null || "" == $this->mailID) {
            throw new IgfsMissingParException("Missing mailID");
        }
    }

    protected function buildRequest()
    {
        $request = parent::buildRequest();
        $request = $this->replaceRequest($request, "{mailID}", $this->mailID);

        return $request;
    }

    protected function setRequestSignature($request)
    {
        $fields = array(
            $this->getVersion(), // APIVERSION
            $this->tid, // TID
            $this->shopID, // SHOPID
            $this->mailID); // MAILID
        // signature dove il buffer e' cosi composto APIVERSION|TID|SHOPID|MAILID
        $signature = $this->getSignature(
            $this->kSig, // KSIGN
            $fields
        );
        $request = $this->replaceRequest($request, "{signature}", $signature);
        return $request;
    }

    protected function parseResponseMap($response)
    {
        parent::parseResponseMap($response);
        // Opzionale
        $this->tranID = IgfsUtils::getValue($response, "tranID");

        // Opzionale
        $this->status = IgfsUtils::getValue($response, "status");
        // Opzionale
        $this->addInfo1 = IgfsUtils::getValue($response, "addInfo1");
        // Opzionale
        $this->addInfo2 = IgfsUtils::getValue($response, "addInfo2");
        // Opzionale
        $this->addInfo3 = IgfsUtils::getValue($response, "addInfo3");
        // Opzionale
        $this->addInfo4 = IgfsUtils::getValue($response, "addInfo4");
        // Opzionale
        $this->addInfo5 = IgfsUtils::getValue($response, "addInfo5");
    }

    protected function getResponseSignature($response)
    {
        $fields = array(
            IgfsUtils::getValue($response, "tid"), // TID
            IgfsUtils::getValue($response, "shopID"), // SHOPID
            IgfsUtils::getValue($response, "rc"), // RC
            IgfsUtils::getValue($response, "errorDesc"),// ERRORDESC
            IgfsUtils::getValue($response, "mailID"),// MAILID
            IgfsUtils::getValue($response, "tranID"),// ORDERID
            IgfsUtils::getValue($response, "status"));// STATUS
        // signature dove il buffer e' cosi composto TID|SHOPID|RC|ERRORDESC|MAILID|STATUS
        return $this->getSignature(
            $this->kSig, // KSIGN
            $fields
        );
    }

    protected function getFileName()
    {
        return __DIR__ . '/IgfsCgPayByMailVerify.request';
    }
}