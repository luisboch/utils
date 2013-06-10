<?php

namespace utils;

include_once 'Map.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author luis
 */
class MapImpl implements Map {

    private $keys;
    private $values;

    function __construct() {
        $this->clear();
    }

    public function clear() {
        $this->keys = array();
        $this->values = array();
    }

    /**
     * @param Object $key
     * @return boolean
     */
    public function containsKey(&$key) {
        foreach ($this->keys as $k => $v) {
            if ($v === $key) {
                return true;
            }
        }
        return false;
    }

    public function containsValue(&$value) {
        foreach ($this->values as $k => $v) {
            if ($v === $value) {
                return true;
            }
        }
        return false;
    }

    public function get(&$key) {

        $i = $this->getIndex($key);

        if ($i != null) {
            return $this->values[$i];
        }
    }

    public function getKeys() {
        return $this->keys;
    }

    public function isEmpty() {
        return size($this->keys) == 0;
    }

    public function put(&$key, &$value) {
        $this->keys[] = &$key;
        $this->values[] = &$value;
    }

    public function putAll(Map &$m) {
        $keys = $m->getKeys();
        foreach ($keys as $ob) {
            $this->put($ob, $m->get($ob));
        }
    }

    public function remove(&$value) {
        $k = array_search($value, $this->values);
        if ($k !== false) {
            unset($this->keys[$k]);
            unset($this->values[$k]);
        }
    }

    public function size() {
        return size($this->keys);
    }

    public function values() {
        return $this->values;
    }

    public function getIndex($key) {
        foreach ($this->keys as $k => $v) {
            if ($v === $key) {
                return $k;
            }
        }
    }

    public function removeByKey(&$key) {
        $k = array_search($key, $this->keys);
        if ($k !== false) {
            unset($this->keys[$k]);
            unset($this->values[$k]);
        }
    }
    
    public function getKeyByValue(&$value){
        
        foreach($this->values as $k => $ob){
            if($ob === $value){
                return $this->keys[$k];
            }
        }
    }
}

?>
