<?php

namespace ylab\administer\renderers;

/**
 * Helper for merging two configs.
 */
class ConfigMerger
{
    /**
     * Merge default config and overwrited config.
     *
     * @param array $config
     * @param array $overwritedConfig
     * @return array
     */
    public function merge(array $config, array $overwritedConfig)
    {
        foreach ($overwritedConfig as $columnName => $columnConfig) {
            $index = array_search($columnName, $config, true);
            if ($index !== false) {
                if ($columnConfig === false) {
                    unset($config[$index]);
                } else {
                    $config[$index] = $columnConfig;
                }
            }
        }
        return $config;
    }
}
