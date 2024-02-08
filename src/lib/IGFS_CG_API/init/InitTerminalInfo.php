<?php

namespace IGFS_CG_API\Init;

/**
 * Class InitTerminalInfo
 *
 * @package IGFS_CG_API\Init
 * @author  N&TS Group <dev@netsgroup.com>
 */
class InitTerminalInfo
{

    public $tid;
    public $payInstrToken;

    function __construct()
    {
    }

    public function toXml($tname)
    {
        $sb = "";
        $sb .= "<" . $tname . ">";
        if ($this->tid != null) {
            $sb .= "<tid><![CDATA[";
            $sb .= $this->tid;
            $sb .= "]]></tid>";
        }
        if ($this->payInstrToken != null) {
            $sb .= "<payInstrToken><![CDATA[";
            $sb .= $this->payInstrToken;
            $sb .= "]]></payInstrToken>";
        }
        $sb .= "</" . $tname . ">";
        return $sb;
    }

}
