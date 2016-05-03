<?php


//custom.revive

function eventHandler(string $eventName, object $eventArguments){
/**
 * move the map feature into an active layer using
 * a icon set map to choose the layer id.
 *
 * @param int $id unique id for a map feature
 */
function unarchive_map_feature($id) {
    $prefix = 'components/com_geolive/users_files/user_files_983/Uploads/'; //
    
    $icons = array(
        '[ImAgE]_JYp_[G]_rP7_SHq.png',
        'ivy_[ImAgE]_lwU_[G]_VVF.png',
        'tEk_[G]_[ImAgE]_L7_xIy.png',
        '[G]_aAJ_hLP_RDn_[ImAgE].png'
    );
    $layers = array(
        1,
        2,
        3,
        4
    );
    $archive = array(
        '[G]_[ImAgE]_gZf_JiS_E65.png',
        '[G]_LGt_eGZ_kDt_[ImAgE].png',
        'AQc_zn7_[G]_[ImAgE]_sTC.png',
        '0o6_Je3_[ImAgE]_[G]_PMR.png'
    );
    
    $iconToLayerMap = array_combine($icons, $layers);
    $archiveToLayerMap = array_combine($archive, $layers);
    $archiveToIconMap = array_combine($archive, $icons);
    
    Core::Get('Maps');
    $marker = MapController::LoadMapItem($id);
    $iconUrl = $marker->getIcon();
    
    $icon = substr($iconUrl, strrpos($iconUrl, '/') + 1);
    $layer = 6;
    $newIcon = $icon;
    if (key_exists($icon, $iconToLayerMap)) {
        $layer = $iconToLayerMap[$icon];
    }
    
    if (key_exists($icon, $archiveToLayerMap)) {
        
        $layer = $archiveToLayerMap[$icon];
        $newIcon = $archiveToIconMap[$icon];
    }
    
    $marker->setLayerId($layer);
    $marker->setIcon($prefix . $newIcon);
    MapController::StoreMapFeature($marker);
    
    file_put_contents(__DIR__ . DS . '.custom.log', 
        'unarchive (' . $id . ')' . $icon . " -> " . $newIcon . ', layer -> ' . $layer . "\n\n", FILE_APPEND);
}

unarchive_map_feature($eventArgs->mapitem);
}