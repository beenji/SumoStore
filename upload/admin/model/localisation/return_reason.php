<?php
namespace Sumo;
class ModelLocalisationReturnReason extends Model
{
    public function getReturnReason($return_reason_id)
    {
        return $this->query(
            "SELECT *
            FROM PREFIX_return_reason
            WHERE return_reason_id = " . (int)$return_reason_id . " AND language_id = " . (int)$this->config->get('language_id')
        )->fetch();
    }

    public function getReturnReasons()
    {
        return $this->fetchAll(
            "SELECT *
            FROM PREFIX_return_reason
            WHERE language_id = " . (int)$this->config->get('language_id') . "
            ORDER BY name");
    }
}
