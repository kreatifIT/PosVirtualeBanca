<?php

namespace IGFS_CG_API;

use SimpleXMLElement;

/**
 * Class Entry
 *
 * @package IGFS_CG_API
 * @author  N&TS Group <dev@netsgroup.com>
 */
class Entry
{
    public $key;
    public $value;

    function __construct()
    {
    }

    /**
     * @param  $xml
     * @return Entry|void|null
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
        $entry = null;
        if (isset($response) && count($response) > 0) {
            $entry = new Entry();
            $entry->key = (IgfsUtils::getValue($response, "key"));
            $entry->value = (IgfsUtils::getValue($response, "value"));
        }
        return $entry;
    }

}
