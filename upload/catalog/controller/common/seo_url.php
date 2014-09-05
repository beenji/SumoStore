<?php
namespace Sumo;
class ControllerCommonSeoUrl extends Controller
{
    private $cache;

    public function index()
    {
        // Add rewrite to url class
        $this->url->addRewrite($this);

        $rand = str_shuffle(md5(microtime(true) . $_SERVER['REMOTE_ADDR']));

        // Decode URL
        if (isset($this->request->get['_route_'])) {
            $parts = explode('/', str_replace('.html', '', strtolower($this->request->get['_route_'])));

            foreach ($parts as $part) {
                $result = Cache::find('seo_keyword' . $part);
                if (!count($result)) {
                    $result = Database::query("SELECT * FROM PREFIX_url_alias WHERE keyword = :keyword AND store_id = :id", array('keyword' => $part, 'id' => $this->config->get('store_id')))->fetch();
                    Cache::set('seo_keyword' . $part, $result);
                }

                if (is_array($result)) {
                    $url = explode('=', $result['query']);
                    if ($url[0] == 'product_id') {
                        $this->request->get['product_id'] = $url[1];
                    }

                    if ($url[0] == 'category_id') {
                        if (!isset($this->request->get['path'])) {
                            $this->request->get['path'] = $url[1];
                        } else {
                            $this->request->get['path'] .= '_' . $url[1];
                        }
                    }

                    if ($url[0] == 'manufacturer_id') {
                        $this->request->get['manufacturer_id'] = $url[1];
                    }

                    if ($url[0] == 'information_id') {
                        $this->request->get['information_id'] = $url[1];
                    }

                    if ($url[0] == 'blog_id') {
                        $this->request->get['blog_id'] = $url[1];
                    }

                    if ($url[0] == 'app') {
                        $this->request->get['_route_'] = $url[0] . '/' . $url[1];
                    }
                }
                else {
                    $this->request->get['route'] = $rand;
                }
            }

            if (isset($this->request->get['route']) && $this->request->get['route'] == $rand) {
                $this->request->get['route'] = str_replace('.html', '', $this->request->get['_route_']);
            }
            else {
                $this->request->get['route'] = 'error/notfound';
            }

            if (!isset($this->request->get['path']) && isset($this->request->get['category_id'])) {
                $this->request->get['path'] = $this->request->get['category_id'];
            }
            if (isset($this->request->get['path']) && isset($this->request->get['product_id'])) {
                $path = explode('_', $this->request->get['path']);

                $sub = Database::query(
                    "SELECT *
                    FROM PREFIX_url_alias
                    WHERE keyword       = :keyword
                        AND category_id = :cat
                        AND language_id = :lang
                        AND store_id    = :store",
                    array(
                        'keyword'       => end($parts),
                        'cat'           => end($path),
                        'lang'          => $this->config->get('language_id'),
                        'store'         => $this->config->get('store_id')
                    )
                )->fetch();
                if (is_array($sub)) {
                    $url = explode('=', $sub['query']);
                    $this->request->get['product_id'] = $url[1];
                }

                $this->request->get['route'] = 'product/product';
            }
            elseif (isset($this->request->get['search'])) {
                $this->request->get['route'] = 'product/search';
            }
            elseif (isset($this->request->get['product_id']) && !isset($this->request->get['order_id'])) {
                $this->request->get['route'] = 'product/product';
            }
            elseif (isset($this->request->get['path'])) {
                $this->request->get['route'] = 'product/category';
            }
            elseif (isset($this->request->get['manufacturer_id'])) {
                $this->request->get['route'] = 'product/manufacturer/info';
            }
            elseif (isset($this->request->get['information_id'])) {
                $this->request->get['route'] = 'information/information';
            }
            elseif (isset($this->request->get['blog_id']) || str_replace('/', '', $this->request->get['route']) == 'blog' || $this->request->get['route'] == 'information/blog') {
                $this->request->get['route'] = 'information/blog';
            }

            if (isset($this->request->get['route'])) {
                return $this->forward($this->request->get['route']);
            }
        }
    }

