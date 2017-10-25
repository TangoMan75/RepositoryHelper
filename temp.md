$actions = [
    'orderBy',
    'search',
];

$operators = [
    'andWhere',
    'orWhere',
];

$modes = [
    'count',
    'distinct',
    'exactMatch',
    'like',
    'simpleArray',
    'sum',
];

$field = [
    'entity',
    'property',
];

$types = [
    'array',
    'boolean',
    'dateTime',
    'integer',
    'notNull',
    'simpleArray',
    'string',
];


if ($type == 'string') {
    $mode = [
        'exactMatch',
        'like',
    ];
}

if ($type == 'integer') {
    $mode = [
        'count',
        'sum',
    ];
}

if ($type == 'simpleArray') {
    $mode = [
        'simpleArray',
    ];
}

