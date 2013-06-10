<?php

require_once './StringUtil.php';

/**
 * PHPEL interpret an string like Expression language of java
 * @example 
 *
 * class Product{
 *      private $id;
 *      public __construct($id){
 *          $this->id = $id;
 *      }
 * }
 * 
 * echo 'Id: '.PHPEL::read("product.id", new Product(14));
 * 
 * will output "Id: 14"
 *
 * @author luis
 * @since Jul 29, 2012
 */
class PHPEL {

    private $object;
    private $str;

    private function __construct(&$object, $str) {
        $this->object = & $object;
        $this->str = $str;
    }

    public static function read($str, &$object) {
        $ob = new PHPEL($object, $str);
        return $ob->interpret();
    }

    private function interpret() {
        $values = explode(' ', trim($this->str));
        $add = true;
        $acum = '';
        $arr = array();
        foreach ($values as $k => $v) {
            if ($add && StringUtil::startsWith($v, '\\')) {
                $test = '\\\\';
                if ($v != $test && !StringUtil::endswith($v, '\\')) {
                    $add = false;
                    $acum = '';
                }
            } else if (!$add && StringUtil::endswith($v, '\\')) {
                $add = true;
                $v = trim($acum . ' ' . $v);
            }
            if ($add) {
                $arr[] = $v;
            } else {
                $acum .= $v . ' ';
            }
        }
        $realValues = array();
        foreach ($arr as $v) {
            if (!StringUtil::startsWith($v, '\\')) {
                $v = trim($v);
            }
            if (!$this->isLogic($v)) {
                $realValues[] = $this->getValue($v, $this->object);
            } else {
                $realValues[] = $v;
            }
        }
        $k = 0;
        while ($b = $this->hasnext($realValues, $k)) {
            if ($this->isLogic($b['val'])) {
                $this->readValues($realValues, $b['val'], $k);
            } else {
                $k++;
            }
        }
        return $realValues[0];
    }

    private function hasnext(&$arr, &$k) {
        $arr = & array_values($arr);
        if (count($arr) > 1) {
            return array('val' => $arr[$k]);
        }
        return false;
    }

    private function getValue($v, $object) {

        if ($v === 'true' || $v === 'TRUE') {
            return TRUE;
        } else if ($v === 'null' || $v === 'NULL') {
            return NULL;
        } else if ($v === 'false' || $v === 'FALSE') {
            return FALSE;
        }

        if ($v != '' && !StringUtil::startsWith($v, '\\')) {
            $pos = strpos($v, '.');
            $val = $pos !== false ? substr($v, 0, $pos) : $v;
            $class = new ReflectionClass($object);
            $prop = $class->getProperty($val);
            if ($prop != '') {
                $prop->setAccessible(true);
                $value = $prop->getValue($object);
                if (is_object($value)) {
                    if ($pos !== false) {
                        $rest = substr($v, $pos);
                        if (StringUtil::startsWith($rest, '.')) {
                            $rest = substr($rest, 1);
                        }
                        return $this->getValue($rest, $value);
                    }
                }
                return $value;
            }
            return $val;
        }
        if (StringUtil::startsWith($v, '\\')) {
            $v = substr($v, 1, strlen($v) - 2);
        }
        return $v;
    }

    private function readValues(&$arr, $logical, &$k) {
        if ($logical == 'empty') {
            unset($arr[$k]);
            $arr[$k + 1] = empty($arr[$k + 1]);
        } else if ($logical == '?') {
            if ($arr[$k - 1]) {
                unset($arr[$k + 3]);
            } else {
                unset($arr[$k + 1]);
            }
            unset($arr[$k - 1]);
            if ($arr[$k + 2] != ':') {
                $log = Logger::getLogger("PHPEL");
                $log->error("Error while reading values of EL, especting : found" . $arr[$k + 2]);
                exit;
            }
            unset($arr[$k + 2]);
            unset($arr[$k]);

            $k--;
        } else if ($logical == '!=') {

            $val1 = $arr[$k - 1] == 'null' ? null : $arr[$k - 1];
            $val2 = $arr[$k + 1] == 'null' ? null : $arr[$k + 1];

            if ($val1 !== $val2) {
                unset($arr[$k]);
                unset($arr[$k + 1]);
                $arr[$k - 1] = TRUE;
            } else {
                unset($arr[$k]);
                unset($arr[$k + 1]);
                $arr[$k - 1] = FALSE;
            }
            $k--;
        } else if ($logical == '==') {

            $val1 = $arr[$k - 1] == 'null' ? null : $arr[$k - 1];
            $val2 = $arr[$k + 1] == 'null' ? null : $arr[$k + 1];

            if ($val1 === $val2) {
                unset($arr[$k]);
                unset($arr[$k + 1]);
                $arr[$k - 1] = TRUE;
            } else {
                unset($arr[$k]);
                unset($arr[$k + 1]);
                $arr[$k - 1] = FALSE;
            }
            $k--;
        }
    }

    /**
     *
     * @param string $str
     * @return boolean
     */
    private function isLogic($str) {
        return $str === 'empty' || $str === '?' || $str === '!=' || $str === '==' || $str === ':';
    }

}

?>
