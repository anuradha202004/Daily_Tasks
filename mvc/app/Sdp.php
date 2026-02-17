<?php
class Sdp{
    public static function run(){
        $front =new Core_Controllers_Front();
        $front -> run();
    }
    public static function getModel($modelName){
        $parts = explode("/", $modelName);
        $module = ucfirst($parts[0]);
        $model = implode("_", array_map("ucfirst", explode("_", $parts[1])));
        $className = sprintf("%s_Model_%s", $module, $model);
        $modelObj = new $className();
        return $modelObj;
    }

    public static function getBlock($blockName){
        $parts = explode("/", $blockName);
        $module = ucfirst($parts[0]);
        $block = implode("_", array_map("ucfirst", explode("_", $parts[1])));
        $className = sprintf("%s_Block_%s", $module, $block);
        $blockObj = new $className();
        return $blockObj;
    }


}
?>
