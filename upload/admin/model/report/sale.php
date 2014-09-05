<?php
namespace Sumo;
class ModelReportSale extends Model
{
	public function getOrders($data = array())
	{
		$sql = "SELECT 	MIN(tmp.date_added) AS date_start,
						MAX(tmp.date_added) AS date_end,
						COUNT(tmp.order_id) AS `orders`,
						SUM(tmp.products) AS products,
						SUM(tmp.tax) AS tax,
						SUM(tmp.total) AS total
				FROM (
				    SELECT o.order_id,
				    	(
				        	SELECT SUM(op.quantity)
				        	FROM PREFIX_order_product AS op
				        	WHERE op.order_id = o.order_id
				        	GROUP BY op.order_id
				     	) AS products,
						(
						 	SELECT SUM(ot.value)
						 	FROM PREFIX_order_total AS ot
						 	WHERE ot.order_id = o.order_id
						 	AND ot.code = 'tax'
						 	GROUP BY ot.order_id
						) AS tax,
						o.total,
						o.date_added
					FROM PREFIX_order AS o";

		$sqlValues = array();

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = :orderStatusID";
			$sqlValues['orderStatusID'] = $data['filter_order_status_id'];
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= :filterDateStart";
			$sqlValues['filterDateStart'] = $data['filter_date_start'];
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= :filterDateEnd";
			$sqlValues['filterDateEnd'] = $data['filter_date_end'];
		}

		$sql .= " GROUP BY o.order_id) AS tmp";

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY DAY(tmp.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY WEEK(tmp.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY MONTH(tmp.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(tmp.date_added)";
				break;
		}

		$sql .= " ORDER BY tmp.date_added DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		return $this->query($sql, $sqlValues)->fetchAll();
	}

	public function getTotalOrders($data = array())
	{
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT DAY(date_added)) AS total FROM PREFIX_order";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT WEEK(date_added)) AS total FROM PREFIX_order";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT MONTH(date_added)) AS total FROM PREFIX_order";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added)) AS total FROM PREFIX_order";
				break;
		}

		$sqlValues = array();

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE order_status_id = :orderStatusID";
			$sqlValues['orderStatusID'] = $data['filter_order_status_id'];
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(date_added) >= :filterDateStart";
			$sqlValues['filterDateStart'] = $data['filter_date_start'];
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(date_added) <= :filterDateEnd";
			$sqlValues['filterDateEnd'] = $data['filter_date_end'];
		}

		$query = $this->query($sql, $sqlValues)->fetch();

		return $query['total'];
	}

	public function getTaxes($data = array())
	{
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order_total` ot LEFT JOIN `" . DB_PREFIX . "order` o ON (ot.order_id = o.order_id) WHERE ot.code = 'tax'";

		$sqlValues = array();

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = :orderStatusID";
			$sqlValues['orderStatusID'] = $data['filter_order_status_id'];
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= :filterDateStart";
			$sqlValues['filterDateStart'] = $data['filter_date_start'];
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= :filterDateEnd";
			$sqlValues['filterDateEnd'] = $data['filter_date_end'];
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY ot.title, DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY ot.title, WEEK(o.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY ot.title, MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY ot.title, YEAR(o.date_added)";
				break;
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		return $this->query($sql, $sqlValues)->fetchAll();
	}

	public function getTotalTaxes($data = array())
	{
		$sql = "SELECT COUNT(*) AS total FROM (SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_total` ot LEFT JOIN `" . DB_PREFIX . "order` o ON (ot.order_id = o.order_id) WHERE ot.code = 'tax'";

		$sqlValues = array();

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = :orderStatusID";
			$sqlValues['orderStatusID'] = $data['filter_order_status_id'];
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= :filterDateStart";
			$sqlValues['filterDateStart'] = $data['filter_date_start'];
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= :filterDateEnd";
			$sqlValues['filterDateEnd'] = $data['filter_date_end'];
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY WEEK(o.date_added), ot.title";
				break;
			case 'month':
				$sql .= " GROUP BY MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;
		}

		$sql .= ") tmp";

		$query = $this->query($sql, $sqlValues)->fetch();

		return $query['total'];
	}

	public function getShipping($data = array())
	{
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order_total` ot LEFT JOIN `" . DB_PREFIX . "order` o ON (ot.order_id = o.order_id) WHERE ot.code = 'shipping'";

		$sqlValues = array();

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = :orderStatusID";
			$sqlValues['orderStatusID'] = $data['filter_order_status_id'];
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= :filterDateStart";
			$sqlValues['filterDateStart'] = $data['filter_date_start'];
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= :filterDateEnd";
			$sqlValues['filterDateEnd'] = $data['filter_date_end'];
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY ot.title, DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY ot.title, WEEK(o.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY ot.title, MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY ot.title, YEAR(o.date_added)";
				break;
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		return $this->query($sql, $sqlValues)->fetchAll();
	}

	public function getTotalShipping($data = array())
	{
		$sql = "SELECT COUNT(*) AS total FROM (SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_total` ot LEFT JOIN `" . DB_PREFIX . "order` o ON (ot.order_id = o.order_id) WHERE ot.code = 'shipping'";

		$sqlValues = array();

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = :orderStatusID";
			$sqlValues['orderStatusID'] = $data['filter_order_status_id'];
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= :filterDateStart";
			$sqlValues['filterDateStart'] = $data['filter_date_start'];
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= :filterDateEnd";
			$sqlValues['filterDateEnd'] = $data['filter_date_end'];
		}

		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}

		switch($group) {
			case 'day';
				$sql .= " GROUP BY DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY WEEK(o.date_added), ot.title";
				break;
			case 'month':
				$sql .= " GROUP BY MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;
		}

		$sql .= ") tmp";

		$query = $this->query($sql, $sqlValues)->fetch();

		return $query['total'];
	}
}
