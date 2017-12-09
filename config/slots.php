<?php

return array(
    'symbols' => array(
        'bar',
        'seven',
        'diamant',
        'horseshoe',
        //
        'lemon',
        'watermelon',
        'cherry',
        'bell',
        //
        'heart',
        'diamonds',
        'clubs',
        'spades'
    ),
    'rands' => array(
        // bar
        array(
            'match' => array('bar', 'bar', 'bar'),
            'chance' => 1000000,
            'prize' => 1000000000
        ),
        array(
            'match' => array('bar', 'bar'),
            'chance' => 500000,
            'prize' => 500000000
        ),
        array(
            'match' => array('bar'),
            'chance' => 250000,
            'prize' => 200000000
        ),
        // seven
        array(
            'match' => array('seven', 'seven', 'seven'),
            'chance' => 100000,
            'prize' => 100000000
        ),
        array(
            'match' => array('seven', 'seven'),
            'chance' => 50000,
            'prize' => 50000000
        ),
        array(
            'match' => array('seven'),
            'chance' => 25000,
            'prize' => 20000000
        ),
        // diamant
        array(
            'match' => array('diamant', 'diamant', 'diamant'),
            'chance' => 75000,
            'prize' => 50000000
        ),
        array(
            'match' => array('diamant', 'diamant'),
            'chance' => 37500,
            'prize' => 25000000
        ),
        array(
            'match' => array('diamant'),
            'chance' => 18750,
            'prize' => 10000000
        ),
        // horseshoe
        array(
            'match' => array('horseshoe', 'horseshoe', 'horseshoe'),
            'chance' => 800,
            'prize' => 10000000
        ),
        array(
            'match' => array('horseshoe', 'horseshoe'),
            'chance' => 400,
            'prize' => 5000000
        ),
        array(
            'match' => array('horseshoe'),
            'chance' => 300,
            'prize' => 2000000
        ),
        /*
         */
        // lemon
        array(
            'match' => array('lemon', 'lemon', 'lemon'),
            'chance' => 700,
            'prize' => 1000000
        ),
        array(
            'match' => array('lemon', 'lemon'),
            'chance' => 350,
            'prize' => 500000
        ),
        // watermelon
        array(
            'match' => array('watermelon', 'watermelon', 'watermelon'),
            'chance' => 500,
            'prize' => 750000
        ),
        array(
            'match' => array('watermelon', 'watermelon'),
            'chance' => 250,
            'prize' => 375000
        ),
        // cherry
        array(
            'match' => array('cherry', 'cherry', 'cherry'),
            'chance' => 225,
            'prize' => 500000
        ),
        array(
            'match' => array('cherry', 'cherry'),
            'chance' => 200,
            'prize' => 250000
        ),
        // bell
        array(
            'match' => array('bell', 'bell', 'bell'),
            'chance' => 175,
            'prize' => 250000
        ),
        array(
            'match' => array('bell', 'bell'),
            'chance' => 150,
            'prize' => 125000
        ),
        /*
         */
        // heart
        array(
            'match' => array('heart', 'heart', 'heart'),
            'chance' => 125,
            'prize' => 150000
        ),
        // diamonds
        array(
            'match' => array('diamonds', 'diamonds', 'diamonds'),
            'chance' => 100,
            'prize' => 75000
        ),
        // clubs
        array(
            'match' => array('clubs', 'clubs', 'clubs'),
            'chance' => 75,
            'prize' => 50000
        ),
        // spades
        array(
            'match' => array('spades', 'spades', 'spades'),
            'chance' => 50,
            'prize' => 5000
        )
    )
);
