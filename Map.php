<?php
namespace utils;
/**
 * Description of Map
 *
 * @author luis
 */
interface Map {
    function size();
    function isEmpty();
    function containsKey(&$key);
    function containsValue(&$value);
    function get(&$key);
    function put(&$key, &$value);
    function remove(&$value);
    function removeByKey(&$key);
    function putAll(Map $m);
    function clear();
    function getKeys();
    function values();
}

?>
