<?php

//onDisplayMap this handler is not necessary...

function eventHandler(string $eventName, object $eventArguments)
{
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

    $queryExpired = 'Select m.id as id, m.lid as lid FROM ' . $markers . '  as m, ' .
    AttributesFilter::JoinAttributeFilterObject($expiredEvents, 'm.id', 'm.type') . ' AND m.lid!=6';

    $expiredItems = array();
    $db->iterate($queryExpired,
        function ($row) use ($field, $tableMetadata, &$expiredItems) {
            Core::Emit('onArchiveItem', array(
                'item' => $row->id,
            ));
            $expiredItems[] = $row->id;
        });

    $queryRevived = 'Select m.id as id, m.lid as lid FROM ' . $markers . '  as m, ' .
    AttributesFilter::JoinAttributeFilterObject($revivedEvents, 'm.id', 'm.type') . ' AND m.lid=6';

    $revivedItems = array();
    $db->iterate($queryRevived,
        function ($row) use ($field, $tableMetadata, &$revivedItems) {
            Core::Emit('onUnarchiveItem', array(
                'item' => $row->id,
            ));
            $revived[] = $row->id;

        });

    //For monitoring, if enabled, Otherwise no listeners should be bound to this event.

    Core::Emit('onUpdateArchive', array(
        'filterDate' => $end,
        'expiredItems' => $expiredItems,
        //'revivedItemsFilter' => $revivedEvents,
        'revivedItems' => $revivedItems,
    ));

}
