<?php
namespace Sumo;
class ModelLocalisationLanguage extends Model
{
    public function addLanguage($data)
    {
        $this->query("INSERT INTO PREFIX_language SET
            name = :name,
            code = :code,
            locale = :locale,
            directory = '',
            filename = '',
            fallback = :fallback,
            image = :image,
            sort_order = :sort_order,
            status = :status", $data);

        $languageID = $this->lastInsertId();

        // Attribute
        $attributes = $this->query("SELECT * FROM PREFIX_attribute_description WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($attributes as $attribute) {
            $this->query("INSERT INTO " . DB_PREFIX . "attribute_description SET
                attribute_id = :attributeID,
                language_id = :languageID,
                name = :name", array(
                    'attributeID'   => $attribute['attribute_id'],
                    'languageID'    => $languageID,
                    'name'          => $attribute['name']));
        }

        // Attribute Group
        $attributeGroups = $this->query("SELECT * FROM PREFIX_attribute_group_description WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($attributeGroups as $attributeGroup) {
            $this->query("INSERT INTO PREFIX_attribute_group_description SET
                attribute_group_id = :attributeGroupID,
                language_id = :languageID,
                name = :name", array(
                    'attributeGroupID' => $attributeGroup['attribute_group_id'],
                    'languageID'       => $languageID,
                    'name'             => $attributeGroup['name']));
        }

