<?php

/**
 * @TODO deprecated events. should update to use onUpdateAttributeRecord, and onCreateAttributeRecord
 */
// onUpdateAttributeRecord
// onCreateAttributeRecord
//
//plugin.Attributes.onUpdateRecord.markerAttributes.*
//plugin.Attributes.onCreateRecord.markerAttributes.*

function customEventHandler( /*string*/$eventName, /*object*/ $eventArguments)
{
// decide whether to archive or unarchive
    if (!empty($eventArgs)) {

        Core::LoadPlugin('Attributes');
        Core::LoadPlugin('Maps');

        $marker = MapController::LoadMapItem($eventArgs->item);
        $tableMetadata = AttributesTable::GetMetadata('markerAttributes');
        $values = AttributesRecord::GetFields($marker->getId(), $marker->getType(), 'sessionDate', $tableMetadata);

        $sessionDate = $values['sessionDate'];

        $date = strtotime($sessionDate);
        $limit = time() - (30 * 24 * 3600);

        // Unarchive items without data, or within date range

        if (empty($sessionDate) || $date > $limit) {
            file_put_contents(__DIR__ . DS . '.custom.log',
                'detect revive (' . $marker->getId() . ': ' . $sessionDate . ')' . "\n\n", FILE_APPEND);
            Core::Emit('onUnarchiveItem', $eventArgs);
        } else {

            file_put_contents(__DIR__ . DS . '.custom.log',
                'detect expire (' . $marker->getId() . ': ' . $sessionDate . ')' . "\n\n", FILE_APPEND);

            Core::Emit('onArchiveItem', $eventArgs);
        }
    }
}
