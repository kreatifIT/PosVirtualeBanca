<?php

namespace IGFS_CG_API\Init;

use IGFS_CG_API\IgfsUtils;
use SimpleXMLElement;

/**
 * Class SelectorTerminalInfo
 *
 * @package IGFS_CG_API\Init
 * @author  N&TS Group <dev@netsgroup.com>
 */
class SelectorTerminalInfo
{

    public $tid;
    public $description;
    public $payInstr;
    public $payInstrDescription;
    public $imgURL;

    /**
     * @param  $xml
     * @return SelectorTerminalInfo|void|null
     */
    public static function fromXml($xml)
    {

        if ($xml == "" || $xml == null) {
            return;
        }

        $dom = new SimpleXMLElement($xml, LIBXML_NOERROR, false);
        if (count($dom) == 0) {
            return;
        }

        $response = IgfsUtils::parseResponseFields($dom);
        $terminal = null;
        if (isset($response) && count($response) > 0) {
            $terminal = new SelectorTerminalInfo();
            $terminal->tid = (IgfsUtils::getValue($response, "tid"));
            $terminal->description = (IgfsUtils::getValue($response, "description"));
            $terminal->payInstr = (IgfsUtils::getValue($response, "payInstr"));
            $terminal->payInstrDescription = (IgfsUtils::getValue($response, "payInstrDescription"));

            if (isset($response["imgURL"])) {
                $imgURL = array();
                foreach ($dom->children() as $item) {
                    if ($item->getName() == "imgURL") {
                        $imgURL[] = $item->__toString();
                    }
                }
                $terminal->imgURL = $imgURL;
            }

        }
        return $terminal;
    }

}
