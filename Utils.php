<?php
namespace utils;
/**
 *
 * @author luis
 */
class Utils
{
    /**
     *
     * @param object $object
     * @return array
     */
    public static function objectToArray($object)
    {
        $class = get_class($object);
        $array = (array)$object;
        $new = array();
        foreach ($array as $k => $v) {
            if (strpos($k, $class)) {
                $k = str_replace($class, '', $k);
            } else {
                $k = str_replace("BasicBean", '', $k);
            }

            $aux = substr($k, 0, 2);
            $k = str_replace($aux, '', $k);
            $new[$k] = $v;
        }

        return $new;
    }

}

?>
