<?php
class ModelCatalogProduct extends Model {
	public function addProduct($data) {
                
            
		$this->event->trigger('pre.admin.product.add', $data);

                if($data['product_type'] == 1){
                    $sql = "select product_id from " . DB_PREFIX . "product where product_id < 5000 and product_type = 1 order by product_id desc limit 1";
                    $query = $this->db->query($sql);
                    $product_info = $query->row;
                    //echo "INSERT INTO " . DB_PREFIX . "product SET product_id = '" . ($product_info['product_id']+1) . "', name = '" . $this->db->escape($data['product_description'][2]['name']) . "',model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', inv_class_sort = '" . $this->db->escape($data['inv_class_sort']) . "', storage_mode_id = '" . (int)$data['storage_mode_id'] . "',product_type = '" . (int)$data['product_type'] . "',weight_inv_flag = '" . (int)$data['weight_inv_flag'] . "', maximum = '" . (int)$data['maximum'] . "', customer_total_limit = '" . (int)$data['customer_total_limit'] . "', wxpay_only = '" . (int)$data['wxpay_only'] . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', issupportstore = '" . (int)$data['issupportstore'] . "', price = '" . (float)$data['price'] . "', shelf_life = '" . $this->db->escape($data['shelf_life']) . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW()";exit;
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product SET product_id = '" . ($product_info['product_id']+1) . "', name = '" . $this->db->escape($data['product_description'][2]['name']) . "',model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape(trim($data['sku'])) . "', inv_class_sort = '" . $this->db->escape($data['inv_class_sort']) . "', storage_mode_id = '" . (int)$data['storage_mode_id'] . "',product_type = '" . (int)$data['product_type'] . "',weight_inv_flag = '" . (int)$data['weight_inv_flag'] . "', maximum = '" . (int)$data['maximum'] . "', customer_total_limit = '" . (int)$data['customer_total_limit'] . "', wxpay_only = '" . (int)$data['wxpay_only'] . "', location = '" . $this->db->escape($data['location']) . "', safestock = '" . (int)$data['safestock'] . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', issupportstore = '" . (int)$data['issupportstore'] . "', price = '" . (float)$data['price'] . "', shelf_life = '" . $this->db->escape($data['shelf_life']) . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', unit_size = '" . (float)$data['unit_size'] . "', inv_size = '" . ($data['inv_size'] ? (float)$data['inv_size'] : 1) . "', unit_weight_class_id = '" . (int)$data['unit_weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_new_on = '" . $this->db->escape($data['date_new_on']) . "', date_new_off = '" . $this->db->escape($data['date_new_off']) . "',  retail_price = '" . (float)$data['retail_price'] . "', date_modified = NOW()");

                }else{
                    //echo "INSERT INTO " . DB_PREFIX . "product SET name = '" . $this->db->escape($data['product_description'][2]['name']) . "',model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', inv_class_sort = '" . $this->db->escape($data['inv_class_sort']) . "', storage_mode_id = '" . (int)$data['storage_mode_id'] . "',product_type = '" . (int)$data['product_type'] . "',weight_inv_flag = '" . (int)$data['weight_inv_flag'] . "', maximum = '" . (int)$data['maximum'] . "', customer_total_limit = '" . (int)$data['customer_total_limit'] . "', wxpay_only = '" . (int)$data['wxpay_only'] . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', issupportstore = '" . (int)$data['issupportstore'] . "', price = '" . (float)$data['price'] . "', shelf_life = '" . $this->db->escape($data['shelf_life']) . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW()";exit;
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product SET name = '" . $this->db->escape($data['product_description'][2]['name']) . "',model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape(trim($data['sku'])) . "', inv_class_sort = '" . $this->db->escape($data['inv_class_sort']) . "', station_id = '" . (int)$data['station_id'] . "', storage_mode_id = '" . (int)$data['storage_mode_id'] . "',product_type = '" . (int)$data['product_type'] . "',weight_inv_flag = '" . (int)$data['weight_inv_flag'] . "', maximum = '" . (int)$data['maximum'] . "', customer_total_limit = '" . (int)$data['customer_total_limit'] . "', wxpay_only = '" . (int)$data['wxpay_only'] . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', safestock = '" . (int)$data['safestock'] . "', minimum = '" . (int)$data['minimum'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', issupportstore = '" . (int)$data['issupportstore'] . "', price = '" . (float)$data['price'] . "', shelf_life = '" . $this->db->escape($data['shelf_life']) . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', unit_size = '" . (float)$data['unit_size'] . "', inv_size = '" . ($data['inv_size'] ? (float)$data['inv_size'] : 1) . "', unit_weight_class_id = '" . (int)$data['unit_weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_new_on = '" . $this->db->escape($data['date_new_on']) . "', date_new_off = '" . $this->db->escape($data['date_new_off']) . "', retail_price = '" . (float)$data['retail_price'] . "', date_modified = NOW()");
                    
                }
		
		$product_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

                
                if (isset($data['product_sku_id'])) {
                        
			//$this->db->query("insert into  " . DB_PREFIX . "x_sku_to_product(sku_id,product_id) values (" . $data['product_sku_id'] . "," . $product_id . ")");
                        $this->db->query("UPDATE " . DB_PREFIX . "product SET sku_id = '" . $this->db->escape($data['product_sku_id']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		/*if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}*/

        if (isset($data['product_discount'])) {
            foreach ($data['product_discount'] as $product_discount) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', quantity = '" . (int)$product_discount['quantity'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
            }
        }

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		/*if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}*/

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		/*if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}*/

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		/*if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		if (isset($data['product_recurrings'])) {
			foreach ($data['product_recurrings'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.add', $product_id);*/

		return $product_id;
	}

        public function addProductWs($data) {
		$this->event->trigger('pre.admin.product.add', $data);

                
                $sql = "select product_id from " . DB_PREFIX . "product where product_type = 3 order by product_id desc limit 1";
                    $query = $this->db->query($sql);
                    $product_info = $query->row;
                    
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET product_id = '" . ($product_info['product_id']+1) . "', station_id = 3, name = '" . $this->db->escape($data['product_description'][2]['name']) . "', model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape(trim($data['sku'])) . "', inv_class_sort = '" . $this->db->escape($data['inv_class_sort']) . "', storage_mode_id = '" . (int)$data['storage_mode_id'] . "', product_type = 3,weight_inv_flag = '" . (int)$data['weight_inv_flag'] . "', maximum = '" . (int)$data['maximum'] . "', customer_total_limit = '" . (int)$data['customer_total_limit'] . "', wxpay_only = '" . (int)$data['wxpay_only'] . "', location = '" . $this->db->escape($data['location']) . "', safestock = '" . (int)$data['safestock'] . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', issupportstore = '" . (int)$data['issupportstore'] . "', price = '" . (float)$data['price'] . "', shelf_life = '" . $this->db->escape($data['shelf_life']) . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_new_on = '" . $this->db->escape($data['date_new_on']) . "', date_new_off = '" . $this->db->escape($data['date_new_off']) . "', retail_price = '" . (float)$data['retail_price'] . "', date_modified = NOW(), box_size = '" . (float)$data['box-size'] . "', unit_size = '" . (float)$data['unit_size'] . "', box_weight_class_id = '" . (int)$data['box_weight_class_id'] . "', unit_weight_class_id = '" . (int)$data['unit_weight_class_id'] . "',weight_type = '" . $this->db->escape($data['weight_type']) . "', unit_price = '" . (float)$data['unit_price'] . "', fix_discount = '" . (float)$data['fix_discount'] . "', purchase_cost = '" . (float)$data['purchase_cost'] . "', weightloss_rate = '" . (float)$data['weightloss_rate'] . "', warehouse_cost = '" . (float)$data['warehouse_cost'] . "', package_weight = '" . (float)$data['package_weight'] . "',repack_cost = '" . (float)$data['repack_cost'] . "'");
                $this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product SET product_id = '" . ($product_info['product_id']+1) . "', station_id = 3, name = '" . $this->db->escape($data['product_description'][2]['name']) . "', model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape(trim($data['sku'])) . "', inv_class_sort = '" . $this->db->escape($data['inv_class_sort']) . "', storage_mode_id = '" . (int)$data['storage_mode_id'] . "', product_type = 3,weight_inv_flag = '" . (int)$data['weight_inv_flag'] . "', maximum = '" . (int)$data['maximum'] . "', customer_total_limit = '" . (int)$data['customer_total_limit'] . "', wxpay_only = '" . (int)$data['wxpay_only'] . "', location = '" . $this->db->escape($data['location']) . "', safestock = '" . (int)$data['safestock'] . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', issupportstore = '" . (int)$data['issupportstore'] . "', price = '" . (float)$data['price'] . "', shelf_life = '" . $this->db->escape($data['shelf_life']) . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_new_on = '" . $this->db->escape($data['date_new_on']) . "', date_new_off = '" . $this->db->escape($data['date_new_off']) . "', retail_price = '" . (float)$data['retail_price'] . "', date_modified = NOW(), box_size = '" . (float)$data['box-size'] . "', unit_size = '" . (float)$data['unit_size'] . "', box_weight_class_id = '" . (int)$data['box_weight_class_id'] . "', unit_weight_class_id = '" . (int)$data['unit_weight_class_id'] . "',weight_type = '" . $this->db->escape($data['weight_type']) . "', unit_price = '" . (float)$data['unit_price'] . "', fix_discount = '" . (float)$data['fix_discount'] . "', purchase_cost = '" . (float)$data['purchase_cost'] . "', weightloss_rate = '" . (float)$data['weightloss_rate'] . "', warehouse_cost = '" . (float)$data['warehouse_cost'] . "', package_weight = '" . (float)$data['package_weight'] . "',repack_cost = '" . (float)$data['repack_cost'] . "'");

		$product_id = $this->db->getLastId();

		if (isset($data['image'])) {
                    
                        copy("../image/" . $this->db->escape($data['image']), "../../../ws/www/image/".$this->db->escape($data['image']));
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
                        $this->db->query("UPDATE xsjws." . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

                if (isset($data['product_sku_id'])) {
                        
			//$this->db->query("insert into  " . DB_PREFIX . "x_sku_to_product(sku_id,product_id) values (" . $data['product_sku_id'] . "," . $product_id . ")");
                        $this->db->query("UPDATE " . DB_PREFIX . "product SET sku_id = '" . $this->db->escape($data['product_sku_id']) . "' WHERE product_id = '" . (int)$product_id . "'");
                        $this->db->query("UPDATE xsjws." . DB_PREFIX . "product SET sku_id = '" . $this->db->escape($data['product_sku_id']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

                
		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
                        $this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		/*if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}*/

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
                                $this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
                            
                                copy("../image/" . $this->db->escape($product_image['image']), "../../../ws/www/image/".$this->db->escape($product_image['image']));
                            
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
                                $this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		/*if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}*/

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
                                $this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		/*if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}*/

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
                                
                                
                                $this->db->query("DELETE FROM xsjws." . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM xsjws." . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		/*if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		if (isset($data['product_recurrings'])) {
			foreach ($data['product_recurrings'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.add', $product_id);*/

		return $product_id;
	}

        
	public function addProductOld($data) {
		$this->event->trigger('pre.admin.product.add', $data);

		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");

		$product_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		if (isset($data['product_recurrings'])) {
			foreach ($data['product_recurrings'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.add', $product_id);

		return $product_id;
	}

	public function editProduct($product_id, $data,$user_id,$filter_warehouse_id_global) {
		$filter_warehouse_id_global = (int)$filter_warehouse_id_global;
//		echo '<pre>';var_dump($filter_warehouse_id_global);print_r($data);die;
		$this->event->trigger('pre.admin.product.edit', $data);

		$sql = "select sku_id from " . DB_PREFIX . "product where product_id = " . $product_id;;
		$query = $this->db->query($sql);
		$product_sku = $query->row;
                
			if(empty($product_sku['sku_id'])){

				$this->db->query("UPDATE " . DB_PREFIX . "product
				SET
					sku_id = '" . (int)$data['product_sku_id'] . "',
					model = '" . $this->db->escape($data['model']) . "',
					sku = '" . $this->db->escape(trim($data['sku'])) . "',
					inv_class_sort = '" . $this->db->escape($data['inv_class_sort']) . "',
					storage_mode_id = '" . (int)$data['storage_mode_id'] . "',
					maximum = '" . (int)$data['maximum'] . "',
					customer_total_limit = '" . (int)$data['customer_total_limit'] . "',
					wxpay_only = '" . (int)$data['wxpay_only'] . "',
					location = '" . $this->db->escape($data['location']) . "',
					quantity = '" . (int)$data['quantity'] . "',
					safestock = '" . (int)$data['safestock'] . "',
					minimum = '" . (int)$data['minimum'] . "',
					stock_status_id = '" . (int)$data['stock_status_id'] . "',
					date_available = '" . $this->db->escape($data['date_available']) . "',
					manufacturer_id = '" . (int)$data['manufacturer_id'] . "',
					issupportstore = '" . (int)$data['issupportstore'] . "',
					price = '" . (float)$data['price'] . "',
					price_protect = '". (float)$data['price_protect'] ."',
					cashback = '" . (float)$data['cashback'] . "',
					date_new_on = '" . $this->db->escape($data['date_new_on']) . "',
					date_new_off = '" . $this->db->escape($data['date_new_off']) . "',
					retail_price = '" . (float)$data['retail_price'] . "',
					shelf_life = '" . $this->db->escape($data['shelf_life']) . "',
					weight = '" . (float)$data['weight'] . "',
					weight_class_id = '" . (int)$data['weight_class_id'] . "',
					unit_size = '" . (float)$data['unit_size'] . "',
					inv_size = '" . ($data['inv_size'] ? (float)$data['inv_size'] : 1) . "',
					unit_weight_class_id = '" . (int)$data['unit_weight_class_id'] . "',
					length = '" . (float)$data['length'] . "',
					width = '" . (float)$data['width'] . "',
					height = '" . (float)$data['height'] . "',
					length_class_id = '" . (int)$data['length_class_id'] . "',
					status = '" . (int)$data['status'] . "',
					sort_order = '" . (int)$data['sort_order'] . "',
					date_modified = NOW()
				WHERE product_id = '" . (int)$product_id . "'");


			}
			else{

				$this->db->query("UPDATE " . DB_PREFIX . "product
				SET
					model = '" . $this->db->escape($data['model']) . "',
					sku = '" . $this->db->escape(trim($data['sku'])) . "',
					inv_class_sort = '" . $this->db->escape($data['inv_class_sort']) . "',
					storage_mode_id = '" . (int)$data['storage_mode_id'] . "',
					maximum = '" . (int)$data['maximum'] . "',
					customer_total_limit = '" . (int)$data['customer_total_limit'] . "',
					wxpay_only = '" . (int)$data['wxpay_only'] . "',
					location = '" . $this->db->escape($data['location']) . "',
					quantity = '" . (int)$data['quantity'] . "',
					safestock = '" . (int)$data['safestock'] . "',
					minimum = '" . (int)$data['minimum'] . "',
					stock_status_id = '" . (int)$data['stock_status_id'] . "',
					date_available = '" . $this->db->escape($data['date_available']) . "',
					manufacturer_id = '" . (int)$data['manufacturer_id'] . "',
					issupportstore = '" . (int)$data['issupportstore'] . "',
					price = '" . (float)$data['price'] . "',
					price_protect = '". (float)$data['price_protect'] ."',
					cashback = '" . (float)$data['cashback'] . "',
					date_new_on = '" . $this->db->escape($data['date_new_on']) . "',
					date_new_off = '" . $this->db->escape($data['date_new_off']) . "',
					retail_price = '" . (float)$data['retail_price'] . "',
					shelf_life = '" . $this->db->escape($data['shelf_life']) . "',
					weight = '" . (float)$data['weight'] . "',
					weight_class_id = '" . (int)$data['weight_class_id'] . "',
					unit_size = '" . (float)$data['unit_size'] . "',
					inv_size = '" . ($data['inv_size'] ? (float)$data['inv_size'] : 1) . "',
					unit_weight_class_id = '" . (int)$data['unit_weight_class_id'] . "',
					length = '" . (float)$data['length'] . "',
					width = '" . (float)$data['width'] . "',
					height = '" . (float)$data['height'] . "',
					length_class_id = '" . (int)$data['length_class_id'] . "',
					status = '" . (int)$data['status'] . "',
					sort_order = '" . (int)$data['sort_order'] . "',
					date_modified = NOW()
				WHERE product_id = '" . (int)$product_id . "'");

			}


		// Reset oss status while image updated
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET oss=0, image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', abstract = '" . $this->db->escape($value['abstract']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
            $this->db->query("UPDATE " . DB_PREFIX . "product SET name = '" . $this->db->escape($value['name']) . "' WHERE product_id = '" . (int)$product_id . "'");
        }

		/*$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}*/

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
        if (isset($data['product_discount'])) {
            foreach ($data['product_discount'] as $product_discount) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id .  "', quantity = '" . (int)$product_discount['quantity'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "',user_id_modify = '".(int)$user_id."'");
            }
        }

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', maximum='".(int)$product_special['maximum']."', promo_title='".$product_special['promo_title']."',customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		//将本次特价的修改保存到特价历史记录表里面
		$sql_s = "INSERT INTO oc_product_special_history (`product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end`,`maximum`,`showup`,`is_promo`,`promo_title`,`promo_limit`,`user_id_modify`,`date_modify`)
			select `product_special_id`,`product_id`,`customer_group_id`,`priority`,`price`,`date_start`,`date_end`,`maximum`,`showup`,`is_promo`,`promo_title`,`promo_limit`,'". $user_id ."',now() from oc_product_special where product_id = '".$product_id ."'";

		$query_s = $this->db->query($sql_s);

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		/*$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}*/

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		/*$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}*/

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related_log WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related_log WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
				//关联商品b表，最近一次修改人和修改日期
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related_log WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related_log SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "', user_id_modify = '".$user_id."'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related_log WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related_log SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "', user_id_modify = '".$user_id."'");

			}
		}

		//对商品属性表记录做修改历史
		$sql_p = "INSERT INTO oc_product_history (`product_id`,`station_id`,`agent_id`,`repack`,`is_gift`,`is_replenish_gift`,`instock`,`is_selected`,`is_soon_to_expire`,`product_type`,`product_type_id`,`weight_inv_flag`,`sku_id`,`status`,
			`name`,`safestock`,`date_new_on`,`date_new_off`,`model`,`sku`,`upc`,`ean`,`jan`,`isbn`,`mpn`,`location`,`stock_status_id`,
			`image`,`oss`,`manufacturer_id`,`shipping`,`price`,`cashback`,`retail_price`,`tax_class_id`,`date_available`,`weight_range_least`,`weight_range_most`,`weight`,`weight_class_id`,
			`unit_size`,`unit_weight_class_id`,`inv_size`,`box_size`,`box_weight_class_id`,`length`,`width`,`height`,`length_class_id`,`subtract`,`sort_order`,`viewed`,`date_added`,
			`date_modified`,`storage_mode_id`,`storage_mode`,`product_id_ext`,`linkproductid`,`quantity`,`minimum`,`maximum`,`unit_price`,`customer_total_limit`,`wxpay_only`,`shelf_life`,`shelf_life_strict`,
			`issupportstore`,`is_sku`,`related_product_id`,`class`,`factor`,`inv_class`,`inv_class_sort`,`produce_group_id`,`purchase_preset`,`supplier_id`,`weight_type`,`fix_discount`,`weightloss_rate`,
			`purchase_cost`,`warehouse_cost`,`repack_cost`,`package_weight`,`scan_product`,`price_protect`,`user_id_modify`,`date_modify`)
			select `product_id`,`station_id`,`agent_id`,`repack`,`is_gift`,`is_replenish_gift`,`instock`,`is_selected`,`is_soon_to_expire`,`product_type`,`product_type_id`,`weight_inv_flag`,`sku_id`,`status`,
			`name`,`safestock`,`date_new_on`,`date_new_off`,`model`,`sku`,`upc`,`ean`,`jan`,`isbn`,`mpn`,`location`,`stock_status_id`,
			`image`,`oss`,`manufacturer_id`,`shipping`,`price`,`cashback`,`retail_price`,`tax_class_id`,`date_available`,`weight_range_least`,`weight_range_most`,`weight`,`weight_class_id`,
			`unit_size`,`unit_weight_class_id`,`inv_size`,`box_size`,`box_weight_class_id`,`length`,`width`,`height`,`length_class_id`,`subtract`,`sort_order`,`viewed`,`date_added`,
`date_modified`,`storage_mode_id`,`storage_mode`,`product_id_ext`,`linkproductid`,`quantity`,`minimum`,`maximum`,`unit_price`,`customer_total_limit`,`wxpay_only`,`shelf_life`,`shelf_life_strict`,
			`issupportstore`,`is_sku`,`related_product_id`,`class`,`factor`,`inv_class`,`inv_class_sort`,`produce_group_id`,`purchase_preset`,`supplier_id`,`weight_type`,`fix_discount`,`weightloss_rate`,
			`purchase_cost`,`warehouse_cost`,`repack_cost`,`package_weight`,`scan_product`,`price_protect`,'". $user_id ."',now() from oc_product where product_id = '". $product_id ."'";

		$query_s = $this->db->query($sql_p);
		
		        //商品积分相关
        //TODO 兼容多仓
        //$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
        //if (isset($data['product_reward'])) {
        //    foreach ($data['product_reward'] as $customer_group_id => $value) {
        //       $this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
        //    }
        //}

        if (isset($data['product_reward'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', points = '" . (int)$data['product_reward'] . "'");
        }

		//删除原有商品原有的仓库信息,如果全局仓库ID变量存在，则只删除全局仓库ID的信息
//		$sql = "DELETE FROM oc_product_to_warehouse where product_id =". (int) $product_id;
//		if($filter_warehouse_id_global > 0){
//			$sql .= " and warehouse_id =". (int) $filter_warehouse_id_global;
//		}
//		$this->db->query($sql);
//		//保存仓库个性化商品信息
//		$save_info = array();
//		if(isset($data['warehouse_product']['select'])){
//			foreach($data['warehouse_product']['select'] as $value){
//				//如果当时对用仓库的价格没有填写，默认把商品信息中的价格保存到相应仓库里
//				$save_info[] = array(
//					'warehouse_id' => $value,
//					'station_id' => $data['station_id'],
//					'price' => array_key_exists($value,$data['warehouse_product']['price']) ? $data['warehouse_product']['price'][$value] : (float)$data['price'] ,
//				);
//			}
//		}
//		$m = sizeof($save_info);
//		$i = 0;
//		if($m){
//			$sql_w = "INSERT INTO oc_product_to_warehouse (`product_id`,`warehouse_id`,`station_id`,`price`) VALUES ";
//			foreach($save_info as $key => $value){
//				$i++;
//				$sql_w .= "( '".$product_id ."','".$value['warehouse_id'] ."','".$value['station_id'] ."','".$value['price'] ."')";
//
//				if($i < $m){
//					$sql_w .= ',';
//				}else{
//					$sql_w .= ';';
//				}
//			}
//			$this->db->query($sql_w);
//		}

		//先判断有没有全局仓库被选中，若被选中，则只保存相应的仓库
		if($filter_warehouse_id_global){

			if(isset($data['warehouse_info'])){
				if(array_key_exists($filter_warehouse_id_global,$data['warehouse_info'])){
//					$this->db->query("delete from oc_product_to_warehouse where product_id = '".(int) $product_id."' and warehouse_id = '".$filter_warehouse_id_global."'");

					$sql = "select warehouse_id from oc_product_to_warehouse where product_id = '".(int) $product_id."' and warehouse_id = '".$filter_warehouse_id_global."'";

					$query = $this->db->query($sql)->rows;

					if(isset($data['warehouse_info'][$filter_warehouse_id_global]['status'])){
						$status = $data['warehouse_info'][$filter_warehouse_id_global]['status'];
					}else{
						$status = 0;
					}

					if(!sizeof($query)){
					$insert = "INSERT INTO oc_product_to_warehouse set abstract = '". $this->db->escape($data['warehouse_info'][$filter_warehouse_id_global]['abstract']) ."',
						price = '".$data['warehouse_info'][$filter_warehouse_id_global]['price']."',
						safe_stock='".(int)$data['warehouse_info'][$filter_warehouse_id_global]['safe_stock']."',
						daily_limit='".(int)$data['warehouse_info'][$filter_warehouse_id_global]['daily_limit']."',
						points = '".(int)$data['warehouse_info'][$filter_warehouse_id_global]['points']."',
						warehouse_id = '".(int) $filter_warehouse_id_global."',
						product_id = '".(int) $product_id."',
						status = '".(int)$status."',
						station_id = '".(int)$data['station_id']."'
						";
					}else{
						$insert = "UPDATE oc_product_to_warehouse set abstract = '". $this->db->escape($data['warehouse_info'][$filter_warehouse_id_global]['abstract']) ."',price = '".$data['warehouse_info'][$filter_warehouse_id_global]['price']."',
						   safe_stock='".(int)$data['warehouse_info'][$filter_warehouse_id_global]['safe_stock']."',
						   daily_limit='".(int)$data['warehouse_info'][$filter_warehouse_id_global]['daily_limit']."',
						   points = '".(int)$data['warehouse_info'][$filter_warehouse_id_global]['points']."',
						   status = '".(int)$status."',station_id = '".(int)$data['station_id']."'
						   where product_id = '".(int) $product_id."' and warehouse_id = '".$filter_warehouse_id_global."'";
					}
					$this->db->query($insert);
				}
			}
		}else{
			if(isset($data['warehouse_all_set'])){
				//保存商品属性到所有仓库中
				if(isset($data['warehouse_info_all'])){
					$station_id = isset($data['station_id']) ? $data['station_id'] : 0;
					$warehouselist = array();
					$product_warehouselist = array();
					if($station_id){
						$sql = "select warehouse_id,title from oc_x_warehouse where station_id =" . $station_id;
						$query = $this->db->query($sql)->rows;
						foreach($query as $value){
							$warehouselist[] = $value['warehouse_id'];
						}
						//查询商品在仓库的分布情况，有就更新，没有就新增
						$sql = "select warehouse_id from oc_product_to_warehouse where product_id =" .(int) $product_id;
						$query = $this->db->query($sql)->rows;
						foreach($query as $value){
							$product_warehouselist[$value['warehouse_id']] = $value;
						}

						foreach($warehouselist as $value){
							if(isset($data['warehouse_info_all']['status'])){
								$status = $data['warehouse_info_all']['status'];
							}else{
								$status = 0;
							}
							if(array_key_exists($value,$product_warehouselist)){
								$sql = "update oc_product_to_warehouse set abstract = '". $this->db->escape($data['warehouse_info_all']['abstract']) ."',price = '".$data['warehouse_info_all']['price']."',safe_stock='".(int)$data['warehouse_info_all']['safe_stock']."',
										daily_limit = '".(int)$data['warehouse_info_all']['daily_limit']."',status = '".(int)$status."',
										points = '".(int)$data['warehouse_info_all']['points']."'
										where warehouse_id = '".(int) $value."' and product_id = '".(int)$product_id."'";
								$this->db->query($sql);
							}else{
								$sql = "INSERT INTO oc_product_to_warehouse set
											abstract = '". $this->db->escape($data['warehouse_info_all']['abstract']) ."',
											price = '".$data['warehouse_info_all']['price']."',
											safe_stock='".(int)$data['warehouse_info_all']['safe_stock']."',
											daily_limit = '".(int)$data['warehouse_info_all']['daily_limit']."',
											points = '".(int)$data['warehouse_info_all']['points']."',
											warehouse_id = '".(int)$value."',
											product_id = '".(int)$product_id."',
											station_id = '".(int)$station_id."',
											status = '".(int)$status."'
											";
								$this->db->query($sql);
							}

						}

					}
				}
			}
		}
//		var_dump($sql_w);die;
		/*$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);

		if (isset($data['product_recurrings'])) {
			foreach ($data['product_recurrings'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.edit', $product_id);*/
	}

        public function editProductWs($product_id, $data) {
		$this->event->trigger('pre.admin.product.edit', $data);

        $sql = "UPDATE " . DB_PREFIX . "product SET
        date_new_on = '" . $this->db->escape($data['date_new_on']) . "',
        date_new_off = '" . $this->db->escape($data['date_new_off']) . "',
        sku = '" . $this->db->escape(trim($data['sku'])) . "',
        inv_class_sort = '" . $this->db->escape($data['inv_class_sort']) . "',
        storage_mode_id = '" . (int)$data['storage_mode_id'] . "',
        -- storage_mode = '',
        maximum = '" . (int)$data['maximum'] . "',
        customer_total_limit = '" . (int)$data['customer_total_limit'] . "',
        wxpay_only = '" . (int)$data['wxpay_only'] . "',
        location = '" . $this->db->escape($data['location']) . "',
        quantity = '" . (int)$data['quantity'] . "',
        safestock = '" . (int)$data['safestock'] . "',    
        minimum = '" . (int)$data['minimum'] . "',
        stock_status_id = '" . (int)$data['stock_status_id'] . "',
        date_available = '" . $this->db->escape($data['date_available']) . "',
        manufacturer_id = '" . (int)$data['manufacturer_id'] . "',
        issupportstore = '" . (int)$data['issupportstore'] . "',
        price = '" . (float)$data['price'] . "',
        retail_price = '" . (float)$data['retail_price'] . "',
        shelf_life = '" . $this->db->escape($data['shelf_life']) . "',
        weight = '" . (float)$data['weight'] . "',
        weight_class_id = '" . (int)$data['weight_class_id'] . "',
        length = '" . (float)$data['length'] . "',
        width = '" . (float)$data['width'] . "',
        height = '" . (float)$data['height'] . "',
        length_class_id = '" . (int)$data['length_class_id'] . "',
        status = '" . (int)$data['status'] . "',
        sort_order = '" . (int)$data['sort_order'] . "',
        date_modified = NOW(), box_size = '" . (float)$data['box-size'] . "', unit_size = '" . (float)$data['unit_size'] . "', box_weight_class_id = '" . (int)$data['box_weight_class_id'] . "', unit_weight_class_id = '" . (int)$data['unit_weight_class_id'] . "',weight_type = '" . $this->db->escape($data['weight_type']) . "', unit_price = '" . (float)$data['unit_price'] . "', fix_discount = '" . (float)$data['fix_discount'] . "', purchase_cost = '" . (float)$data['purchase_cost'] . "', weightloss_rate = '" . (float)$data['weightloss_rate'] . "', warehouse_cost = '" . (float)$data['warehouse_cost'] . "', package_weight = '" . (float)$data['package_weight'] . "',repack_cost = '" . (float)$data['repack_cost'] . "'
        WHERE product_id = '" . (int)$product_id . "'";

		$this->db->query($sql);

                
                
                $sql = "UPDATE xsjws." . DB_PREFIX . "product SET
        date_new_on = '" . $this->db->escape($data['date_new_on']) . "',
        date_new_off = '" . $this->db->escape($data['date_new_off']) . "',
        sku = '" . $this->db->escape(trim($data['sku'])) . "',
        inv_class_sort = '" . $this->db->escape($data['inv_class_sort']) . "',
        storage_mode_id = '" . (int)$data['storage_mode_id'] . "',
        -- storage_mode = '',
        maximum = '" . (int)$data['maximum'] . "',
        customer_total_limit = '" . (int)$data['customer_total_limit'] . "',
        wxpay_only = '" . (int)$data['wxpay_only'] . "',
        location = '" . $this->db->escape($data['location']) . "',
        quantity = '" . (int)$data['quantity'] . "',
        safestock = '" . (int)$data['safestock'] . "',    
        minimum = '" . (int)$data['minimum'] . "',
        stock_status_id = '" . (int)$data['stock_status_id'] . "',
        date_available = '" . $this->db->escape($data['date_available']) . "',
        manufacturer_id = '" . (int)$data['manufacturer_id'] . "',
        issupportstore = '" . (int)$data['issupportstore'] . "',
        price = '" . (float)$data['price'] . "',
        retail_price = '" . (float)$data['retail_price'] . "',
        shelf_life = '" . $this->db->escape($data['shelf_life']) . "',
        weight = '" . (float)$data['weight'] . "',
        weight_class_id = '" . (int)$data['weight_class_id'] . "',
        length = '" . (float)$data['length'] . "',
        width = '" . (float)$data['width'] . "',
        height = '" . (float)$data['height'] . "',
        length_class_id = '" . (int)$data['length_class_id'] . "',
        status = '" . (int)$data['status'] . "',
        sort_order = '" . (int)$data['sort_order'] . "',
        date_modified = NOW(), box_size = '" . (float)$data['box-size'] . "', unit_size = '" . (float)$data['unit_size'] . "', box_weight_class_id = '" . (int)$data['box_weight_class_id'] . "', unit_weight_class_id = '" . (int)$data['unit_weight_class_id'] . "',weight_type = '" . $this->db->escape($data['weight_type']) . "', unit_price = '" . (float)$data['unit_price'] . "', fix_discount = '" . (float)$data['fix_discount'] . "', purchase_cost = '" . (float)$data['purchase_cost'] . "', weightloss_rate = '" . (float)$data['weightloss_rate'] . "', warehouse_cost = '" . (float)$data['warehouse_cost'] . "', package_weight = '" . (float)$data['package_weight'] . "',repack_cost = '" . (float)$data['repack_cost'] . "'
        WHERE product_id = '" . (int)$product_id . "'";

		$this->db->query($sql);

		// Reset oss status while image updated
		if (isset($data['image'])) {
                        
                        copy("../image/" . $this->db->escape($data['image']), "../../../ws/www/image/".$this->db->escape($data['image']));
			$this->db->query("UPDATE " . DB_PREFIX . "product SET oss=0, image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
                        $this->db->query("UPDATE xsjws." . DB_PREFIX . "product SET oss=0, image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}


		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("DELETE FROM xsjws." . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', abstract = '" . $this->db->escape($value['abstract']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
                        $this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', abstract = '" . $this->db->escape($value['abstract']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		/*$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}*/

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("DELETE FROM xsjws." . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
                                $this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("DELETE FROM xsjws." . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
                                copy("../image/" . $this->db->escape($product_image['image']), "../../../ws/www/image/".$this->db->escape($product_image['image']));
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
                                $this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		/*$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}*/

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("DELETE FROM xsjws." . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
                                $this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		/*$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}*/

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

                $this->db->query("DELETE FROM xsjws." . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM xsjws." . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

                
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
                                
                                
                                $this->db->query("DELETE FROM xsjws." . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM xsjws." . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO xsjws." . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		/*$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);

		if (isset($data['product_recurrings'])) {
			foreach ($data['product_recurrings'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.edit', $product_id);*/
	}

	public function editProductOld($product_id, $data) {
		$this->event->trigger('pre.admin.product.edit', $data);

		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

        // Reset oss status while image updated
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET oss=0, image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);

		if (isset($data['product_recurrings'])) {
			foreach ($data['product_recurrings'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.edit', $product_id);
	}

	public function copyProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		if ($query->num_rows) {
			$data = array();

			$data = $query->row;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';
                        $data['sku_id'] = '0';

			$data = array_merge($data, array('product_attribute' => $this->getProductAttributes($product_id)));
			$data = array_merge($data, array('product_description' => $this->getProductDescriptions($product_id)));
			$data = array_merge($data, array('product_discount' => $this->getProductDiscounts($product_id)));
			$data = array_merge($data, array('product_filter' => $this->getProductFilters($product_id)));
			$data = array_merge($data, array('product_image' => $this->getProductImages($product_id)));
			$data = array_merge($data, array('product_option' => $this->getProductOptions($product_id)));
			$data = array_merge($data, array('product_related' => $this->getProductRelated($product_id)));
			$data = array_merge($data, array('product_reward' => $this->getProductRewards($product_id)));
			$data = array_merge($data, array('product_special' => $this->getProductSpecials($product_id)));
			$data = array_merge($data, array('product_category' => $this->getProductCategories($product_id)));
			$data = array_merge($data, array('product_download' => $this->getProductDownloads($product_id)));
			$data = array_merge($data, array('product_layout' => $this->getProductLayouts($product_id)));
			$data = array_merge($data, array('product_store' => $this->getProductStores($product_id)));
			$data = array_merge($data, array('product_recurrings' => $this->getRecurrings($product_id)));

			$this->addProduct($data);
		}
	}

	public function deleteProduct($product_id) {
		$this->event->trigger('pre.admin.product.delete', $product_id);
        return false;

		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_recurring WHERE product_id = " . (int)$product_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.delete', $product_id);
	}

//	public function getPurchasePrice($product_id){
//		$sql = "select p.product_id,p.name,p.price,s.supplier_order_quantity_type,if(p.instock = 0,s.price,if(s.supplier_order_quantity_type = 1,s.price*p.inv_size/s.supplier_unit_size,s.price*p.inv_size)) purchase_price
//			from oc_product p
//			left join oc_x_sku s on s.sku_id = p.sku_id
//			where p.product_id = '".(int)$product_id."'
//			";
//
//		$query = $this->db->query($sql);
//
//		return $query->row['purchase_price'];
//	}

	public function getPurchasePrice($product_id){
		$sql = "select pc.purchase_cost as purchase_price from oc_x_purchase_cost pc where pc.product_id = '".(int) $product_id."'";

		$query = $this->db->query($sql);

		return isset($query->row['purchase_price']) ? $query->row['purchase_price'] : 0;
	}

	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE  p.product_id = '" . (int)$product_id . "' AND  pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

               
		return $query->row;
	}

	public function getProducts($data = array(),$filter_warehouse_id_global) {
		$Separator = '<br />';
        if(!empty($data['filter_product_category'])){
            $sql = "SELECT
            p.*,pd.*,count(DISTINCT pw.warehouse_id) warehouse_num ,
			 GROUP_CONCAT(DISTINCT pw.sku_barcode SEPARATOR '".$Separator."') sku_barcode,
			 concat(round(p.weight,0), wd.title) w_size,
			 max(if(pw.daily_limit is not null,pw.daily_limit,0)) max_limit,min(if(pw.daily_limit is not null,pw.daily_limit,0)) min_limit,
		     if(cd1.name is not null and cd1.name is not null,concat(cd2.name,'>>',cd1.name),'') category,
		     sw.s_w_num
		     FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            left join oc_product_to_category as ptc on ptc.product_id = p.product_id
            LEFT JOIN oc_category c1 on c1.category_id = ptc.category_id
			 LEFT join oc_category c2 on c2.category_id = c1.parent_id
			 LEFT JOIN oc_category_description cd1 ON (c1.category_id= cd1.category_id)
			 LEFT JOIN oc_category_description cd2 ON (c2.category_id = cd2.category_id)
            LEFT JOIN oc_product_to_warehouse pw ON pw.product_id = p.product_id
            left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id
            left join (
						select s.station_id , count(DISTINCT w.warehouse_id) s_w_num from oc_x_station s
						left join oc_x_warehouse w on w.station_id = s.station_id
						group by s.station_id
						) sw on sw.station_id = p.station_id
            WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        }
        else{
            $sql = "SELECT
			 p.*,pd.*,count(DISTINCT pw.warehouse_id) warehouse_num ,
			 GROUP_CONCAT(DISTINCT pw.sku_barcode SEPARATOR '".$Separator."') sku_barcode,
			 concat(round(p.weight,0), wd.title) w_size,
			 max(if(pw.daily_limit is not null,pw.daily_limit,0)) max_limit,min(if(pw.daily_limit is not null,pw.daily_limit,0)) min_limit,
			 if(cd1.name is not null and cd1.name is not null,concat(cd2.name,'>>',cd1.name),'') category,
			 sw.s_w_num
			 FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            left join oc_product_to_category as ptc on ptc.product_id = p.product_id
            LEFT JOIN oc_category c1 on c1.category_id = ptc.category_id
			 LEFT join oc_category c2 on c2.category_id = c1.parent_id
			 LEFT JOIN oc_category_description cd1 ON (c1.category_id= cd1.category_id)
			 LEFT JOIN oc_category_description cd2 ON (c2.category_id = cd2.category_id)
            LEFT JOIN oc_product_to_warehouse pw ON pw.product_id = p.product_id
            left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id
            left join (
						select s.station_id , count(DISTINCT w.warehouse_id) s_w_num from oc_x_station s
						left join oc_x_warehouse w on w.station_id = s.station_id
						group by s.station_id
						) sw on sw.station_id = p.station_id
            WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        }

		if($filter_warehouse_id_global){
			$sql .= " AND pw.warehouse_id = '".(int)$filter_warehouse_id_global."'";
		}

		if (!empty($data['filter_sku'])) {
			$sql_s = "select product_id from oc_product_sku_barcode where sku_barcode = '".(int) $data['filter_sku']."' group by product_id";
			$query_product = $this->db->query($sql_s);

			if($query_product->num_rows){
				$sql .= " AND p.product_id = '".(int)$query_product->row['product_id']."'";
			}else{
				$sql .= " AND p.product_id = 0";
			}

		}


		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$products_filter = preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/" ,',' ,$this->db->escape($data['filter_model']));
			$products_array = explode(',',$products_filter);
			if(sizeof($products_array)){
				$product_ids = implode(',',$products_array);
				$sql .= " AND p.product_id in(". $product_ids .")";
			}
		}

//		if (!empty($data['filter_product_id'])) {
//			$products_array = explode(',',$this->db->escape($data['filter_product_id']));
//			if(sizeof($products_array)){
//				$product_ids = implode(',',$products_array);
//				$sql .= " AND p.product_id in(". $product_ids .")";
//			}
//		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
		
		if (isset($data['filter_product_type']) && !is_null($data['filter_product_type'])) {
			$sql .= " AND p.product_type = '" . (int)$data['filter_product_type'] . "'";
		}
                
		if (isset($data['filter_product_category']) && !is_null($data['filter_product_category'])) {
			$sql .= " AND ptc.category_id = '" . (int)$data['filter_product_category'] . "'";
		}

		if (isset($data['filter_station_id']) && !is_null($data['filter_station_id'])) {
			$sql .= " AND p.station_id = '" . (int)$data['filter_station_id'] . "'";
		}

		if (isset($data['filter_product_type_id']) && !is_null($data['filter_product_type_id'])) {
			$sql .= " AND p.product_type_id = '" . (int)$data['filter_product_type_id'] . "'";
		}
		
		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                        $data['sort'] = $data['sort'] == 'p.model' ? 'p.product_id' : $data['sort'];
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.product_id";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
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

		$query = $this->db->query($sql);

		return $query->rows;
	}

        
        public function getProductSku($product_id){
            //$sql = "SELECT s.sku_id, s.`name` FROM oc_x_sku AS s LEFT JOIN oc_x_sku_to_product AS stp ON s.sku_id = stp.sku_id WHERE stp.product_id = " . $product_id;
            $sql = "SELECT s.sku_id, s.`name` FROM oc_product AS p LEFT JOIN oc_x_sku AS s ON s.sku_id = p.sku_id WHERE p.product_id = " . $product_id;
            $query = $this->db->query($sql);
            return $query->row;
        }
        
        public function getProductSkus($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "x_sku as s WHERE s.status = 1 and ";

		if (!empty($data['filter_product_sku'])) {
			$sql .= "  s.name LIKE '%" . $this->db->escape($data['filter_product_sku']) . "%'";
		}

		
		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY s.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
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

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'abstract'      => $result['abstract'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $product_description_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductFilters($product_id) {
		$product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "'");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount p LEFT JOIN ". DB_PREFIX ."user u on u.user_id = p.user_id_modify WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array(),$filter_warehouse_id_global) {
		if(!empty($data['filter_product_category'])){
			$sql = "SELECT COUNT(DISTINCT p.product_id) AS total
		     FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            left join oc_product_to_category as ptc on ptc.product_id = p.product_id
            LEFT JOIN oc_product_to_warehouse pw ON pw.product_id = p.product_id
            left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id
            WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		}
		else{
			$sql = "SELECT COUNT(DISTINCT p.product_id) AS total
			 FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN oc_product_to_warehouse pw ON pw.product_id = p.product_id
            left join oc_weight_class_description wd on p.weight_class_id = wd.weight_class_id
            WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		}

		if($filter_warehouse_id_global){
			$sql .= " AND pw.warehouse_id = '".(int)$filter_warehouse_id_global."'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_sku'])) {
			$sql_s = "select product_id from oc_product_sku_barcode where sku_barcode = '".(int) $data['filter_sku']."' group by product_id";
			$query_product = $this->db->query($sql_s);

			if($query_product->num_rows){
				$sql .= " AND p.product_id = '".(int)$query_product->row['product_id']."'";
			}else{
				$sql .= " AND p.product_id = 0";
			}

		}

		if (!empty($data['filter_model'])) {
			$products_filter = preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)/" ,',' ,$this->db->escape($data['filter_model']));
			$products_array = explode(',',$products_filter);
			if(sizeof($products_array)){
				$product_ids = implode(',',$products_array);
				$sql .= " AND p.product_id in(". $product_ids .")";
			}
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

                if (isset($data['filter_product_type']) && !is_null($data['filter_product_type'])) {
			$sql .= " AND p.product_type = '" . (int)$data['filter_product_type'] . "'";
		}
                
                if (isset($data['filter_product_category']) && !is_null($data['filter_product_category'])) {
			$sql .= " AND ptc.category_id = '" . (int)$data['filter_product_category'] . "'";
		}
		
		if (isset($data['filter_station_id']) && !is_null($data['filter_station_id'])) {
			$sql .= " AND p.station_id = '" . (int)$data['filter_station_id'] . "'";
		}

		if (isset($data['filter_product_type_id']) && !is_null($data['filter_product_type_id'])) {
			$sql .= " AND p.product_type_id = '" . (int)$data['filter_product_type_id'] . "'";
		}
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

    public function getStationInfo(){
        $query = $this->db->query('SELECT * FROM oc_x_station');

        $stationInfo = array();
        foreach($query->rows as $v){
            $stationInfo[$v['station_id']] = $v;
        }

        return $stationInfo;
    }

    public function getProductPromotion(){
       //TODO get special price and discount rules
    }

	public function getSpecialPriceHistory($product_id, $start = 0, $limit = 10){
		if ($start<0) {
			$start = 0;
		}
		if ($limit<1) {
			$limit = 10;
		}

		$sql = "select h.showup, h.promo_title, h.maximum, h.price, h.date_start, h.date_end, u.username
			from oc_product_special_history h
			left join oc_user u on u.user_id = h.user_id_modify
			where h.product_id = '". $product_id ."'
			order by date_modify desc
			limit $start,$limit";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalSpecialPriceHistory($product_id){
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_special_history WHERE product_id = '" . (int)$product_id . "'");

		return $query->row['total'];
	}

	public function getProductModifyHistory($product_id, $start = 0, $limit = 10){
		if ($start<0) {
			$start = 0;
		}
		if ($limit<1) {
			$limit = 10;
		}

		$sql = "select h.sku_id, h.sku, h.model, h.inv_class_sort, h.storage_mode_id, h.maximum, h.customer_total_limit,
			h.wxpay_only, h.location, h.quantity, h.safestock, h.minimum, h.stock_status_id, h.date_available, manufacturer_id,
			h.issupportstore, h.price , h.cashback ,h.date_new_on, h.date_new_off, h.retail_price, h.shelf_life ,h.weight, h. weight_class_id,
			h.unit_size, h.inv_size, h.unit_weight_class_id, h.length, h.width, h.height, h.length_class_id, h.status, h.sort_order,
			h.date_modify, u.username
			from oc_product_history h
			left join oc_user u on u.user_id = h.user_id_modify
			where h.product_id = '". $product_id ."'
			order by date_modify desc
			limit $start,$limit";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalProductModifyHistory($product_id){
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_history WHERE product_id = '" . (int)$product_id . "'");

		return $query->row['total'];
	}

	public function  getPriceProtect($product_id){
		$sql = "select price_protect*price as protect_price from oc_product where product_id = '". $product_id ."'";

		$query = $this->db->query($sql);

		return $query->row['protect_price'];
	}

	//获取商品在不同仓库的个性信息，如价格，库存等
	public function getProductInAllWarehouse($product_id){
		$sql = "select if(pw.warehouse_id is not null,pw.warehouse_id,0) warehouse_id,p.product_id,p.name,p.price,if(pw.price is not null,pw.price,0)as w_price,
				pw.abstract,pw.safe_stock,pw.sku_barcode,pw.stock_area,pw.status,pw.daily_limit,pw.points
				from oc_product p
				left join oc_product_to_warehouse pw on pw.product_id = p.product_id
				where p.product_id = '". (int)$product_id ."'
				group by p.product_id,pw.warehouse_id";

		$query = $this->db->query($sql);

		$warehouse_products = array();
		if(sizeof($query->rows)){
			foreach($query->rows as $value){
				$warehouse_products[$value['warehouse_id']] = $value;
			}
		}

		//判断商品是否在某个仓库设置个性价格，如果有，就在前台对应仓库里列出来，没有置空

		return $warehouse_products;
	}

}