    public function rewrite($link)
    {
        $link = str_replace('common/home', '', $link);

        $cache = Cache::find('rewrites', $link);
        if ($cache && !empty($cache)) {
            return $cache;
        }

        $url_info = parse_url(str_replace('&amp;', '&', $link));
        if (!empty($url_info['query'])) {
            $url_info['query'] .= '&';
        }
        else {
            $url_info['query'] = '';
        }
        if (!empty($url_info['path'])) {
            $url_info['query'] .= 'route=' . ltrim(str_replace('.html', '', $url_info['path']), '/');
        }
        $url = '';

        $data = array();
        if (isset($url_info['query'])) {
            parse_str($url_info['query'], $data);

            foreach ($data as $key => $value) {
                if (isset($data['route'])) {
                    if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id')) {

                        $product = Database::query(
                            "SELECT *
                            FROM PREFIX_url_alias
                            WHERE `query` = :query
                            AND (
                                language_id = :lang
                                OR language_id = ''
                            )",
                            array(
                                'query' => $key . '=' . $value,
                                'lang'  => $this->config->get('language_id')
                            )
                        )->fetch();

                        if (is_array($product)) {
                            $url = rtrim($url, '/') . '/' . strtolower($product['keyword']);

                            $url_info['path'] = '';
                            unset($data[$key]);
                        }
                    }
                    elseif ($key == 'path') {
                        if ($value == 'unknown') {
                            if (isset($data['product_id'])) {
                                $check = Database::query("SELECT category_id FROM PREFIX_product_to_category WHERE product_id = :id ORDER BY category_id DESC LIMIT 1", array('id' => $data['product_id']))->fetch();
                                if (isset($check['category_id'])) {
                                    $path = Database::query("SELECT category_id, path_id, level FROM PREFIX_category_path WHERE path_id = :id", array('id' => $check['category_id']))->fetch();
                                    if ($path['category_id'] == $path['path_id'] && $path['level'] == 0) {
                                        //$desc = Database::query("SELECT name FROM PREFIX_category_description WHERE category_id = :id AND language_id = :lang", array('id' => $check['category_id'], 'lang' => $this->config->get('language_id')))->fetch();
                                        //$url = rtrim($url, '/') . '/' . strtolower($desc['name']) . '/';
                                        $check = Database::query(
                                            "SELECT * FROM PREFIX_url_alias WHERE `query` = 'category_id=" . (int)$check['category_id'] . "' AND language_id = :lang",
                                            array('lang' => $this->config->get('language_id'))
                                        )->fetch();
                                        if (is_array($check)) {
                                            $url = rtrim($url, '/') . '/' . strtolower($check['keyword']) . '/';
                                            $url_info['path'] = '';
                                        }
                                        $url_info['path'] = '';
                                    }
                                    else {
                                        $paths = Database::fetchAll("SELECT path_id FROM PREFIX_category_path WHERE category_id = :id ORDER BY level ASC", array('id' => $path['category_id']));
                                        foreach ($paths as $path) {
                                            $check = Database::query(
                                                "SELECT * FROM PREFIX_url_alias WHERE `query` = 'category_id=" . (int)$path['path_id'] . "' AND language_id = :lang",
                                                array('lang' => $this->config->get('language_id'))
                                            )->fetch();
                                            if (is_array($check)) {
                                                $url = rtrim($url, '/') . '/' . strtolower($check['keyword']) . '/';
                                                $url_info['path'] = '';
                                            }
                                            //$url = rtrim($url, '/') . '/' . strtolower($desc['name']) . '/';
                                        }
                                        //$url = rtrim($url, '/') . '/-iets-/';
                                    }
                                }
                            }
                            $url_info['path'] = '';
                        }
                        else {
                            $categories = explode('_', $value);

                            foreach ($categories as $category) {
                                $check = Database::query(
                                    "SELECT * FROM PREFIX_url_alias WHERE `query` = 'category_id=" . (int)$category . "' AND language_id = :lang",
                                    array('lang' => $this->config->get('language_id'))
                                )->fetch();
                                if (is_array($check)) {
                                    $url = rtrim($url, '/') . '/' . strtolower($check['keyword']) . '/';
                                    $url_info['path'] = '';
                                }
                            }

                        }

                        unset($data[$key]);
                    }
                    else if ($data['route'] == 'information/information' && $key == 'information_id') {
                        $page = Database::query(
                            "SELECT *
                            FROM PREFIX_url_alias
                            WHERE `query` = :query
                            AND (
                                language_id = :lang
                                OR language_id = ''
                            )",
                            array(
                                'query' => 'information_id=' . $value,
                                'lang'  => $this->config->get('language_id')
                            )
                        )->fetch();

                        if (is_array($page)) {

                            // check for subpage
                            $check = Database::query("
                                SELECT parent_id
                                FROM PREFIX_information
                                WHERE information_id = " . (int)$value)->fetch();
                            if ($check['parent_id']) {
                                $query2 = Database::query("
                                    SELECT *
                                    FROM PREFIX_url_alias
                                    WHERE `query` = :query
                                    AND (
                                        language_id = " . (int)$this->config->get('language_id') . "
                                        OR language_id = ''
                                    )",
                                    array('query' => $key . '=' . (int)$check['parent_id'])
                                )->fetch();
                                if (count($query2)) {
                                    $url = rtrim($url, '/') . '/' . $query2['keyword'];
                                }
                            }

                            $url = rtrim($url, '/') . '/' . strtolower($page['keyword']);

                            $url_info['path'] = '';
                            unset($data[$key]);
                        }

                    }
                    else if ($data['route'] == 'information/blog' && $key == 'blog_id') {
                        $query = Database::query("
                            SELECT *
                            FROM PREFIX_url_alias
                            WHERE `query` = :query
                            AND (
                                language_id = " . (int)$this->config->get('language_id') . "
                                OR language_id = ''
                            )",
                            array(
                                'query' => $key . '=' . $value
                            )
                        )->fetch();

                        if (count($query)) {
                            $url = rtrim($url, '/') . '/blog/' . $query['keyword'];

                            $url_info['path'] = '';
                            unset($data[$key]);
                        }
                    }
                }
            }
        }

        if ($url) {
            unset($data['route']);

            $query = '';

            if ($data) {
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            $query .= '&' . $key . '[' . $k . ']=' . $v;
                        }
                    }
                    else {
                        $query .= '&' . $key . '=' . $value;
                    }
                }

                if ($query) {
                    $query = '?' . trim($query, '&');
                }
            }

            $return_url = str_replace('/index.php', '', $url_info['path']);
            $return_url = str_replace($url, '', $return_url) . $url . $query;

            Cache::set('rewrites', $link, $return_url);
            return $return_url;
        }
        else {
            Cache::set('rewrites', $link, $link);
            return $link;
        }
    }
}
