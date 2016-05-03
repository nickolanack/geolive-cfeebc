<?php

//onDisplayMap

function eventHandler(string $eventName, object $eventArguments){
Core::LoadPlugin('Attributes');

$end = date('Y-m-d', time() - (30 * 24 * 3600));
$table = 'markerAttributes';
$tableMetadata = AttributesTable::GetMetadata($table);
$field = 'sessionDate';
/* @var $db MapsDatabase */
$db = Core::LoadPlugin('Maps')->getDatabase();
$markers = $db->table(MapsDatabase::$MAPITEM);

$expiredEvents = json_decode(
    '{
                "join":"join","table":"' . $table . '","set":"*","filters":[
                    {"join":"intersect","table":"' . $table . '","set":"*","filters":[
                        {"field":"' .
         $field . '","comparator":"lessThan","value":"' . $end . '","format":"date"}
                    ]}
                ]
            }');

$queryExpired = 'Select m.id as id, m.lid as lid FROM ' . $markers . '  as m, ' .
     AttributesFilter::JoinAttributeFilterObject($expiredEvents, 'm.id', 'm.type') . ' AND m.lid!=6';

$db->iterate($queryExpired, 
    function ($row) use($field, $tableMetadata) {
        Core::Emit('custom.expire', array(
            'mapitem' => $row->id
        ));
    });

$revivedEvents = json_decode(
    '{
                "join":"join","table":"' . $table . '","set":"*","filters":[
                    {"join":"intersect","table":"' . $table .
         '","set":"*","filters":[
                        {"field":"' .
         $field . '","comparator":"greatorThanOrEqualTo","value":"' . $end . '","format":"date"}
                    ]}
                ]
            }');

$queryRevived = 'Select m.id as id, m.lid as lid FROM ' . $markers . '  as m, ' .
     AttributesFilter::JoinAttributeFilterObject($revivedEvents, 'm.id', 'm.type') . ' AND m.lid=6';

$db->iterate($queryRevived, 
    function ($row) use($field, $tableMetadata) {
        Core::Emit('custom.revive', array(
            'mapitem' => $row->id
        ));
    });
}