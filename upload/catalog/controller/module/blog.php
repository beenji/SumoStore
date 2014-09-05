<?php  
class ControllerModuleBlog extends Controller 
{
    protected function index() 
    {
        $this->language->load('module/information');
        
        $this->data['heading_title'] = $this->language->get('heading_title_blog');
        
        $this->load->model('catalog/information');
        
        $this->data['blogs'] = array();

        foreach ($this->model_catalog_information->getBlogs() as $result) {
            $this->data['blogs'][] = array(
                'title' => $result['title'],
                'href'  => $this->url->link('information/blog', 'blog_id=' . $result['blog_id'])
            );
        }
        
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/blog.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/module/blog.tpl';
        } else {
            $this->template = 'default/template/module/blog.tpl';
        }
        
        $this->render();
    }
}
