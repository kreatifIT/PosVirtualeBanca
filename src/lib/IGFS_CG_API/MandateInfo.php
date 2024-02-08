<?php

namespace IGFS_CG_API;

/**
 * Class MandateInfo
 *
 * @package IGFS_CG_API
 * @author  N&TS Group <dev@netsgroup.com>
 */
class MandateInfo
{

    public $mandateID;
    public $contractID;
    public $sequenceType;
    public $frequency;
    public $durationStartDate;
    public $durationEndDate;
    public $firstCollectionDate;
    public $finalCollectionDate;
    public $maxAmount;
    
    function __construct()
    {
    }

    public function toXml($tname)
    {
        $sb = "";
        $sb .= "<" . $tname . ">";
        if ($this->mandateID != null) {
            $sb .= "<mandateID><![CDATA[";
            $sb .= $this->mandateID;
            $sb .= "]]></mandateID>";
        }
        if ($this->contractID != null) {
            $sb .= "<contractID><![CDATA[";
            $sb .= $this->contractID;
            $sb .= "]]></contractID>";
        }
        if ($this->sequenceType != null) {
            $sb .= "<sequenceType><![CDATA[";
            $sb .= $this->sequenceType;
            $sb .= "]]></sequenceType>";
        }
        if ($this->frequency != null) {
            $sb .= "<frequency><![CDATA[";
            $sb .= $this->frequency;
            $sb .= "]]></frequency>";
        }
        if ($this->durationStartDate != null) {
            $sb .= "<durationStartDate><![CDATA[";
            $sb .= IgfsUtils::formatXMLGregorianCalendar($this->durationStartDate);
            $sb .= "]]></durationStartDate>";
        }
        if ($this->durationEndDate != null) {
            $sb .= "<durationEndDate><![CDATA[";
            $sb .= IgfsUtils::formatXMLGregorianCalendar($this->durationEndDate);
            $sb .= "]]></durationEndDate>";
        }
        if ($this->firstCollectionDate != null) {
            $sb .= "<firstCollectionDate><![CDATA[";
            $sb .= IgfsUtils::formatXMLGregorianCalendar($this->firstCollectionDate);
            $sb .= "]]></firstCollectionDate>";
        }
        if ($this->finalCollectionDate != null) {
            $sb .= "<finalCollectionDate><![CDATA[";
            $sb .= IgfsUtils::formatXMLGregorianCalendar($this->finalCollectionDate);
            $sb .= "]]></finalCollectionDate>";
        }
        if ($this->maxAmount != null) {
            $sb .= "<maxAmount><![CDATA[";
            $sb .= $this->maxAmount;
            $sb .= "]]></maxAmount>";
        }
        $sb .= "</" . $tname . ">";
        return $sb;
    }

}
