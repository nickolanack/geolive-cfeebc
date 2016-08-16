<?php

//custom.expire

function eventHander(string $eventName, object $eventArguments)
{
/**
 * moves the map feature in to an archive layer (which is known to be layer id, 6)
 * @param int $id unique id for a map feature
 */
    function archive_map_feature($id)
    {
        $prefix = 'components/com_geolive/users_files/user_files_982/Uploads/'; // not used

        $icons = array(
            '[ImAgE]_JYp_[G]_rP7_SHq.png',
            'ivy_[ImAgE]_lwU_[G]_VVF.png',
            'tEk_[G]_[ImAgE]_L7_xIy.png',
            '[G]_aAJ_hLP_RDn_[ImAgE].png',
        );

        $archive = array(
            '[G]_[ImAgE]_gZf_JiS_E65.png',
            '[G]_LGt_eGZ_kDt_[ImAgE].png',
            'AQc_zn7_[G]_[ImAgE]_sTC.png',
            '0o6_Je3_[ImAgE]_[G]_PMR.png',
        );
        //
        // good - [ImAgE]_JYp_[G]_rP7_SHq ->[G]_[ImAgE]_gZf_JiS_E65

        $iconToArchiveMap = array_combine($icons, $archive);

        Core::Get('Maps');
        $marker = MapController::LoadMapItem($id);
        $iconUrl = $marker->getIcon();

        $icon = substr($iconUrl, strrpos($iconUrl, '/') + 1);
        $newIcon = $icon;
        file_put_contents(__DIR__ . DS . '.custom.log', $icon . "\n", FILE_APPEND);

        if (key_exists($icon, $iconToArchiveMap)) {
            $newIcon = $iconToArchiveMap[$icon];
            $marker->setIcon($prefix . $newIcon);
        }

        $marker->setLayerId(6);
        MapController::StoreMapFeature($marker);

        file_put_contents(__DIR__ . DS . '.custom.log',
            'archive (' . $id . ')' . $icon . '->' . $newIcon . ', layer -> 6' . "\n\n", FILE_APPEND);
    }

    archive_map_feature($eventArgs->item);
}
