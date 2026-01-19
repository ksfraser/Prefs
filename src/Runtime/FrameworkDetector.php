<?php

namespace Ksfraser\Prefs\Runtime;

/**
 * Lightweight runtime detection for showing the right store options.
 */
class FrameworkDetector
{
    public static function isWordPress(): bool
    {
        return defined('ABSPATH') || function_exists('get_option') || function_exists('add_option');
    }

    public static function isSuiteCrm(): bool
    {
        // SuiteCRM/Sugar typically defines sugarEntry and has Administration class.
        return defined('sugarEntry') || class_exists('Administration');
    }

    public static function isFrontAccounting(): bool
    {
        // FrontAccounting commonly defines TB_PREF and provides db_query().
        return defined('TB_PREF') || function_exists('db_query') || function_exists('add_access_extensions');
    }

    /**
     * @return array<string,string> map store_type => label
     */
    public static function getAvailableStoreTypeLabels(): array
    {
        // Always available types.
        $types = [
            'ini_file' => 'INI File',
            'json_file' => 'JSON File',
            'xml_file' => 'XML File',
            'sgml_file' => 'SGML File (XML-like)',
            'csv_file' => 'CSV File',
            'yaml_file' => 'YAML File (ext-yaml required)',
            'pdo_table' => 'Database (PDO table)',
        ];

        if (self::isFrontAccounting()) {
            $types['fa_sys_prefs'] = 'FrontAccounting sys_prefs';
            $types['fa_table'] = 'FrontAccounting DB table';
        }

        if (self::isWordPress()) {
            $types['wp_options'] = 'WordPress options';
        }

        if (self::isSuiteCrm()) {
            $types['suite_admin'] = 'SuiteCRM Administration';
        }

        return $types;
    }
}