        // Category
        $categories = $this->query("SELECT * FROM PREFIX_category_description WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($categories as $category) {
            $this->query("INSERT INTO PREFIX_category_description SET
                category_id = :categoryID,
                language_id = :languageID,
                name = :name,
                meta_description = :metaDescription,
                meta_keyword = :metaKeyword,
                description = :description", array(
                    'categoryID'        => $category['categoryID'],
                    'languageID'        => $languageID,
                    'name'              => $category['name'],
                    'metaDescription'   => $category['meta_description'],
                    'metaKeyword'       => $category['meta_keyword'],
                    'description'       => $category['description']));
        }

        // Customer Group
        $customerGroups = $this->query("SELECT * FROM PREFIX_customer_group_description WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($customerGroups as $customerGroup) {
            $this->query("INSERT INTO PREFIX_customer_group_description SET
                customer_group_id = :customerGroupID,
                language_id = :languageID,
                name = :name,
                description = :description", array(
                    'customerGroupID'   => $customerGroup['customer_group_id'],
                    'languageID'        => $languageID,
                    'name'              => $customerGroup['name'],
                    'description'       => $customerGroup['description']));
        }

        // Download
        $downloads = $this->query("SELECT * FROM PREFIX_download_description WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($downloads as $download) {
            $this->query("INSERT INTO PREFIX_download_description SET
                download_id = :downloadID,
                language_id = :languageID,
                name = :name", array(
                    'downloadID'    => $download['download_id'],
                    'languageID'    => $languageID,
                    'name'          => $download['name']));
        }

        // Information
        $informations = $this->query("SELECT * FROM PREFIX_information_description WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($informations as $information) {
            $this->query("INSERT INTO PREFIX_information_description SET
                information_id = :informationID,
                language_id = :languageID,
                title = :title,
                description = :description", array(
                    'informationID'     => $information['information_id'],
                    'languageID'        => $languageID,
                    'title'             => $information['title'],
                    'description'       => $information['description']));
        }

        // Length
        $lengths = $this->query("SELECT * FROM PREFIX_length_class_description WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($lenghts as $length) {
            $this->query("INSERT INTO PREFIX_length_class_description SET
                length_class_id = :lengthClassID,
                language_id = :languageID,
                title = :title,
                unit = :unit", array(
                    'lengthClassID'     => $length['length_class_id'],
                    'languageID'        => $languageID,
                    'title'             => $length['title'],
                    'unit'              => $length['unit']));
        }

        // Order Status
        $orderStatuses = $this->query("SELECT * FROM PREFIX_order_status WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($orderStatus as $orderStatus) {
            $this->query("INSERT INTO PREFIX_order_status SET
                order_status_id = :orderStatusID,
                language_id = :languageID,
                name = :name", array(
                    'orderStatusID'     => $orderStatus['order_status_id'],
                    'languageID'        => $languageID,
                    'name'              => $orderStatus['name']));
        }

        // Product
        $products = $this->query("SELECT * FROM PREFIX_product_description WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($products as $product) {
            $this->query("INSERT INTO PREFIX_product_description SET
                product_id = :productID,
                language_id = :languageID,
                name = :name,
                meta_description = :metaDescription,
                meta_keyword = :metaKeyword,
                description = :description,
                tag = :tag", array(
                    'productID'         => $product['product_id'],
                    'languageID'        => $languageID,
                    'name'              => $product['name'],
                    'metaDescription'   => $product['meta_description'],
                    'metaKeyword'       => $product['meta_keyword'],
                    'description'       => $product['description']));
        }

        // Product Attribute
        $productAttributes = $this->query("SELECT * FROM PREFIX_product_attribute WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($productAttributes as $productAttribute) {
            $this->query("INSERT INTO PREFIX_product_attribute SET
                product_id = :productID,
                attribute_id = :attributeID,
                language_id = :languageID,
                text = :text", array(
                    'productID'     => $productAttribute['product_id'],
                    'attributeID'   => $productAttribute['attribte_id'],
                    'languageID'    => $productAttribute['language_id'],
                    'text'          => $productAttribute['text']));
        }

        // Return Action
        $returnActions = $this->query("SELECT * FROM PREFIX_return_action WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($returnActions as $returnAction) {
            $this->query("INSERT INTO PREFIX_return_action SET
                return_action_id = :returnActionID,
                language_id = :languageID,
                name = :name", array(
                    'returnActionID'    => $returnAction['return_action_id'],
                    'languageID'        => $languageID,
                    'name'              => $returnAction['name']));
        }

        // Return Reason
        $returnReasons = $this->query("SELECT * FROM PREFIX_return_reason WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($returnReasons as $returnReason) {
            $this->query("INSERT INTO PREFIX_return_reason SET
                return_reason_id = :returnReasonID,
                language_id = :languageID,
                name = :name", array(
                    'returnReasonID'    => $returnReason['return_reason_id'],
                    'languageID'        => $languageID,
                    'name'              => $returnReason['name']
                ));
        }

        // Return Status
        $returnStatuses = $this->query("SELECT * FROM PREFIX_return_status WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($returnStatuses as $returnStatus) {
            $this->query("INSERT INTO PREFIX_return_status SET
                return_status_id = :returnStatusID,
                language_id = :languageID,
                name = :name", array(
                    'returnStatusID'    => $returnStatus['return_status_id'],
                    'languageID'        => $languageID,
                    'name'              => $returnStatus['name']));
        }

        // Stock Status
        $stockStatuses = $this->query("SELECT * FROM PREFIX_stock_status WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($stockStatuses as $stockStatus) {
            $this->query("INSERT INTO PREFIX_stock_status SET
                stock_status_id = :stockStatusID,
                language_id = :languageID,
                name = :name", array(
                    'stockStatusID'     => $stockStatus['stock_status_id'],
                    'languageID'        => $languageID,
                    'name'              => $stockStatus['name']));
        }

        // Voucher Theme
        $voucherThemes = $this->query("SELECT * FROM PREFIX_voucher_theme_description WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($voucherThemes as $voucherTheme) {
            $this->query("INSERT INTO PREFIX_voucher_theme_description SET
                voucher_theme_id = :voucherThemeID,
                language_id = :languageID,
                name = :name", array(
                    'voucherThemeID'    => $voucherTheme['voucher_theme_id'],
                    'languageID'        => $languageID,
                    'name'              => $voucherTheme['name']));
        }

        // Weight Class
        $weightClasses = $this->query("SELECT * FROM PREFIX_weight_class_description WHERE language_id = :languageID", array('languageID' => (int)$this->config->get('language_id')))->fetchAll();

        foreach ($weightClasses as $weightClass) {
            $this->query("INSERT INTO PREFIX_weight_class_description SET
                weight_class_id = :weightClassID,
                language_id = :languageID,
                title = :title,
                unit = :unit", array(
                    'weightClassID'     => $weightClass['weight_class_id'],
                    'languageID'        => $languageID,
                    'title'             => $weightClass['title'],
                    'unit'              => $weightClass['unit']));
        }

        Cache::removeAll(true);

        return $languageID;
    }

    public function editLanguage($languageID, $data)
    {
        $this->query("UPDATE PREFIX_language SET
            name = :name,
            code = :code,
            locale = :locale,
            fallback = :fallback,
            image = :image,
            sort_order = :sort_order,
            status = :status WHERE language_id = :language_id", array_merge($data, array('language_id' => $languageID)));

        Cache::removeAll(true);
    }

    public function deleteLanguage($languageID)
    {
        $this->query("DELETE FROM PREFIX_language WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_attribute_description WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_attribute_group_description WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_category_description WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_customer_group_description WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_download_description WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_information_description WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_length_class_description WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_order_status WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_product_attribute WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_product_description WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_return_action WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_return_reason WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_return_status WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_stock_status WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_voucher_theme_description WHERE language_id = :languageID", array('languageID' => (int)$languageID));
        $this->query("DELETE FROM PREFIX_weight_class_description WHERE language_id = :languageID", array('languageID' => (int)$languageID));

        Cache::removeAll(true);
    }

    public function getLanguage($language_id)
    {
        return $this->query("SELECT DISTINCT * FROM PREFIX_language WHERE language_id = :languageID", array('languageID' => (int)$language_id))->fetch();
    }

    public function getLanguages($data = array())
    {
        if (count($data)) {
            $sql = "SELECT * FROM PREFIX_language ";

            $sort_data = array(
                'name',
                'code',
                'sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            }
            else {
                $sql .= " ORDER BY sort_order, name";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            }
            else {
                $sql .= " ASC";
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

            return $this->fetchAll($sql);
        }
        else {
            $language_data = Cache::find('language');

            if (!$language_data || !count($language_data)) {
                $language_data = array();
                $default_language = $this->config->get('language_id');

                $query = $this->fetchAll("SELECT * FROM PREFIX_language WHERE status = 1 ORDER BY sort_order, name");

                foreach ($query as $result) {
                    $result['is_default'] = $default_language == $result['language_id'] ? true : false;
                    $language_data[$result['language_id']] = $result;
                }

                Cache::set('language', $language_data);
            }

            return $language_data;
        }
    }

    public function getTotalLanguages()
    {
        $data = $this->query("SELECT COUNT(*) AS total FROM PREFIX_language");
        return $data['total'];
    }

    public function duplicateLanguage($originalID)
    {
        $data = $this->getLanguage($originalID);
        $translations = $this->fetchAll("SELECT key_id, value FROM PREFIX_translations WHERE language_id = :id", array('id' => $originalID));

        $newLanguageID = $this->addLanguage($data);
        foreach ($translations as $list) {
            $list['languageID'] = $newLanguageID;
            $this->query("INSERT INTO PREFIX_translations SET key_id = :key_id, value = :value, language_id = :languageID", $list);
        }

        Cache::removeAll(true);
    }
}
