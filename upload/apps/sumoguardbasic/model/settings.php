<?php
namespace Sumoguardbasic;
use Sumo;
use SumoGuard;
use App;

class ModelSettings extends App\Model
{
    public function enabled()
    {
        if (@file_exists(DIR_CACHE . 'sumo.guard.disabled')) {
            return 0;
        }

        $license = $this->config->get('license_key');
        if (empty($license)) {
            return 2;
        }

        $valid = SumoGuard\Settings::v();
        if (!$valid || !is_array($valid)) {
            return 3;
        }

        return 1;
    }

    public function setStatus($status)
    {
        if (!$status) {
            touch(DIR_CACHE . 'sumo.guard.disabled');
            if (!file_exists(DIR_CACHE . 'sumo.guard.disabled')) {
                exit('ERROR_CREATING_DISABLE_FILE');
            }
        }
        else {
            unlink(DIR_CACHE . 'sumo.guard.disabled');
            if (file_exists(DIR_CACHE . 'sumo.guard.disabled')) {
                exit('ERROR_ERASING_DISABLE_FILE');
            }
        }
    }
}
