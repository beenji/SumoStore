<?php
namespace Sumo;
class ControllerCatalogData extends Controller
{
	// List fields to pick from
	protected $sqlFields = array(
			'model',
			'location',
			'quantity',
			'price',
			'cost_price',
			'tax_percentage',
			'viewed',
			'name',
			'title',
			'description'
		);

	public function index()
	{
		$this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_DASHBOARD'),
            'href'      => $this->url->link('catalog/dashboard'),
        ));

        $this->document->addBreadcrumbs(array(
            'text'      => Language::getVar('SUMO_ADMIN_CATALOG_IMPORT_EXPORT'),
        ));

		$this->document->setTitle(Language::getVar('SUMO_ADMIN_CATALOG_IMPORT_EXPORT'));
		$this->load->model('catalog/data');

		$this->data = array_merge($this->data, array(
			'fields'		=> $this->sqlFields
		));

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && ($data = $this->validateForm()) !== false) {
			$products = $this->model_catalog_data->getProducts($data);

			// Assemble CSV
			if (!empty($products)) {
				$this->assembleCSV($products, $data);

				return;
			}
		}

		$this->template = 'catalog/export.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->document->addStyle('view/css/pages/data.css');
        $this->document->addScript('view/js/pages/data.js');
                
        $this->response->setOutput($this->render());
	}

	protected function validateForm()
	{
		$return = array(
			'start'  => $this->request->post['start'],
			'limit'  => $this->request->post['limit'],
			'fields' => array()
		);

		// Check if at least one field is selected, a direct foreach results in a notice
		if (isset($this->request->post['field']) && is_array($this->request->post['field'])) {
			foreach ($this->request->post['field'] as $field) {
				if (in_array($field, $this->sqlFields)) {
					$return['fields'][] = $field;
				}
			}
		}

		if (empty($return['fields'])) {
			// Nothing to do here...
			return false;
		}

		if (!isset($this->request->post['wrap'])) {
			$return['wrap'] = 0;
		} else {
			$return['wrap'] = 1;
		}

		if (empty($this->request->post['separator'])) {
			$return['separator'] = ',';
		} else {
			$return['separator'] = $this->request->post['separator'];
		}

		if (!empty($this->request->post['sort']) && in_array($this->request->post['sort'], $this->request->post['field'])) {
			$return['sort'] = $this->request->post['sort'];
		} else {
			$return['sort'] = '';
		}

		$return['name'] = !empty($this->request->post['name']) ? $this->request->post['name'] : 'export';

		if (mb_substr($return['name'], -4) != '.csv') {
			$return['name'] .= '.csv';
		}

		return $return;
	}

	protected function assembleCSV($products, $options)
	{
		$header = $body = array();

		foreach ($options['fields'] as $field) {
			$header[] = $field;
		}

		foreach ($products as $k => $product) {
			foreach ($options['fields'] as $field) {
				$body[$k][] = isset($product[$field]) ? $product[$field] : '';
			}
		}

		// Apply wrap around the vars?
		if ($options['wrap']) {
			foreach ($header as $k => $v) {
				$header[$k] = '"' . str_replace('"', '\"', $v) . '"';
			}

			foreach ($body as $k => $row) {
				foreach ($row as $sk => $v) {
					$body[$k][$sk] = '"' . str_replace('"', '\"', $v) . '"';
				}
			}
		}

		// Replace linefeeds
		foreach ($body as $k => $row) {
			foreach ($row as $sk => $v) {
				$body[$k][$sk] = str_replace('"\n"', '', $v);
			}
		}

		// Output header
		$csv = implode($options['separator'], $header) . "\n";

		// Output body
		foreach ($body as $line) {
			$csv .= implode($options['separator'], $line) . "\n";
		}

		header("Content-Type: application/csv");
		header("Content-Disposition: attachment; filename=" . $options['name']);
		$this->response->setOutput($csv);
	}
}