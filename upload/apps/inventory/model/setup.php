<?php
namespace Inventory;
use Sumo;
use App;

class ModelSetup extends App\Model
{
    public function install()
    {
        return true;
    }

    public function deinstall()
    {
        return true;
    }

    public function wasInstalled()
    {
        try {
            $this->select();
            return true;
        }
        catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
