<?php
$isAdmin = ($user_info['user_group_id'] == 1) ? true : false;
?>
<ul id="menu">
<li id="dashboard"><a href="<?php echo $home; ?>"><i class="fa fa-dashboard fa-fw"></i> <span><?php echo $text_dashboard; ?></span></a></li>
<li id="catalog"><a class="parent"><i class="fa fa-tags fa-fw"></i> <span>商品管理</span></a>
    <ul>
        <li><a href="<?php echo $category; ?>"><?php echo $text_category; ?></a></li>
        <li><a href="<?php echo $product; ?>"><?php echo $text_product; ?></a></li>
        <li><a href="<?php echo $product_inv; ?>">商品管理-仓库</a></li>
        <li><a href="<?php echo $product_skubarcode; ?>">商品编码管理</a></li>
        <li style="display: none"><a href="<?php echo $recurring; ?>"><?php echo $text_recurring; ?></a></li>
        <li style="display: none"><a href="<?php echo $filter; ?>"><?php echo $text_filter; ?></a></li>
        <li style="display: none"><a class="parent"><?php echo $text_attribute; ?></a>
            <ul>
                <li><a href="<?php echo $attribute; ?>"><?php echo $text_attribute; ?></a></li>
                <li><a href="<?php echo $attribute_group; ?>"><?php echo $text_attribute_group; ?></a></li>
            </ul>
        </li>
        <li style="display: none"><a href="<?php echo $option; ?>"><?php echo $text_option; ?></a></li>
        <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
        <li style="display: none"><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>
        <li style="display: none"><a href="<?php echo $review; ?>"><?php echo $text_review; ?></a></li>
        <li><a href="<?php echo $product_price; ?>">商品价格维护</a></li>
    </ul>
</li>
<li><a class="parent"><i class="fa fa-paw fa-fw"></i> <span>采购管理</span></a>
    <ul>
        <li><a href="<?php echo $purchase; ?>">生鲜采购数据导入</a></li>
        <li><a href="<?php echo $vmipurchase; ?>">生鲜盘点数据导入</a></li>
        <li><a href="<?php echo $inventory_fresh; ?>">生鲜可售库存</a></li>
        <li><a href="<?php echo $inventory_plan; ?>">生鲜预设可售库存</a></li>
        <li><a href="<?php echo $pre_purchase; ?>">采购单</a></li>
        <li><a href="<?php echo $pre_purchase_adjust; ?>">采购调整单</a></li>
	    <li><a href="<?php echo $warehouse_allocation_note; ?>">仓间调拨单</a></li>
        <li><a href="<?php echo $upload_pre_purchase; ?>">采购单收货单上传</a></li>
        <li><a href="<?php echo $inventory; ?>">快消可售库存</a></li>
        <li><a class="parent">商品原料管理</a>
            <ul>
                <li><a href="<?php echo $sku; ?>">原料管理</a></li>
                <li><a href="<?php echo $supplier; ?>">原料供应商管理</a></li>
                <li><a href="<?php echo $balance; ?>">原料供应商余额明细管理</a></li>
            </ul>
        </li>
        <li><a class="parent">商品采购价和售价调价</a>
            <ul>
                <li><a href="<?php echo $priceApplication; ?>">售价调价申请</a></li>
                <li style="display: none"><a href="<?php echo $purchasePriceApplication; ?>">采购价调价申请</a></li>
                <li><a href="<?php echo $priceExzamine; ?>">价格审核</a></li>
            </ul>
        </li>
    </ul>
