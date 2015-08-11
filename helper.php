<?php
/**
 * User: Alexandr Tumaykin <alexandrtumaykin@gmail.com>
 */
 
define('DS', '/');
define('RN', "\r\n");
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
define('TAB', "\t");
define('DB_YES', "Y");
define('DB_NO', "N");

if (!function_exists("d")) {
    /**
     * Debug function
     */
    function d()
    {
        $caller = debug_backtrace();
        $caller = array_shift($caller);
        echo 'File: ' . $caller['file'] . ' / Line: ' . $caller['line'] . RN;
        array_map(function ($x) {
            var_dump($x);
            echo EOL;
        }, $caller['args']);
        die;
    }
}

if (!function_exists("dd")) {
    /**
     * Debug function
     */
    function dd()
    {
        $caller = debug_backtrace();
        $caller = array_shift($caller);
        echo '<code>File: ' . $caller['file'] . ' / Line: ' . $caller['line'] . '</code>';
        echo '<pre>';
        array_map(function ($x) {
            var_dump($x);
        }, $caller['args']);
        echo '</pre>';
        die;
    }
}

if (!function_exists("p")) {
    /**
     * Debug function
     */
    function p()
    {
        $caller = debug_backtrace();
        $caller = array_shift($caller);
        echo 'File: ' . $caller['file'] . ' / Line: ' . $caller['line'] . RN;
        array_map(function ($x) {
            print_r($x);
            echo EOL;
        }, $caller['args']);
        die;
    }
}

if (!function_exists("pp")) {
    /**
     * Debug function
     */
    function pp()
    {
        $caller = debug_backtrace();
        $caller = array_shift($caller);
        echo '<code>File: ' . $caller['file'] . ' / Line: ' . $caller['line'] . '</code>';
        echo '<pre>';
        array_map(function ($x) {
            print_r($x);
        }, $caller['args']);
        echo '</pre>';
        die;
    }
}

/**
 * @return float
 */
function getExecutionTime()
{
    static $microtimeStart = null;

    if ($microtimeStart === null) {
        $microtimeStart = microtime(true);

        return 0.0;
    }

    return round(microtime(true) - $microtimeStart, 4);
}

/**
 * Размер используемой памяти
 *
 * @param string $size
 *
 * @return string
 */
function getMemoryUsage($size = 'M')
{
    switch (strtolower($size)) {
        case 'k':
            $size = round(memory_get_peak_usage() / 1024, 2) . 'K';
            break;
        case 'm':
            $size = round(memory_get_peak_usage() / 1024 / 1024, 2) . 'M';
            break;
        case 'g':
            $size = round(memory_get_peak_usage() / 1024 / 1024 / 1024, 2) . 'G';
            break;
        default:
            $size = round(memory_get_peak_usage(), 2) . 'B';
            break;
    }

    return $size . "/" . ini_get("memory_limit");
}

/**
 * @param $str
 *
 * @return string
 */
function translit($str)
{
    $tr = [
        "А" => "A", "Б" => "B", "В" => "V",
        "Г" => "G", "Д" => "D", "Е" => "E",
        "Ж" => "J", "З" => "Z", "И" => "I",
        "Й" => "Y", "К" => "K", "Л" => "L",
        "М" => "M", "Н" => "N", "О" => "O",
        "П" => "P", "Р" => "R", "С" => "S",
        "Т" => "T", "У" => "U", "Ф" => "F",
        "Х" => "H", "Ц" => "TS", "Ч" => "CH",
        "Ш" => "SH", "Щ" => "SCH", "Ъ" => "",
        "Ы" => "YI", "Ь" => "", "Э" => "E",
        "Ю" => "YU", "Я" => "YA", "а" => "a",
        "б" => "b", "в" => "v", "г" => "g",
        "д" => "d", "е" => "e", "ж" => "j",
        "з" => "z", "и" => "i", "й" => "y",
        "к" => "k", "л" => "l", "м" => "m",
        "н" => "n", "о" => "o", "п" => "p",
        "р" => "r", "с" => "s", "т" => "t",
        "у" => "u", "ф" => "f", "х" => "h",
        "ц" => "ts", "ч" => "ch", "ш" => "sh",
        "щ" => "sch", "ъ" => "y", "ы" => "yi",
        "ь" => "'", "э" => "e", "ю" => "yu",
        "я" => "ya",
    ];

    return strtr($str, $tr);
}

