<?php

namespace IGFS_CG_API\paybymail;

use IGFS_CG_API\Exception\IgfsMissingParException;
use IGFS_CG_API\IgfsUtils;

/**
 * Class IgfsCgPayByMailInit
 *
 * @package IGFS_CG_API\paybymail
 * @author  N&TS Group <dev@netsgroup.com>
 */
class IgfsCgPayByMailInit extends BaseIgfsCgPayByMail
{

    public $shopUserRef;
    public $shopUserName;
    public $shopUserAccount;
    public $shopUserMobilePhone;
    public $shopUserIMEI;
    public $trType = "AUTH";
    public $amount;
    public $currencyCode;
    public $langID = "EN";
    public $callbackURL;
    public $addInfo1;
    public $addInfo2;
    public $addInfo3;
    public $addInfo4;
    public $addInfo5;
    public $accountName;
    public $level3Info;
    public $description;
    public $paymentReason;

    public $mailID;

    function __construct()
    {
        parent::__construct();
    }

    protected function resetFields()
    {
        parent::resetFields();
        $this->shopUserRef = null;
        $this->shopUserName = null;
        $this->shopUserAccount = null;
        $this->shopUserMobilePhone = null;
        $this->shopUserIMEI = null;
        $this->trType = "AUTH";
        $this->amount = null;
        $this->currencyCode = null;
        $this->langID = "EN";
        $this->callbackURL = null;
        $this->addInfo1 = null;
        $this->addInfo2 = null;
        $this->addInfo3 = null;
        $this->addInfo4 = null;
        $this->addInfo5 = null;
        $this->accountName = null;
        $this->level3Info = null;
        $this->description = null;
        $this->paymentReason = null;

        $this->mailID = null;
    }

