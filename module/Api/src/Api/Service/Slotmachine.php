<?php

namespace Api\Service;

class Slotmachine {

    protected $_sm;
    protected $_config;

    public function __construct($sm) {
        $this->_sm = $sm;
        $configFile = realpath(dirname(INDEX_PATH) . '/../config') . '/slots.php';
        $this->_config = include $configFile;
        return $this;
    }

    public function getSm() {
        return $this->_sm;
    }

    public function getResult() {
        $symbols = $this->_config['symbols'];
        $rands = $this->_config['rands'];

        $denominators = array();
        foreach ($rands as $rand) {
            $denominators[] = $rand['chance'];
        }

        $nok = $this->getNok($denominators);

        $border = 0;
        $prizes = array();
        $numerators = array();
        foreach ($rands as $rand) {
            $name = $rand['match'][0] . ':' . count($rand['match']);
            $prizes[$name] = $rand['prize'];
            $numerators[$name] = $nok / $rand['chance'];
            $border += $numerators[$name];
        }
        asort($numerators);

        $begin = 1;
        $prev = 0;
        $ranges = array();
        foreach ($numerators as $key => $num) {
            $ranges[$key]['min'] = $begin;
            $ranges[$key]['max'] = $num + $prev;
            $begin = $num + $prev + 1;
            $prev = $num + $prev;
        }

        $win = rand(1, $nok); // get win number
        $winPrize = 0;

        $result = array();
        if ($win <= $border) {
            foreach ($ranges as $name => $range) {
                if ($win >= $range['min'] && $win <= $range['max']) {
                    $winPrize = $prizes[$name];
                    $type = explode(':', $name);
                    if ($type[1] < 3) {
                        $symbols = array_slice($symbols, 4);
                        $only_one = array('lemon', 'watermelon', 'cherry', 'bell');
                        $i = 3 - $type[1];
                        $max = 7;

                        if ($type[1] == 2 && in_array($type[0], $only_one)) {
                            $symbols = array_slice($symbols, 4);
                            $max = 3;
                        }

                        $result = $this->getLoseSlots($symbols, $i, $max, $result);

                        for ($t = 0; $t < $type[1]; $t++) {
                            $result[] = $type[0];
                        }
                    } else {
                        $result = array($type[0], $type[0], $type[0]);
                    }
                    break;
                }
            }
        } else {
            $symbols = array_slice($symbols, 4);
            $result = $this->getLoseSlots($symbols, 3, 7, $result);
        }

        shuffle($result);

        return array(
            'win' => $result,
            'prize' => $winPrize
        );
    }

    /**
     * наименьшее общее кратное
     */
    public function getNok($arr = array()) {
        $n = count($arr);
        $a = abs($arr[0]);
        for ($i = 1; $i < $n; $i++) {
            $b = abs($arr[$i]);
            $c = $a;
            while ($a && $b) {
                $a > $b ? $a %= $b : $b %= $a;
            }
            $a = abs($c * $arr[$i]) / ($a + $b);
        }
        return $a;
    }

    public function getLoseSlots($symbols, $iVal, $maxVal, $result) {
        $only_one = array('lemon', 'watermelon', 'cherry', 'bell');
        $only_two = array('heart', 'diamonds', 'clubs', 'spades');
        $i = $iVal;
        $j = 0;
        $max = $maxVal;
        while ($i) {
            $k = rand(0, $max);
            $val = $symbols[$k];
            if (in_array($val, $result)) {
                if (in_array($val, $only_one)) {
                    continue;
                } else if (in_array($val, $only_two)) {
                    $counts = array_count_values($result);
                    if ($counts[$val] == 2) {
                        $key = array_search($val, $symbols);
                        unset($symbols[$key]);
                        $symbols = array_values($symbols);
                        $max--;
                        continue;
                    }
                }
            }
            $result[$j] = $val;
            $i--;
            $j++;
        }

        return $result;
    }

}