</li>
<li id="extension" style="display: none"><a class="parent"><i class="fa fa-puzzle-piece fa-fw"></i> <span><?php echo $text_extension; ?></span></a>
    <ul>
        <li><a href="<?php echo $installer; ?>"><?php echo $text_installer; ?></a></li>
        <li><a href="<?php echo $modification; ?>"><?php echo $text_modification; ?></a></li>
        <li><a href="<?php echo $module; ?>"><?php echo $text_module; ?></a></li>
        <li><a href="<?php echo $shipping; ?>"><?php echo $text_shipping; ?></a></li>
        <li><a href="<?php echo $payment; ?>"><?php echo $text_payment; ?></a></li>
        <li><a href="<?php echo $total; ?>"><?php echo $text_total; ?></a></li>
        <li><a href="<?php echo $feed; ?>"><?php echo $text_feed; ?></a></li>
        <?php if ($openbay_show_menu == 1) { ?>
        <li><a class="parent"><?php echo $text_openbay_extension; ?></a>
            <ul>
                <li><a href="<?php echo $openbay_link_extension; ?>"><?php echo $text_openbay_dashboard; ?></a></li>
                <li><a href="<?php echo $openbay_link_orders; ?>"><?php echo $text_openbay_orders; ?></a></li>
                <li><a href="<?php echo $openbay_link_items; ?>"><?php echo $text_openbay_items; ?></a></li>
                <?php if ($openbay_markets['ebay'] == 1) { ?>
                <li><a class="parent"><?php echo $text_openbay_ebay; ?></a>
                    <ul>
                        <li><a href="<?php echo $openbay_link_ebay; ?>"><?php echo $text_openbay_dashboard; ?></a></li>
                        <li><a href="<?php echo $openbay_link_ebay_settings; ?>"><?php echo $text_openbay_settings; ?></a></li>
                        <li><a href="<?php echo $openbay_link_ebay_links; ?>"><?php echo $text_openbay_links; ?></a></li>
                        <li><a href="<?php echo $openbay_link_ebay_orderimport; ?>"><?php echo $text_openbay_order_import; ?></a></li>
                    </ul>
                </li>
                <?php } ?>
                <?php if ($openbay_markets['amazon'] == 1) { ?>
                <li><a class="parent"><?php echo $text_openbay_amazon; ?></a>
                    <ul>
                        <li><a href="<?php echo $openbay_link_amazon; ?>"><?php echo $text_openbay_dashboard; ?></a></li>
                        <li><a href="<?php echo $openbay_link_amazon_settings; ?>"><?php echo $text_openbay_settings; ?></a></li>
                        <li><a href="<?php echo $openbay_link_amazon_links; ?>"><?php echo $text_openbay_links; ?></a></li>
                    </ul>
                </li>
                <?php } ?>
                <?php if ($openbay_markets['amazonus'] == 1) { ?>
                <li><a class="parent"><?php echo $text_openbay_amazonus; ?></a>
                    <ul>
                        <li><a href="<?php echo $openbay_link_amazonus; ?>"><?php echo $text_openbay_dashboard; ?></a></li>
                        <li><a href="<?php echo $openbay_link_amazonus_settings; ?>"><?php echo $text_openbay_settings; ?></a></li>
                        <li><a href="<?php echo $openbay_link_amazonus_links; ?>"><?php echo $text_openbay_links; ?></a></li>
                    </ul>
                </li>
                <?php } ?>
                <?php if ($openbay_markets['etsy'] == 1) { ?>
                <li><a class="parent"><?php echo $text_openbay_etsy; ?></a>
                    <ul>
                        <li><a href="<?php echo $openbay_link_etsy; ?>"><?php echo $text_openbay_dashboard; ?></a></li>
                        <li><a href="<?php echo $openbay_link_etsy_settings; ?>"><?php echo $text_openbay_settings; ?></a></li>
                        <li><a href="<?php echo $openbay_link_etsy_links; ?>"><?php echo $text_openbay_links; ?></a></li>
                    </ul>
                </li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
    </ul>
</li>
<li id="sale"><a class="parent"><i class="fa fa-shopping-cart fa-fw"></i> <span><?php echo $text_sale; ?></span></a>
    <ul>
        <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
        <li><a href="<?php echo $financial_confirm ; ?>"><?php echo $text_financial_confirm ; ?></a></li>
        <li style="display: none"><a href="<?php echo $order_recurring; ?>"><?php echo $text_order_recurring; ?></a></li>
        <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
        <li><a href="<?php echo $order_replenish; ?>">订单补货管理</a></li>
        <li style="display: none"><a class="parent"><?php echo $text_voucher; ?></a>
            <ul>
                <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
                <li><a href="<?php echo $voucher_theme; ?>"><?php echo $text_voucher_theme; ?></a></li>
            </ul>
        </li>
        <li style="display: none"><a class="parent"><?php echo $text_paypal ?></a>
            <ul>
                <li><a href="<?php echo $paypal_search ?>"><?php echo $text_paypal_search ?></a></li>
            </ul>
        </li>
    </ul>
</li>
<li><a class="parent"><i class="fa fa-share-alt fa-fw"></i> <span><?php echo $text_marketing; ?></span></a>
    <ul>
        <li><a href="<?php echo $home_notice; ?>"><?php echo $text_home_notice; ?></a></li>
        <li><a href="<?php echo $banner; ?>"><?php echo $text_banner; ?></a></li>
        <li><a href="<?php echo $coupon; ?>"><?php echo $text_coupon; ?></a></li>
        <li><a href="<?php echo $promotion; ?>">促销规则管理</a></li>
        <li><a href="<?php echo $activity; ?>">活动分类管理</a></li>
        <li><a href="<?php echo $smspin_query; ?>">注册验证码查询</a></li>
        <li><a href="<?php echo $index_promotion; ?>"><?php echo $text_index_promotion; ?></a></li>
        <li><a href="<?php echo $bd; ?>"><?php echo $text_BD; ?></a></li>
        <li><a class="parent">客户运营</a>
            <ul>
                <li><a href="<?php echo $customer_price; ?>"><?php echo $text_customer_price; ?></a></li>
                <li><a href="<?php echo $order_instead; ?>"><?php echo $text_order_instead; ?></a></li>
                <li><a href="<?php echo $customer_activity ;?>"><?php echo $text_customer_activity; ?></a></li>
            </ul>
        </li>

        <li><a class="parent"><?php echo $text_customer; ?></a>
            <ul>
                <li><a href="<?php echo $customer; ?>"><?php echo $text_customer; ?></a></li>
                <li><a href="<?php echo $area; ?>">区域用户管理</a></li>
                <li><a href="<?php echo $customer_group; ?>"><?php echo $text_customer_group; ?></a></li>
                <?php if($isAdmin == 1) { ?>
                <li style="display: none"><a href="<?php echo $custom_field; ?>"><?php echo $text_custom_field; ?></a></li>
                <li style="display: none"><a href="<?php echo $customer_ban_ip; ?>"><?php echo $text_customer_ban_ip; ?></a></li>
                <?php } ?>
            </ul>
        </li>


        <li style="display: none"><a href="<?php echo $information; ?>"><?php echo $text_information; ?></a></li>
        <li style="display: none"><a href="<?php echo $marketing; ?>"><?php echo $text_marketing; ?></a></li>
        <li style="display: none"><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
        <li style="display: none"><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
    </ul>
</li>

<li><a class="parent"><i class="fa fa-cubes fa-fw"></i> <span>仓库管理</span></a>
    <ul>
        <li><a href="<?php echo $w_user; ?>">仓库工作人员管理</a></li>
        <li><a href="<?php echo $container; ?>">仓库周转筐管理</a></li>
        <li><a href="<?php echo $container_w; ?>">未还周转筐记录</a></li>
        <li><a class="parent">仓库分拣系统</a>
            <ul>
                <li><a href="<?php echo $warehouse_distribution; ?>">仓库分配</a></li>
                <li><a href="<?php echo $warehouse_management; ?>">仓库货位管理</a></li>
    </ul>
        </li>
        <li><a href="<?php echo $return_product; ?>">仓库退货管理</a></li>
        <li><a href="<?php echo $inventory_order_missing ?>">库内丢失确认</a></li>
        <li><a href="<?php echo $sort_error; ?>">分拣错误信息管理</a></li>
    </ul>
</li>

<li><a class="parent"><i class="fa fa-truck fa-fw"></i> <span>物流管理</span></a>
    <ul>
        <li><a href="<?php echo $logistic_allot; ?>">订单分派汇总</a></li>
        <li><a href="<?php echo $logistic_allot2; ?>">订单分派汇总2</a></li>
        <li><a href="<?php echo $logistic_allot_order; ?>">订单分派线路</a></li>
        <li><a href="<?php echo $logistic_allot_van; ?>">地图分车管理</a></li>
        <li><a href="<?php echo $logistic_info; ?>">司机线路信息管理</a></li>
    </ul>
</li>

<?php if($isAdmin == 1) { ?>
<li id="system"><a class="parent"><i class="fa fa-cog fa-fw"></i> <span><?php echo $text_system; ?></span></a>
    <ul>
        <li><a href="<?php echo $setting; ?>"><?php echo $text_setting; ?></a></li>
        <li style="display: none"><a class="parent"><?php echo $text_design; ?></a>
            <ul>
                <li><a href="<?php echo $layout; ?>"><?php echo $text_layout; ?></a></li>
            </ul>
        </li>
        <li><a class="parent"><?php echo $text_users; ?></a>
            <ul>
                <li><a href="<?php echo $user; ?>"><?php echo $text_user; ?></a></li>
                <li><a href="<?php echo $user_group; ?>"><?php echo $text_user_group; ?></a></li>
                <li><a href="<?php echo $api; ?>"><?php echo $text_api; ?></a></li>
            </ul>
        </li>






        <!--
        <li><a class="parent">仓库生产管理</a>
          <ul>
            <li><a href="<?php echo $produce_group; ?>">生产分组管理</a></li>
            <li><a href="<?php echo $produce_user; ?>">生产人员管理</a></li>
            <li><a href="<?php echo $produce_user_product; ?>">生产分组-菜品 管理</a></li>
            <li><a href="<?php echo $produce_user_product; ?>">生产人员-菜品 管理</a></li>
          </ul>
        </li>
        -->











        <li><a class="parent"><?php echo $text_localisation; ?></a>
            <ul>
                <li><a href="<?php echo $location; ?>"><?php echo $text_location; ?></a></li>
                <li><a href="<?php echo $language; ?>"><?php echo $text_language; ?></a></li>
                <li><a href="<?php echo $currency; ?>"><?php echo $text_currency; ?></a></li>
                <li><a href="<?php echo $stock_status; ?>"><?php echo $text_stock_status; ?></a></li>
                <li><a href="<?php echo $order_status; ?>"><?php echo $text_order_status; ?></a></li>
                <li><a class="parent"><?php echo $text_return; ?></a>
                    <ul>
                        <li><a href="<?php echo $return_status; ?>"><?php echo $text_return_status; ?></a></li>
                        <li><a href="<?php echo $return_action; ?>"><?php echo $text_return_action; ?></a></li>
                        <li><a href="<?php echo $return_reason; ?>"><?php echo $text_return_reason; ?></a></li>
                    </ul>
                </li>
                <li><a href="<?php echo $country; ?>"><?php echo $text_country; ?></a></li>
                <li><a href="<?php echo $zone; ?>"><?php echo $text_zone; ?></a></li>
                <li><a href="<?php echo $geo_zone; ?>"><?php echo $text_geo_zone; ?></a></li>
                <li><a class="parent"><?php echo $text_tax; ?></a>
                    <ul>
                        <li><a href="<?php echo $tax_class; ?>"><?php echo $text_tax_class; ?></a></li>
                        <li><a href="<?php echo $tax_rate; ?>"><?php echo $text_tax_rate; ?></a></li>
                    </ul>
                </li>
                <li><a href="<?php echo $length_class; ?>"><?php echo $text_length_class; ?></a></li>
                <li><a href="<?php echo $weight_class; ?>"><?php echo $text_weight_class; ?></a></li>
            </ul>
        </li>
    </ul>
</li>
<li id="tools"><a class="parent"><i class="fa fa-wrench fa-fw"></i> <span><?php echo $text_tools; ?></span></a>
    <ul>
        <li><a href="<?php echo $upload; ?>"><?php echo $text_upload; ?></a></li>
        <li style="display: none"><a href="<?php echo $backup; ?>"><?php echo $text_backup; ?></a></li>
        <li style="display: none"><a href="<?php echo $error_log; ?>"><?php echo $text_error_log; ?></a></li>
    </ul>
</li>
<?php } ?>

<li id="reports"><a class="parent"><i class="fa fa-bar-chart-o fa-fw"></i> <span><?php echo $text_reports; ?></span></a>
    <ul>
        <li><a class="parent"><?php echo $text_sale; ?></a>
            <ul>
                <li><a href="<?php echo $report_sale_order; ?>"><?php echo $text_report_sale_order; ?></a></li>
                <li><a href="<?php echo $report_sale_customer_active; ?>">BD活跃商家报告</a></li>
                <li><a href="<?php echo $report_bd_perforemance;?>">BD绩效汇总</a></li>
                <li style="display: none"><a href="<?php echo $report_sale_station; ?>">门店销售日报表</a></li>
                <li style="display: none"><a href="<?php echo $report_sale_stat; ?>">销售统计</a></li>
                <li style="display: none"><a href="<?php echo $report_sale_tax; ?>"><?php echo $text_report_sale_tax; ?></a></li>
                <li style="display: none"><a href="<?php echo $report_sale_shipping; ?>"><?php echo $text_report_sale_shipping; ?></a></li>
                <li style="display: none"><a href="<?php echo $report_sale_return; ?>"><?php echo $text_report_sale_return; ?></a></li>
                <li style="display: none"><a href="<?php echo $report_sale_coupon; ?>"><?php echo $text_report_sale_coupon; ?></a></li>

                <li><a href="<?php echo $report_product_sale; ?>">商品报表</a></li>
                <li><a href="<?php echo $report_customer_info; ?>">用户报表</a></li>
                <li><a href="<?php echo $report_logistic_driver_total; ?>">财务审核司机金额</a></li>
            </ul>
        </li>

        <li> <a href="<?php echo $report_bd_coupon ; ?>">优惠券报表</a></li>
        <li><a class="parent">库存数据</a>
            <ul>
                <li><a href="<?php echo $report_inv_mi_cold; ?>">基础库存数据</a></li>

            </ul>
        </li>
        <li><a class="parent">物流管理</a>
            <ul>
                <li><a href="<?php echo $report_logistic_info; ?>"><?php echo $text_report_logistic_info; ?></a></li>
                <li><a href="<?php echo $report_logistic_driver; ?>"><?php echo $text_report_logistic_driver; ?></a></li>
            </ul>
        </li>
        <li><a class="parent"><?php echo $text_purchase; ?></a>
            <ul>
                <li><a href="<?php echo $report_purchase_order; ?>"><?php echo $text_report_purchase_order; ?></a></li>
                <li><a href="<?php echo $report_purchase_detail; ?>"><?php echo $text_report_purchase_detail; ?></a></li>
            </ul>
        </li>
        <li style="display: none"><a class="parent"><?php echo $text_product; ?></a>
            <ul>
                <li><a href="<?php echo $report_product_viewed; ?>"><?php echo $text_report_product_viewed; ?></a></li>
                <li><a href="<?php echo $report_product_purchased; ?>"><?php echo $text_report_product_purchased; ?></a></li>
            </ul>
        </li>

        <li><a class="parent">仓库管理</a>
            <ul>
                <li><a href="<?php echo $report_warehouse_distribution; ?>"><?php echo $text_report_warehouse_distribution; ?></a></li>
                <li><a href="<?php echo $report_early_shipment; ?>">早班出货报表</a></li>
                <li><a href="<?php echo $report_sorting_staff; ?>">分拣员工报表</a></li>
                <li><a href="<?php echo $report_sorting_leader_shipment; ?>">分拣班组长报表</a></li>

            </ul>
        </li>


        <li style="display: none"><a class="parent"><?php echo $text_customer; ?></a>
            <ul>
                <li style="display: none"><a href="<?php echo $report_customer_online; ?>"><?php echo $text_report_customer_online; ?></a></li>
                <li><a href="<?php echo $report_customer_activity; ?>"><?php echo $text_report_customer_activity; ?></a></li>
                <li><a href="<?php echo $report_customer_order; ?>"><?php echo $text_report_customer_order; ?></a></li>
                <li style="display: none"><a href="<?php echo $report_customer_reward; ?>"><?php echo $text_report_customer_reward; ?></a></li>
                <li style="display: none"><a href="<?php echo $report_customer_credit; ?>"><?php echo $text_report_customer_credit; ?></a></li>
            </ul>
        </li>
        <li style="display: none"><a class="parent"><?php echo $text_marketing; ?></a>
            <ul>
                <li><a href="<?php echo $report_marketing; ?>"><?php echo $text_marketing; ?></a></li>
                <li><a href="<?php echo $report_affiliate; ?>"><?php echo $text_report_affiliate; ?></a></li>
                <li><a href="<?php echo $report_affiliate_activity; ?>"><?php echo $text_report_affiliate_activity; ?></a></li>
            </ul>
        </li>
    </ul>
</li>
</ul>
