<?php
    /**
     * Allows translation in Smarty templates.
     *
     * @param string $string The string to be translated.
     * @return string The translated string.
     */
    function smarty_modifier_translate($string) {
        return Zend_Registry::get('Zend_Translate')->_($string);
    }
?>
