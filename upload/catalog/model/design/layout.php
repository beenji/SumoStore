<?php
namespace Sumo;
class ModelDesignLayout extends Model
{
    public function getLayout($route)
    {
        $cacheFile = 'settings.layouts.' . $this->config->get('config_store_id');
        $cache = Cache::find($cacheFile, $route);
        if ($cache !== null) {
            return $cache;
        }
        $fallback = explode('/', $route);

        $result = Database::query("SELECT * FROM PREFIX_layout_route WHERE (:route LIKE CONCAT(route, '%') OR :fallback LIKE CONCAT(route, '%')) AND store_id = :store ORDER BY route DESC LIMIT 1", array('route' => $route, 'fallback' => $fallback[0] . '/', 'store' => $this->config->get('config_store_id')))->fetch();

        Cache::set($cacheFile, $route, $result['layout_id']);

        return $result['layout_id'];
    }
}
