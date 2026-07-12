<?php
/**
 * Language Helper — Lynvaii Multi-language Support
 */

function loadLanguage($lang = null) {
    $lang = $lang ?? getLang();
    $langFile = FRONTEND_PATH . '/lang/' . $lang . '.php';
    
    if (!file_exists($langFile)) {
        $langFile = FRONTEND_PATH . '/lang/id.php';
    }
    
    $GLOBALS['__lang'] = require $langFile;
}

/**
 * Translate a key
 * Usage: __('nav.home') or __('hero.title')
 */
function __($key, $replacements = []) {
    if (!isset($GLOBALS['__lang'])) {
        loadLanguage();
    }

    $keys = explode('.', $key);
    $value = $GLOBALS['__lang'];

    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $key; // Return key if translation not found
        }
    }

    // Replace placeholders like :name
    if (is_string($value) && !empty($replacements)) {
        foreach ($replacements as $placeholder => $replacement) {
            $value = str_replace(':' . $placeholder, $replacement, $value);
        }
    }

    return $value;
}
