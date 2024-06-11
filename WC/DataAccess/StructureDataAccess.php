<?php
namespace WC\DataAccess;

use WC\WitchCase;

/**
 * Class to aggregate Structure related data access functions
 * all these functions are statics
 * 
 * @author Jean2Grom
 */
class StructureDataAccess 
{
    static function readStructuresUsage( WitchCase $wc, array $structures ) 
    {
        if( empty($structures) ){
            return false;
        }

        $query = "";
        $query  .=  "SELECT `cauldron`.`data`->>'$.structure' AS structure";
        $query  .=  ", COUNT(`cauldron`.`id`) AS cauldron ";
        $query  .=  ", COUNT(`witch`.`id`) AS witches ";
        $query  .=  "FROM `cauldron` ";
        $query  .=  "LEFT JOIN `witch` ";
        $query  .=      "ON `cauldron`.`id`=`witch`.`cauldron` ";
        $query  .=  "WHERE `cauldron`.`data`->>'$.structure' ";
        $query  .=      "IN ( :".implode("key, :", array_keys($structures))."key ) ";
        $query  .=  "GROUP BY structure ";
        
        $params = [];
        foreach( $structures as $i => $structure ){
            $params[ $i."key" ] = $structure;
        }

        $result = $wc->db->selectQuery($query, $params);

        if( $result === false ){
            return false;
        }

        $return = [];
        foreach( $structures as $structure ){
            $return[ $structure ] = [ "cauldron" => 0, "witches" => 0 ];
        }
        
        foreach( $result as $row ){
            $return[ $row['structure'] ] = [ 
                "cauldron"  => $row['cauldron'], 
                "witches"   => $row['witches'] 
            ];
        }

        return $return;
    }

}
