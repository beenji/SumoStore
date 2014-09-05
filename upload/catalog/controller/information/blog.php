<?php
namespace Sumo;
class ControllerInformationBlog extends Controller
{
    public function index()
    {
        $this->load->model('catalog/information');
        $this->data['type'] = 'blog';
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_HOME'),
            'href'      => $this->url->link('common/home'),
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => Language::getVar('SUMO_NOUN_BLOG_TITLE'),
            'href'      => $this->url->link('information/blog'),

        );

        $this->data['settings'] = $this->config->get('details_information_information_' . $this->config->get('template'));

        if (!is_array($this->data['settings']) || !count($this->data['settings'])) {
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'blogTree', 'data' => $information_info));
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'usp', 'location' => 'information'));
            $this->data['settings']['left'][] = $this->getChild('app/widgetsimplesidebar/', array('type' => 'banner', 'location' => 'information', 'data' => $information_info));
            $this->data['settings']['bottom'][] = $this->getChild('app/widgetsimpleproduct/', array('type' => 'latest', 'limit' => 6));
        }

        // Blog specific
        if (isset($this->request->get['blog_id'])) {
            $blog_id = (int) $this->request->get['blog_id'];
            $blog_info = $this->model_catalog_information->getBlog($blog_id);
            if ($blog_info) {
                $this->document->setTitle($blog_info['title']);

                $this->data['breadcrumbs'][] = array(
                    'text'      => $blog_info['title'],
                    'href'      => $this->url->link('information/blog', 'blog_id=' .  $blog_id),

                );

                $this->data['heading_title']    = $blog_info['title'];
                $this->data['button_back']      = Language::getVar('BUTTON_BACK');
                $this->data['description']      = html_entity_decode($blog_info['text'], ENT_QUOTES, 'UTF-8');
                $this->data['blog_info']        = $blog_info;
                $this->data['back']             = $this->url->link('blog');

                $this->template = 'information/content.tpl';

                $this->children = array(
                    'common/footer',
                    'common/header'
                );

                $this->response->setOutput($this->render());
            }
            else {
                $this->forward($this->url->link('information/blog'));
            }
        }
        // Blog overview
        else {

            $this->document->setTitle(Language::getVar('SUMO_NOUN_BLOG_TITLE'));

            $this->data['heading_title']    = Language::getVar('SUMO_NOUN_BLOG_TITLE');
            $this->data['no_results']  = Language::getVar('SUMO_NOUN_BLOG_NONE');

            $this->data['blogs'] = array();
            $blogs = $this->model_catalog_information->getBlogs();

            foreach ($blogs as $list) {
                $list['link']       = $this->url->link('information/blog', 'blog_id=' . $list['blog_id']);
                $list['intro_text'] = html_entity_decode($list['intro_text'], ENT_QUOTES, 'UTF-8');
                $list['text']       = html_entity_decode($list['text'], ENT_QUOTES, 'UTF-8');
                $this->data['blogs'][] = $list;
            }

            $this->template = 'information/blog_list.tpl';

            $this->children = array(
                'common/footer',
                'common/header'
            );

            $this->response->setOutput($this->render());
        }
    }
}
