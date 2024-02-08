<?php

namespace IGFS_CG_API\Tran;

use IGFS_CG_API\Exception\IgfsException;
use IGFS_CG_API\Exception\IgfsMissingParException;
use IGFS_CG_API\IgfsUtils;

/**
 * Class IgfsCgCredit
 *
 * @package IGFS_CG_API\Tran
 * @author  N&TS Group <dev@netsgroup.com>
 */
class IgfsCgCredit extends BaseIgfsCgTran
{

    public $shopUserRef;
    public $amount;
    public $currencyCode;
    public $refTranID;
    public $pan;
    public $payInstrToken;
    public $expireMonth;
    public $expireYear;
    public $description;

    public $pendingAmount;

    function __construct()
    {
        parent::__construct();
    }

    protected function resetFields()
    {
        parent::resetFields();
        $this->shopUserRef = null;
        $this->amount = null;
        $this->currencyCode = null;
        $this->refTranID = null;
        $this->pan = null;
        $this->payInstrToken = null;
        $this->expireMonth = null;
        $this->expireYear = null;
        $this->description = null;

        $this->pendingAmount = null;
    }

    protected function checkFields()
    {
        parent::checkFields();
        if ($this->amount == null) {
            throw new IgfsMissingParException("Missing amount");
        }

        if ($this->refTranID == null) {
            if ($this->pan == null) {
                if ($this->payInstrToken == null) {
                    throw new IgfsMissingParException("Missing refTranID");
                }
            }
        }

        if ($this->pan != null) {
            // Se è stato impostato il pan verifico...
            if ($this->pan == "") {
                throw new IgfsMissingParException("Missing pan");
            }
        }
        if ($this->payInstrToken != null) {
            // Se è stato impostato il payInstrToken verifico...
            if ($this->payInstrToken == "") {
                throw new IgfsMissingParException("Missing payInstrToken");
            }
        }

        if ($this->pan != null or $this->payInstrToken != null) {
            if ($this->currencyCode == null) {
                throw new IgfsMissingParException("Missing currencyCode");
            }
        }
    }

    protected function buildRequest()
    {
        $request = parent::buildRequest();
        if ($this->shopUserRef != null) {
            $request = $this->replaceRequest($request, "{shopUserRef}", "<shopUserRef><![CDATA[" . $this->shopUserRef . "]]></shopUserRef>");
        } else {
            $request = $this->replaceRequest($request, "{shopUserRef}", "");
        }
        $request = $this->replaceRequest($request, "{amount}", $this->amount);
        if ($this->currencyCode != null) {
            $request = $this->replaceRequest($request, "{currencyCode}", "<currencyCode><![CDATA[" . $this->currencyCode . "]]></currencyCode>");
        } else {
            $request = $this->replaceRequest($request, "{currencyCode}", "");
        }

        if ($this->refTranID != null) {
            $request = $this->replaceRequest($request, "{refTranID}", "<refTranID><![CDATA[" . $this->refTranID . "]]></refTranID>");
        } else {
            $request = $this->replaceRequest($request, "{refTranID}", "");
        }

        if ($this->pan != null) {
            $request = $this->replaceRequest($request, "{pan}", "<pan><![CDATA[" . $this->pan . "]]></pan>");
        } else {
            $request = $this->replaceRequest($request, "{pan}", "");
        }

        if ($this->payInstrToken != null) {
            $request = $this->replaceRequest($request, "{payInstrToken}", "<payInstrToken><![CDATA[" . $this->payInstrToken . "]]></payInstrToken>");
        } else {
            $request = $this->replaceRequest($request, "{payInstrToken}", "");
        }

        if ($this->expireMonth != null) {
            $request = $this->replaceRequest($request, "{expireMonth}", "<expireMonth><![CDATA[" . $this->expireMonth . "]]></expireMonth>");
        } else {
            $request = $this->replaceRequest($request, "{expireMonth}", "");
        }
        if ($this->expireYear != null) {
            $request = $this->replaceRequest($request, "{expireYear}", "<expireYear><![CDATA[" . $this->expireYear . "]]></expireYear>");
        } else {
            $request = $this->replaceRequest($request, "{expireYear}", "");
        }

        if ($this->description != null) {
            $request = $this->replaceRequest($request, "{description}", "<description><![CDATA[" . $this->description . "]]></description>");
        } else {
            $request = $this->replaceRequest($request, "{description}", "");
        }

        return $request;
    }

    protected function setRequestSignature($request)
    {
        // signature dove il buffer e' cosi composto APIVERSION|TID|SHOPID|AMOUNT|CURRENCYCODE|REFORDERID|PAN|PAYINSTRTOKEN|EXPIREMONTH|EXPIREYEAR
        $fields = array(
            $this->getVersion(), // APIVERSION
            $this->tid, // TID
            $this->shopID, // SHOPID
            $this->shopUserRef, // SHOPUSERREF
            $this->amount, // AMOUNT
            $this->currencyCode, // CURRENCYCODE
            $this->refTranID, // REFORDERID
            $this->pan, // PAN
            $this->payInstrToken, // PAYINSTRTOKEN
            $this->expireMonth, // EXPIREMONTH
            $this->expireYear, // EXPIREYEAR
            $this->addInfo1, // UDF1
            $this->addInfo2, // UDF2
            $this->addInfo3, // UDF3
            $this->addInfo4, // UDF4
            $this->addInfo5); // UDF5
        try {
            $signature = $this->getSignature(
                $this->kSig, // KSIGN
                $fields
            );
            $request = $this->replaceRequest($request, "{signature}", $signature);
        } catch (IgfsException $e) {
        }

        return $request;
    }

    protected function parseResponseMap($response)
    {
        parent::parseResponseMap($response);
        // Opzionale
        $this->pendingAmount = IgfsUtils::getValue($response, "pendingAmount");
    }

    protected function getResponseSignature($response)
    {
        $fields = array(
            IgfsUtils::getValue($response, "tid"), // TID
            IgfsUtils::getValue($response, "shopID"), // SHOPID
            IgfsUtils::getValue($response, "rc"), // RC
            IgfsUtils::getValue($response, "errorDesc"),// ERRORDESC
            IgfsUtils::getValue($response, "tranID"), // ORDERID
            IgfsUtils::getValue($response, "date"), // TRANDATE
            IgfsUtils::getValue($response, "addInfo1"), // UDF1
            IgfsUtils::getValue($response, "addInfo2"), // UDF2
            IgfsUtils::getValue($response, "addInfo3"), // UDF3
            IgfsUtils::getValue($response, "addInfo4"), // UDF4
            IgfsUtils::getValue($response, "addInfo5"));// UDF5
        // signature dove il buffer e' cosi composto TID|SHOPID|RC|ERRORDESC|ORDERID|DATE|UDF1|UDF2|UDF3|UDF4|UDF5
        return $this->getSignature(
            $this->kSig, // KSIGN
            $fields
        );
    }

    protected function getFileName()
    {
        return __DIR__ . '/IgfsCgCredit.request';
    }
}
