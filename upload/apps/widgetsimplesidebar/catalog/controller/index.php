<?php
namespace Widgetsimplesidebar;
use App;
use Sumo;
class ControllerWidgetsimplesidebar extends App\Controller
{
    public function index($input = array())
    {
        if (!isset($input['type']) || !method_exists($this, $input['type'])) {
            return false;
        }
        return $this->$input['type']($input);
    }

    public function categoryTree($input)
    {
        $this->load->appModel('category');

        $categories = $this->widgetsimplesidebar_model_category->getCategories($input['data']['filter_category_id']);
        $this->data['categories'] = $categories;
        $this->data['input'] = $input;

        $this->template = 'category_tree.tpl';
        $this->output = $this->render();
    }

    public function manufacturerTree($input)
    {
        $this->load->model('catalog/manufacturer');

        $this->data['items'] = $this->model_catalog_manufacturer->getManufacturers();
        $this->data['input'] = $input;

        $this->template = 'manufacturer_tree.tpl';
        $this->output = $this->render();
    }

    public function informationTree($input)
    {
        $this->load->appModel('information');

        $items = $this->widgetsimplesidebar_model_information->getItems();
        $this->data['title'] = Sumo\Language::getVar('SUMO_NOUN_INFORMATION_SINGULAR');
        $this->data['items'] = $items;
        if (isset($this->request->get['information_id'])) {
            $this->data['item_id'] = $this->request->get['information_id'];
        }

        $this->data['url'] = 'information/information';
        $this->data['type'] = 'information_id';

        $this->template = 'information_tree.tpl';
        $this->output = $this->render();
    }

    public function blogTree($input)
    {
        $this->load->model('catalog/information');
        $items = $this->model_catalog_information->getBlogs(array('limit' => 5, 'order' => 'b.publish_date'));
        $this->data['title'] = Sumo\Language::getVar('SUMO_NOUN_BLOG_TITLE');
        $this->data['items'] = $items;
        if (isset($this->request->get['blog_id'])) {
            $this->data['item_id'] = $this->request->get['blog_id'];
        }

        $this->data['url'] = 'information/blog';
        $this->data['type'] = 'blog_id';

        $this->template = 'information_tree.tpl';
        $this->output = $this->render();
    }

    public function accountTree($input)
    {
        $this->template = 'account_tree.tpl';
        $this->output = $this->render();
    }

    public function banner($input)
    {
        $this->load->appModel('Banner');
        $banners = array();
        if (!empty($input['location'])) {
            $tmpbanners = $this->widgetsimplesidebar_model_banner->getBanners($this->config->get('store_id'));
            foreach ($tmpbanners as $id => $data) {
                if ($data['location'] != $input['location']) {
                    unset($tmpbanners[$id]);
                }
            }

            $limit = count($tmpbanners) + 1;
            if (isset($input['limit'])) {
                $limit = $input['limit'];
            }
            $count = 1;
            foreach ($tmpbanners as $data) {
                if ($count > $limit) {
                    continue;
                }
                $banners[$count] = $data;
                $count++;
            }
            if (isset($input['number']) && isset($banners[$input['number']])) {
                $banners = array($banners[$input['number']]);
            }

        }
        else if (!empty($input['banner_id'])) {
            $tmpbanners = $this->widgetsimplesidebar_model_banner->getBanners($this->request->get['store_id']);
            $banners = array($tmpbanners[$input['banner_id']]);
        }

        if (count($banners)) {
            $this->data['banners'] = $banners;
        }
        else {
            $this->data['banners'] = array();
        }

        $this->template = 'banner.tpl';
        $this->output = $this->render();
    }

    public function slider($input)
    {
        $this->load->model('tool/image');
        $this->load->appModel('Banner');
        $this->data['sliders'] = $this->widgetsimplesidebar_model_banner->getBanners($this->config->get('store_id'));
        foreach ($this->data['sliders'] as $id => $data) {
            if ($data['location'] != 'slider') {
                unset($this->data['sliders'][$id]);
            }
        }
        $this->template = 'slider.tpl';
        $this->output = $this->render();
    }

    public function usp($input)
    {
        $this->load->appModel('Usp');
        $usp = false;
        if (!empty($input['location'])) {
            $tmpusps = $this->widgetsimplesidebar_model_usp->getUsps($this->config->get('store_id'));
            foreach ($tmpusps as $id => $data) {
                if ($data['location'] != $input['location']) {
                    unset($tmpusps[$id]);
                }
            }

            $usp = reset($tmpusps);

        }
        else if (!empty($input['usp_id'])) {
            $tmpusps = $this->widgetsimplesidebar_model_usp->getUsps($this->request->get['store_id']);
            $usp = $tmpusps[$input['usp_id']];
        }

        if (count($usp)) {
            $this->data['usp'] = $usp;
        }
        else {
            $this->data['usp'] = false;
        }

        $this->template = 'usp.tpl';
        $this->output = $this->render();
    }
}