    protected function checkFields()
    {
        parent::checkFields();
        if ($this->trType == null) {
            throw new IgfsMissingParException("Missing trType");
        }
        if ($this->langID == null) {
            throw new IgfsMissingParException("Missing langID");
        }
        if ($this->shopUserRef == null) {
            throw new IgfsMissingParException("Missing shopUserRef");
        }
            
        if ($this->level3Info != null) {
            $i = 0;
            if ($this->level3Info->product != null) {
                foreach ($this->level3Info->product as $product) {
                    if ($product->productCode == null) {
                        throw new IgfsMissingParException("Missing productCode[" . $i . "]");
                    }
                    if ($product->productDescription == null) {
                        throw new IgfsMissingParException("Missing productDescription[" . $i . "]");
                    }
                }
                $i++;
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
        if ($this->shopUserName != null) {
            $request = $this->replaceRequest($request, "{shopUserName}", "<shopUserName><![CDATA[" . $this->shopUserName . "]]></shopUserName>");
        } else {
            $request = $this->replaceRequest($request, "{shopUserName}", "");
        }
        if ($this->shopUserAccount != null) {
            $request = $this->replaceRequest($request, "{shopUserAccount}", "<shopUserAccount><![CDATA[" . $this->shopUserAccount . "]]></shopUserAccount>");
        } else {
            $request = $this->replaceRequest($request, "{shopUserAccount}", "");
        }
        if ($this->shopUserMobilePhone != null) {
            $request = $this->replaceRequest($request, "{shopUserMobilePhone}", "<shopUserMobilePhone><![CDATA[" . $this->shopUserMobilePhone . "]]></shopUserMobilePhone>");
        } else {
            $request = $this->replaceRequest($request, "{shopUserMobilePhone}", "");
        }
        if ($this->shopUserIMEI != null) {
            $request = $this->replaceRequest($request, "{shopUserIMEI}", "<shopUserIMEI><![CDATA[" . $this->shopUserIMEI . "]]></shopUserIMEI>");
        } else {
            $request = $this->replaceRequest($request, "{shopUserIMEI}", "");
        }

        $request = $this->replaceRequest($request, "{trType}", $this->trType);
        if ($this->amount != null) {
            $request = $this->replaceRequest($request, "{amount}", "<amount><![CDATA[" . $this->amount . "]]></amount>");
        } else {
            $request = $this->replaceRequest($request, "{amount}", "");
        }
        if ($this->currencyCode != null) {
            $request = $this->replaceRequest($request, "{currencyCode}", "<currencyCode><![CDATA[" . $this->currencyCode . "]]></currencyCode>");
        } else {
            $request = $this->replaceRequest($request, "{currencyCode}", "");
        }

        $request = $this->replaceRequest($request, "{langID}", $this->langID);
        if ($this->callbackURL != null) {
            $request = $this->replaceRequest($request, "{callbackURL}", "<callbackURL><![CDATA[" . $this->callbackURL . "]]></callbackURL>");
        } else {
            $request = $this->replaceRequest($request, "{callbackURL}", "");
        }
        
        if ($this->addInfo1 != null) {
            $request = $this->replaceRequest($request, "{addInfo1}", "<addInfo1><![CDATA[" . $this->addInfo1 . "]]></addInfo1>");
        } else {
            $request = $this->replaceRequest($request, "{addInfo1}", "");
        }
        if ($this->addInfo2 != null) {
            $request = $this->replaceRequest($request, "{addInfo2}", "<addInfo2><![CDATA[" . $this->addInfo2 . "]]></addInfo2>");
        } else {
            $request = $this->replaceRequest($request, "{addInfo2}", "");
        }
        if ($this->addInfo3 != null) {
            $request = $this->replaceRequest($request, "{addInfo3}", "<addInfo3><![CDATA[" . $this->addInfo3 . "]]></addInfo3>");
        } else {
            $request = $this->replaceRequest($request, "{addInfo3}", "");
        }
        if ($this->addInfo4 != null) {
            $request = $this->replaceRequest($request, "{addInfo4}", "<addInfo4><![CDATA[" . $this->addInfo4 . "]]></addInfo4>");
        } else {
            $request = $this->replaceRequest($request, "{addInfo4}", "");
        }
        if ($this->addInfo5 != null) {
            $request = $this->replaceRequest($request, "{addInfo5}", "<addInfo5><![CDATA[" . $this->addInfo5 . "]]></addInfo5>");
        } else {
            $request = $this->replaceRequest($request, "{addInfo5}", "");
        }
        
        if ($this->accountName != null) {
            $request = $this->replaceRequest($request, "{accountName}", "<accountName><![CDATA[" . $this->accountName . "]]></accountName>");
        } else {
            $request = $this->replaceRequest($request, "{accountName}", "");
        }

        if ($this->level3Info != null) {
            $request = $this->replaceRequest($request, "{level3Info}", $this->level3Info->toXml("level3Info"));
        } else {
            $request = $this->replaceRequest($request, "{level3Info}", "");
        }
        if ($this->description != null) {
            $request = $this->replaceRequest($request, "{description}", "<description><![CDATA[" . $this->description . "]]></description>");
        } else {
            $request = $this->replaceRequest($request, "{description}", "");
        }
        if ($this->paymentReason != null) {
            $request = $this->replaceRequest($request, "{paymentReason}", "<paymentReason><![CDATA[" . $this->paymentReason . "]]></paymentReason>");
        } else {
            $request = $this->replaceRequest($request, "{paymentReason}", "");
        }

        return $request;
    }

    protected function setRequestSignature($request)
    {
        // signature dove il buffer e' cosi composto APIVERSION|TID|SHOPID|SHOPUSERREF|SHOPUSERNAME|SHOPUSERACCOUNT|SHOPUSERMOBILEPHONE|SHOPUSERIMEI|TRTYPE|AMOUNT|CURRENCYCODE|LANGID|NOTIFYURL|ERRORURL|CALLBACKURL
        $fields = array(
        $this->getVersion(), // APIVERSION
        $this->tid, // TID
        $this->shopID, // SHOPID
        $this->shopUserRef, // SHOPUSERREF
        $this->shopUserName, // SHOPUSERNAME
        $this->shopUserAccount, // SHOPUSERACCOUNT
        $this->shopUserMobilePhone, //SHOPUSERMOBILEPHONE
        $this->shopUserIMEI, //SHOPUSERIMEI
        $this->trType,// TRTYPE
        $this->amount, // AMOUNT
        $this->currencyCode, // CURRENCYCODE
        $this->langID, // LANGID
        $this->callbackURL, // CALLBACKURL
        $this->addInfo1, // UDF1
        $this->addInfo2, // UDF2
        $this->addInfo3, // UDF3
        $this->addInfo4, // UDF4
        $this->addInfo5);
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
        $this->mailID = IgfsUtils::getValue($response, "mailID");
    }

    protected function getResponseSignature($response)
    {
        $fields = array(
        IgfsUtils::getValue($response, "tid"), // TID
        IgfsUtils::getValue($response, "shopID"), // SHOPID
        IgfsUtils::getValue($response, "rc"), // RC
        IgfsUtils::getValue($response, "errorDesc"),// ERRORDESC
        IgfsUtils::getValue($response, "mailID")); // MAILID    
        // signature dove il buffer e' cosi composto TID|SHOPID|RC|ERRORDESC|MAILID
        return $this->getSignature(
            $this->kSig, // KSIGN
            $fields
        ); 
    }
    
    protected function getFileName()
    {
        return __DIR__ . '/IgfsCgPayByMailInit.request';
    }
}