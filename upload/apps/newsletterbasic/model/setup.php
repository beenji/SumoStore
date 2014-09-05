<?php
namespace Newsletterbasic;
use Sumo;
use App;

class ModelSetup extends App\Model
{
    public function install()
    {
        return true;
    }

    public function activate($store_id)
    {
        $this->setAppStatus($store_id, 1);
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
