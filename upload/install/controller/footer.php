<?php
namespace Sumo;
class ControllerFooter extends Controller
{
    public function index()
    {
        foreach (glob(DIR_LANGUAGE . '*.php') as $language) {
            $this->data['languages'][] = str_replace('.php', '', basename($language));
        }
        $this->template = 'footer.tpl';
        $this->render();
    }
}
