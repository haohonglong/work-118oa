<?php
/**
 * Created by PhpStorm.
 * User: chexiang
 * Date: 15/8/27
 * Time: 11:16
 */
use yii\rbac\Item;
return [
    'manageAgent' => ['type' => Item::TYPE_PERMISSION,'description'=>'','bizRule'=>null,'data'=>null],
    'manageUser' => ['type' => Item::TYPE_PERMISSION,'description'=>'','bizRule'=>null,'data'=>null],
    'manageLeader' => ['type' => Item::TYPE_PERMISSION,'description'=>'','bizRule'=>null,'data'=>null],
    'manageHighLeader' => ['type' => Item::TYPE_PERMISSION,'description'=>'','bizRule'=>null,'data'=>null],
    'manageSU' => ['type' => Item::TYPE_PERMISSION,'description'=>'','bizRule'=>null,'data'=>null],

    'agent' => [
        'type' => Item::TYPE_ROLE,
        'description' => 'AgentGroup',
        'bizRule' => null,
        'data' => null,
        'children' => ['manageAgent']
    ],

    'user' => [
        'type' => Item::TYPE_ROLE,
        'description' => 'UserGroup',
        'bizRule' => null,
        'data' => null,
        'children' => [
            'agent',
            'manageUser'
        ]
    ],

    'leader' => [
        'type' => Item::TYPE_ROLE,
        'description' => 'LeaderGroup',
        'bizRule' => null,
        'data' => null,
        'children' => [
            'user',
            'manageLeader'
        ]
    ],

    'highLeader' => [
        'type' => Item::TYPE_ROLE,
        'description' => 'LeaderGroup',
        'bizRule' => null,
        'data' => null,
        'children' => [
            'leader',
            'manageHighLeader'
        ]
    ],

    'su' => [
        'type' => Item::TYPE_ROLE,
        'description' => 'SuperUser',
        'bizRule' => null,
        'data' => null,
        'children' => [
            'highLeader',
            'manageSU'
        ]
    ],
];