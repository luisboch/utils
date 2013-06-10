<?php

namespace utils;
/**
 * Description of StringUtil
 *
 * @author luis
 */
class StringUtil {
    

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function endswith($haystack, $needle)
    {
        $strlen = strlen($haystack);
        $testlen = strlen($needle);
        if ($testlen > $strlen)
            return false;
        $bool = substr_compare($haystack, $needle, -$testlen) === 0;
        return $bool;
    }

    /**
     * @param $value
     * @return float
     */
    public static function toFloat($value)
    {
        return floatval(str_replace(',', '.', str_replace('.', '', $value)));
    }

    /**
     * @param $value
     * @return string
     */
    public static function currency($value)
    {
        return 'R$ '.self::floatToString($value);
    }

    /**
     * @param $value
     * @return string
     */
    public static function floatToString($value)
    {
        return number_format($value, 2, ',', '.');
    }
    
    /**
     *
     * @param String $email
     * @return boolean
     */
    public static function checkEmail($email)
    {
        return preg_match(
            '/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $email);
    }

    /**
     *
     * @param String $cpf
     * @return boolean
     */
    public static function checkCpf($cpf)
    {
        $cpf = str_pad(preg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);

        if (!is_numeric($cpf)) {
            return false;
        }
        // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' ||
            $cpf == '22222222222' || $cpf == '33333333333' ||
            $cpf == '44444444444' || $cpf == '55555555555' ||
            $cpf == '66666666666' || $cpf == '77777777777' ||
            $cpf == '88888888888' || $cpf == '99999999999') {
            
            return false;
            
        } else { // Calcula os números para verificar se o CPF é verdadeiro
            
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ($cpf{$c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }

}

?>