/**
 * return seconds to hh:mm
 *
 * @param integer $t seconds
 * @param string  $format
 *
 * @return string
 */
function formatTime($t, $format = 'hh:mm')
{
    if (function_exists('gmp_sign')) {
        $sign = gmp_sign($t);
    } else {
        $sign = $t > 0 ? 1 : $t == 0 ? 0 : -1;
    }
    
    $s = abs($t) % 60;
    $m = (abs($t) / 60) % 60;
    $h = floor(abs($t) / 3600) * $sign;
    $d = floor(abs($t) / 3600 / 24);

    switch ($format) {
        case 'd hh:mm:ss':
            $h = floor(abs($t) / 3600) % 24 * $sign;

            return sprintf("%dд %02d:%02d:%02d", $d, $h, $m, $s);
        case 'd hh:mm':
            $h = floor(abs($t) / 3600) % 24 * $sign;

            return sprintf("%dд %02d:%02d", $d, $h, $m);
        case 'hh:mm:ss':
            return sprintf("%02d:%02d:%02d", $h, $m, $s);
        case 'hh:mm':
        default:
            return sprintf("%02d:%02d", $h, $m);
    }
}

/**
 * определение вхождения времени в интервал
 *
 * @param $startTime - начало интервала в формате hh:ii
 * @param $endTime   - конец интервала в формате hh:ii
 * @param $time      - проверяемое время в формате hh:ii
 *
 * @return bool
 */
function timeInRange($startTime, $endTime, $time)
{
    list($startTimeHour, $startTimeMinute) = explode(':', $startTime);
    list($endTimeHour, $endTimeMinute) = explode(':', $endTime);
    list($timeHour, $timeMinute) = explode(':', $time);

    $startTimeSeconds = $startTimeHour * 3600 + $startTimeMinute * 60;
    $endTimeSeconds = $endTimeHour * 3600 + $endTimeMinute * 60;
    $timeSeconds = $timeHour * 3600 + $timeMinute * 60;

    // если $startTime < $endTime
    if ($startTimeSeconds < $endTimeSeconds) {
        $result = $startTimeSeconds <= $timeSeconds && $timeSeconds <= $endTimeSeconds;
    } else {
        $result = $endTimeSeconds >= $timeSeconds || $startTimeSeconds <= $timeSeconds;
    }

    return $result;
}

/**
 * переводит массив с порядковыми номерами ключей
 * в буквенный код для использования в excel
 *
 * @param array  $headers
 *
 * @param string $a
 *
 * @return array
 */
function letterCells(array $headers = [], $a = 'A')
{
    $result = [];

    foreach ($headers as $i => $header) {
        $result[$a] = $header;
        $a++;
    }

    return $result;
}

/**
 * переводит в Y/N
 *
 * @param $value
 *
 * @return string
 */
function boolToYesNo($value)
{
    return $value ? 'Y' : 'N';
}

/**
 * Склонение слов после числительных
 *
 * @param integer $digit
 * @param array   $expr
 * @param bool    $onlyword
 *
 * @example declension(1, ['день','дня','дней'])
 *
 * @return string
 */
function declension($digit, $expr, $onlyword = false)
{

    if (!is_array($expr)) {
        $expr = array_filter(explode(' ', $expr));
    }

    if (empty($expr[2])) {
        $expr[2] = $expr[1];
    }

    $i = preg_replace('/[^0-9]+/s', '', $digit) % 100;

    if ($onlyword) {
        $digit = '';
    }

    if ($i >= 5 && $i <= 20) {
        $res = $digit . ' ' . $expr[2];
    } else {
        $i %= 10;

        if ($i === 1) {
            $res = $digit . ' ' . $expr[0];
        } elseif ($i >= 2 && $i <= 4) {
            $res = $digit . ' ' . $expr[1];
        } else {
            $res = $digit . ' ' . $expr[2];
        }
    }

    return trim($res);
}

/**
 * @param array|string $delimiters
 * @param string       $string
 *
 * @return array
 */
function multiSplit($delimiters, $string)
{
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);

    return $launch;
}

/**
 * @param        $str
 * @param string $charlist
 *
 * @return string
 */
function hTrim($str, $charlist = " ,;\t\n\r\0\x0B")
{
    return trim($str, $charlist);
}
