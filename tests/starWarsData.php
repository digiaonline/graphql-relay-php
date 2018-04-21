<?php

namespace Digia\GraphQL\Relay\Test;

/**
 * This defines a basic set of data for our Star Wars Schema.
 * This data is hard coded for the sake of the demo, but you could imagine
 * fetching this data from a backend service rather than from hardcoded
 * JSON objects in a more complex demo.
 */

function xwing()
{
    return [
        'id'   => '1',
        'name' => 'X-Wing',
    ];
}

function ywing()
{
    return [
        'id'   => '2',
        'name' => 'Y-Wing',
    ];
}

function awing()
{
    return [
        'id'   => '3',
        'name' => 'A-Wing',
    ];
}

// Yeah, technically it's Corellian. But it flew in the service of the rebels,
// so for the purposes of this demo it's a rebel ship.
function falcon()
{
    return [
        'id'   => '4',
        'name' => 'Millenium Falcon',
    ];
}

function homeOne()
{
    return [
        'id'   => '5',
        'name' => 'Home One',
    ];
}

function tieFighter()
{
    return [
        'id'   => '6',
        'name' => 'TIE Fighter',
    ];
}

function tieInterceptor()
{
    return [
        'id'   => '7',
        'name' => 'TIE Interceptor',
    ];
}

function executor()
{
    return [
        'id'   => '8',
        'name' => 'Executor',
    ];
}

function rebels()
{
    return [
        'id'    => '1',
        'name'  => 'Alliance to Restore the Republic',
        'ships' => ['1', '2', '3', '4', '5'],
    ];
}

function empire()
{
    return [
        'id'    => '2',
        'name'  => 'Galactic Empire',
        'ships' => ['6', '7', '8'],
    ];
}

function data()
{
    global $data;

    $data = [
        'Faction' => [
            '1' => rebels(),
            '2' => empire(),
        ],
        'Ship'    => [
            '1' => xwing(),
            '2' => ywing(),
            '3' => awing(),
            '4' => falcon(),
            '5' => homeOne(),
            '6' => tieFighter(),
            '7' => tieInterceptor(),
            '8' => executor(),
        ],
    ];

    return $data;
}

function createShip($shipName, $factionId)
{
    global $data;

    static $nextShip = 9;

    $newShip = [
        'id'   => (string)$nextShip++,
        'name' => $shipName,
    ];

    $data['Ship'][$newShip['id']]           = $newShip;
    $data['Faction'][$factionId]['ships'][] = $newShip['id'];

    return $newShip;
}

function getShip($id)
{
    return data()['Ship'][$id];
}

function getFaction($id)
{
    return data()['Faction'][$id];
}

function getRebels()
{
    return rebels();
}

function getEmpire()
{
    return empire();
}
