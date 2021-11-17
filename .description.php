<?php

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arActivityDescription = [
    'NAME' => Loc::getMessage("GDHA_NAME"),
    'DESCRIPTION' => Loc::getMessage("GDHA_DESCRIPTION"),
    'TYPE' => 'activity',
    'CLASS' => 'GetDepartmentHeadActivity',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => [
        'ID' => 'stayfuneral',
        'OWN_ID' => 'stayfuneral',
        'OWN_NAME' => Loc::getMessage("GDHA_OWN_NAME")
    ],
    'RETURN' => [
        'HeadUser' => [
            'NAME' => Loc::getMessage("GDHA_HEAD_USER"),
            'TYPE' => FieldType::USER
        ],
        'HeadUserPrintable' => [
            'NAME' => Loc::getMessage("GDHA_HEAD_USER_PRINTABLE"),
            'TYPE' => FieldType::STRING
        ]
    ]

];