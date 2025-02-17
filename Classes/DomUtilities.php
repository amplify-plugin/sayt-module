<?php

namespace Amplify\System\Sayt\Classes;

// Contains a list of EasyAsk categories and provides methods to easily access
// the categories and pertaining data for the current search as well as the intial values.
class DomUtilities
{
    public static function findAttribute($object, $attribute)
    {
        $return = null;
        if ($object && count($object) > 0) {
            foreach ($object->attributes() as $a => $b) {
                if ($a == $attribute) {
                    $return = $b;
                }
            }
        }
        if ($return) {
            return $return;
        }
    }
}
