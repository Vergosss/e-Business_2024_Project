<?php
/**
 * v1.0.0
 */
if(!class_exists('pisol_help')){
class pisol_help{

    static function inline($id, $title="", $width= 150, $height = 400, $echo = true){
        $msg = sprintf('<a name="%s" href="#TB_inline?width=%s&height=%s&inlineId=%s" class="thickbox"><span class="dashicons dashicons-editor-help"></span></a>', esc_attr($title), esc_attr($width), esc_attr($height), $id);

        if($echo) echo $msg;

        return $msg;
    }

    static function tooltip($msg, $echo = true){
        $msg = '<p class="pisol-tooltip" tooltip="'.esc_html($msg).'"><span class="dashicons dashicons-editor-help"></span></p>';

        if($echo) echo $msg;

        return $msg;
    }

    static function image($url, $title="", $echo = true){
        $msg = sprintf('<a title="%s" href="%s" class="thickbox"><span class="dashicons dashicons-editor-help"></span></a>', esc_attr($title),esc_url($url));

        if($echo) echo $msg;

        return $msg;
    }

    static function youtube($id, $title="Video", $width= 560, $height = 315, $echo = true){
        $msg = sprintf('<a name="%s" href="https://www.youtube.com/embed/%s?rel=0&TB_iframe=true&width=%s&height=%s" class="thickbox"><span class="dashicons dashicons-youtube"></span></a>',esc_attr($title), $id,  esc_attr($width), esc_attr($height));

        if($echo) echo $msg;

        return $msg;
    }
}
}