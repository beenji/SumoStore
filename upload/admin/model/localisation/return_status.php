<?php
namespace Sumo;
class ModelLocalisationReturnStatus extends Model
{
    public function getReturnStatus($return_status_id)
    {
        return $this->query(
            "SELECT *
            FROM PREFIX_return_status
            WHERE return_status_id = " . (int)$return_status_id . " AND language_id = " . (int)$this->config->get('language_id')
        )->fetch();
    }

    public function getReturnStatuses()
    {
        return $this->fetchAll(
            "SELECT *
            FROM PREFIX_return_status
            WHERE language_id = " . $this->config->get('language_id'));
    }
}
