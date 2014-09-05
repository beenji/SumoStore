<?php

class CacheOld
{
    private $expire = 3600;

    public $vars, $cache;

    /* OLD, LEGACY ONLY */
    public function get($key)
    {
        return Sumo\Cache::find($key);

        //echo '<!-- OLD: get:' . $key . ' -->';
        $files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

        if ($files) {
            $cache = file_get_contents($files[0]);

            $data = unserialize($cache);

            foreach ($files as $file) {
                $time = substr(strrchr($file, '.'), 1);

                  if ($time < time()) {
                    if (file_exists($file)) {
                        unlink($file);
                    }
                  }
            }

            return $data;
        }
    }

    /* OLD, LEGACY ONLY */
    public function set($key, $value)
    {
        return Sumo\Cache::set($key, $value);

        $this->delete($key);

        $file = DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + $this->expire);

        $handle = fopen($file, 'w');

        fwrite($handle, serialize($value));

        fclose($handle);
    }

    /* OLD, LEGACY ONLY */
    public function delete($key)
    {
        return Sumo\Cache::remove($key, true);
        $files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

        if ($files) {
            foreach ($files as $file) {
                  if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
}
