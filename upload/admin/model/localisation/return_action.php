<?php
namespace Sumo;
class ModelLocalisationReturnAction extends Model
{
    public function getReturnAction($return_reason_id)
    {
        return $this->query(
            "SELECT *
            FROM PREFIX_return_action
            WHERE return_action_id = " . (int)$return_reason_id . " AND language_id = " . (int)$this->config->get('language_id')
        )->fetch();
    }

    public function getReturnActions()
    {
        return $this->fetchAll(
            "SELECT *
            FROM PREFIX_return_action
            WHERE language_id = " . $this->config->get('language_id'));
    }
}
