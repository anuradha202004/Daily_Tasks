<?php
class Sdp{
    public static function run(){
        $front =new Core_Controllers_Front();
        $front -> run();
    }
    public static function getModel($modelName){
        $parts = explode("/", $modelName);
        $parts = array_map("ucfirst", $parts);
        $className = $parts[0] . "_Model_" . implode("_", array_slice($parts, 1));
        return new $className();
    }

    public static function getBlock($blockName){
        $parts = explode("/", $blockName);
        $parts = array_map("ucfirst", $parts);
        $className = $parts[0] . "_Block_" . implode("_", array_slice($parts, 1));
        return new $className();
    }


}
?>
