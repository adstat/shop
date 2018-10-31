<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$ad = [1=>2,2=>3,4=>32,67=>123,5=>32,3=>4];
//var_dump($ad);
//$b = [];
//foreach ($ad as $key=>$value) {
//    $b[$key] = $value;
//}
//var_dump($b);
//$a = 1;
////$a += 1;
//$b = &$a;
//$c = $a;
//$d = &$b;
//$a += 1;
//
//var_dump($a);
//var_dump($b);
//var_dump($c);
//var_dump($d);
//$a = [];
//$b = '';
//$c = &$b;
//$a[] = '一斤包子';
//if ($c='西瓜') {
//    $a[] = $c;
//}
//    $b = '西瓜';
//    var_dump($a);
//static $a = 2;
//$b =2;
//$a++;
//$a--;
//var_dump($a);
//switch ($a){
//    case $b== $a:
//        var_dump(1);
//        break;
//    case $b!=$a :
//        var_dump(3);
//        break;
//    case $b== 2 && $a == 2:
//        var_dump(2);
//        break;
//    case $b== 2 && $a == 1:
//        var_dump(4);
//        break;
//}
//$c =
//    [1,[1,2]]
////false
////22
////null
////'123123'
////0
//
////'a:2:{s:6:\"access\";a:263:{i:0;s:19:\"accounting/exchange\";i:1;s:25:\"accounting/return_deposit\";i:2;s:11:\"agent/agent\";i:3;s:16:\"catalog/activity\";i:4;s:17:\"catalog/attribute\";i:5;s:23:\"catalog/attribute_group\";i:6;s:15:\"catalog/balance\";i:7;s:16:\"catalog/category\";i:8;s:16:\"catalog/download\";i:9;s:14:\"catalog/filter\";i:10;s:19:\"catalog/information\";i:11;s:17:\"catalog/inventory\";i:12;s:20:\"catalog/manufacturer\";i:13;s:14:\"catalog/option\";i:14;s:13:\"catalog/price\";i:15;s:15:\"catalog/product\";i:16;s:27:\"catalog/product_application\";i:17;s:19:\"catalog/product_inv\";i:18;s:23:\"catalog/product_mannage\";i:19;s:21:\"catalog/product_price\";i:20;s:26:\"catalog/product_skubarcode\";i:21;s:17:\"catalog/recurring\";i:22;s:14:\"catalog/review\";i:23;s:11:\"catalog/sku\";i:24;s:16:\"catalog/supplier\";i:25;s:18:\"common/column_left\";i:26;s:18:\"common/filemanager\";i:27;s:11:\"common/menu\";i:28;s:14:\"common/profile\";i:29;s:12:\"common/stats\";i:30;s:18:\"dashboard/activity\";i:31;s:15:\"dashboard/chart\";i:32;s:18:\"dashboard/customer\";i:33;s:13:\"dashboard/map\";i:34;s:16:\"dashboard/online\";i:35;s:15:\"dashboard/order\";i:36;s:16:\"dashboard/recent\";i:37;s:14:\"dashboard/sale\";i:38;s:13:\"design/banner\";i:39;s:13:\"design/layout\";i:40;s:14:\"extension/feed\";i:41;s:19:\"extension/installer\";i:42;s:22:\"extension/modification\";i:43;s:16:\"extension/module\";i:44;s:17:\"extension/openbay\";i:45;s:17:\"extension/payment\";i:46;s:18:\"extension/shipping\";i:47;s:15:\"extension/total\";i:48;s:16:\"feed/google_base\";i:49;s:19:\"feed/google_sitemap\";i:50;s:15:\"feed/openbaypro\";i:51;s:20:\"localisation/country\";i:52;s:21:\"localisation/currency\";i:53;s:21:\"localisation/geo_zone\";i:54;s:21:\"localisation/language\";i:55;s:25:\"localisation/length_class\";i:56;s:21:\"localisation/location\";i:57;s:25:\"localisation/order_status\";i:58;s:26:\"localisation/return_action\";i:59;s:26:\"localisation/return_reason\";i:60;s:26:\"localisation/return_status\";i:61;s:25:\"localisation/stock_status\";i:62;s:22:\"localisation/tax_class\";i:63;s:21:\"localisation/tax_rate\";i:64;s:25:\"localisation/weight_class\";i:65;s:17:\"localisation/zone\";i:66;s:25:\"logistic/driver_face_list\";i:67;s:23:\"logistic/logistic_allot\";i:68;s:24:\"logistic/logistic_allot2\";i:69;s:29:\"logistic/logistic_allot_order\";i:70;s:27:\"logistic/logistic_allot_van\";i:71;s:22:\"logistic/logistic_info\";i:72;s:19:\"marketing/affiliate\";i:73;s:14:\"marketing/area\";i:74;s:12:\"marketing/bd\";i:75;s:23:\"marketing/bd_statistics\";i:76;s:17:\"marketing/contact\";i:77;s:16:\"marketing/coupon\";i:78;s:25:\"marketing/index_promotion\";i:79;s:19:\"marketing/marketing\";i:80;s:23:\"marketing/order_instead\";i:81;s:24:\"marketing/plan_promotion\";i:82;s:19:\"marketing/promotion\";i:83;s:22:\"marketing/smspin_query\";i:84;s:14:\"module/account\";i:85;s:16:\"module/affiliate\";i:86;s:20:\"module/amazon_button\";i:87;s:13:\"module/banner\";i:88;s:17:\"module/bestseller\";i:89;s:15:\"module/carousel\";i:90;s:15:\"module/category\";i:91;s:19:\"module/ebay_listing\";i:92;s:15:\"module/featured\";i:93;s:13:\"module/filter\";i:94;s:22:\"module/google_hangouts\";i:95;s:11:\"module/html\";i:96;s:18:\"module/information\";i:97;s:13:\"module/latest\";i:98;s:16:\"module/pp_button\";i:99;s:15:\"module/pp_login\";i:100;s:16:\"module/slideshow\";i:101;s:14:\"module/special\";i:102;s:12:\"module/store\";i:103;s:17:\"notice/homenotice\";i:104;s:14:\"openbay/amazon\";i:105;s:22:\"openbay/amazon_listing\";i:106;s:22:\"openbay/amazon_product\";i:107;s:16:\"openbay/amazonus\";i:108;s:24:\"openbay/amazonus_listing\";i:109;s:24:\"openbay/amazonus_product\";i:110;s:12:\"openbay/ebay\";i:111;s:20:\"openbay/ebay_profile\";i:112;s:21:\"openbay/ebay_template\";i:113;s:12:\"openbay/etsy\";i:114;s:20:\"openbay/etsy_product\";i:115;s:21:\"openbay/etsy_shipping\";i:116;s:17:\"openbay/etsy_shop\";i:117;s:23:\"payment/amazon_checkout\";i:118;s:24:\"payment/authorizenet_aim\";i:119;s:24:\"payment/authorizenet_sim\";i:120;s:21:\"payment/bank_transfer\";i:121;s:22:\"payment/bluepay_hosted\";i:122;s:24:\"payment/bluepay_redirect\";i:123;s:14:\"payment/cheque\";i:124;s:11:\"payment/cod\";i:125;s:17:\"payment/firstdata\";i:126;s:24:\"payment/firstdata_remote\";i:127;s:21:\"payment/free_checkout\";i:128;s:22:\"payment/klarna_account\";i:129;s:22:\"payment/klarna_invoice\";i:130;s:14:\"payment/liqpay\";i:131;s:14:\"payment/nochex\";i:132;s:15:\"payment/paymate\";i:133;s:16:\"payment/paypoint\";i:134;s:13:\"payment/payza\";i:135;s:26:\"payment/perpetual_payments\";i:136;s:18:\"payment/pp_express\";i:137;s:18:\"payment/pp_payflow\";i:138;s:25:\"payment/pp_payflow_iframe\";i:139;s:14:\"payment/pp_pro\";i:140;s:21:\"payment/pp_pro_iframe\";i:141;s:19:\"payment/pp_standard\";i:142;s:14:\"payment/realex\";i:143;s:21:\"payment/realex_remote\";i:144;s:22:\"payment/sagepay_direct\";i:145;s:22:\"payment/sagepay_server\";i:146;s:18:\"payment/sagepay_us\";i:147;s:24:\"payment/securetrading_pp\";i:148;s:24:\"payment/securetrading_ws\";i:149;s:14:\"payment/skrill\";i:150;s:19:\"payment/twocheckout\";i:151;s:28:\"payment/web_payment_software\";i:152;s:16:\"payment/worldpay\";i:153;s:23:\"purchase/inventory_plan\";i:154;s:21:\"purchase/pre_purchase\";i:155;s:28:\"purchase/pre_purchase_adjust\";i:156;s:28:\"purchase/pre_purchase_upload\";i:157;s:17:\"purchase/purchase\";i:158;s:34:\"purchase/warehouse_allocation_note\";i:159;s:16:\"report/affiliate\";i:160;s:25:\"report/affiliate_activity\";i:161;s:22:\"report/affiliate_login\";i:162;s:16:\"report/bd_coupon\";i:163;s:25:\"report/billing_statements\";i:164;s:19:\"report/check_single\";i:165;s:24:\"report/customer_activity\";i:166;s:22:\"report/customer_credit\";i:167;s:24:\"report/customer_feedback\";i:168;s:20:\"report/customer_info\";i:169;s:21:\"report/customer_login\";i:170;s:22:\"report/customer_online\";i:171;s:21:\"report/customer_order\";i:172;s:22:\"report/customer_reward\";i:173;s:19:\"report/driver_total\";i:174;s:18:\"report/inv_mi_cold\";i:175;s:25:\"report/inv_mi_cold_search\";i:176;s:22:\"report/logistic_driver\";i:177;s:19:\"report/logistic_fee\";i:178;s:20:\"report/logistic_info\";i:179;s:16:\"report/marketing\";i:180;s:17:\"report/order_info\";i:181;s:18:\"report/out_of_info\";i:182;s:21:\"report/product_margin\";i:183;s:26:\"report/product_particulars\";i:184;s:24:\"report/product_promotion\";i:185;s:24:\"report/product_purchased\";i:186;s:19:\"report/product_sale\";i:187;s:21:\"report/product_viewed\";i:188;s:22:\"report/purchase_detail\";i:189;s:21:\"report/purchase_order\";i:190;s:21:\"report/purchase_query\";i:191;s:22:\"report/purchase_report\";i:192;s:20:\"report/sale_bd_query\";i:193;s:18:\"report/sale_coupon\";i:194;s:27:\"report/sale_customer_active\";i:195;s:18:\"report/sale_margin\";i:196;s:17:\"report/sale_order\";i:197;s:18:\"report/sale_return\";i:198;s:20:\"report/sale_shipping\";i:199;s:16:\"report/sale_stat\";i:200;s:19:\"report/sale_station\";i:201;s:15:\"report/sale_tax\";i:202;s:21:\"report/short_confirms\";i:203;s:20:\"report/sorting_staff\";i:204;s:28:\"report/warehouse_check_order\";i:205;s:23:\"report/warehouse_margin\";i:206;s:17:\"sale/custom_field\";i:207;s:13:\"sale/customer\";i:208;s:20:\"sale/customer_ban_ip\";i:209;s:19:\"sale/customer_group\";i:210;s:22:\"sale/financial_confirm\";i:211;s:10:\"sale/order\";i:212;s:16:\"sale/order_audit\";i:213;s:20:\"sale/order_replenish\";i:214;s:17:\"sale/order_urgent\";i:215;s:14:\"sale/recurring\";i:216;s:11:\"sale/refund\";i:217;s:11:\"sale/return\";i:218;s:18:\"sale/return_adjust\";i:219;s:17:\"sale/return_apply\";i:220;s:25:\"sale/return_specification\";i:221;s:12:\"sale/voucher\";i:222;s:18:\"sale/voucher_theme\";i:223;s:15:\"setting/setting\";i:224;s:13:\"setting/store\";i:225;s:16:\"shipping/auspost\";i:226;s:17:\"shipping/citylink\";i:227;s:14:\"shipping/fedex\";i:228;s:13:\"shipping/flat\";i:229;s:13:\"shipping/free\";i:230;s:13:\"shipping/item\";i:231;s:23:\"shipping/parcelforce_48\";i:232;s:15:\"shipping/pickup\";i:233;s:19:\"shipping/royal_mail\";i:234;s:12:\"shipping/ups\";i:235;s:13:\"shipping/usps\";i:236;s:15:\"shipping/weight\";i:237;s:24:\"station/accounting_cycle\";i:238;s:15:\"station/station\";i:239;s:11:\"tool/backup\";i:240;s:14:\"tool/error_log\";i:241;s:11:\"tool/upload\";i:242;s:12:\"total/coupon\";i:243;s:12:\"total/credit\";i:244;s:14:\"total/handling\";i:245;s:16:\"total/klarna_fee\";i:246;s:19:\"total/low_order_fee\";i:247;s:12:\"total/reward\";i:248;s:14:\"total/shipping\";i:249;s:15:\"total/sub_total\";i:250;s:9:\"total/tax\";i:251;s:11:\"total/total\";i:252;s:13:\"total/voucher\";i:253;s:15:\"user/accounting\";i:254;s:8:\"user/api\";i:255;s:14:\"user/container\";i:256;s:16:\"user/container_w\";i:257;s:19:\"user/return_product\";i:258;s:15:\"user/sort_error\";i:259;s:9:\"user/user\";i:260;s:20:\"user/user_permission\";i:261;s:11:\"user/w_user\";i:262;s:37:\"user/warehouse_allocation_note_leader\";}s:6:\"modify\";a:263:{i:0;s:19:\"accounting/exchange\";i:1;s:25:\"accounting/return_deposit\";i:2;s:11:\"agent/agent\";i:3;s:16:\"catalog/activity\";i:4;s:17:\"catalog/attribute\";i:5;s:23:\"catalog/attribute_group\";i:6;s:15:\"catalog/balance\";i:7;s:16:\"catalog/category\";i:8;s:16:\"catalog/download\";i:9;s:14:\"catalog/filter\";i:10;s:19:\"catalog/information\";i:11;s:17:\"catalog/inventory\";i:12;s:20:\"catalog/manufacturer\";i:13;s:14:\"catalog/option\";i:14;s:13:\"catalog/price\";i:15;s:15:\"catalog/product\";i:16;s:27:\"catalog/product_application\";i:17;s:19:\"catalog/product_inv\";i:18;s:23:\"catalog/product_mannage\";i:19;s:21:\"catalog/product_price\";i:20;s:26:\"catalog/product_skubarcode\";i:21;s:17:\"catalog/recurring\";i:22;s:14:\"catalog/review\";i:23;s:11:\"catalog/sku\";i:24;s:16:\"catalog/supplier\";i:25;s:18:\"common/column_left\";i:26;s:18:\"common/filemanager\";i:27;s:11:\"common/menu\";i:28;s:14:\"common/profile\";i:29;s:12:\"common/stats\";i:30;s:18:\"dashboard/activity\";i:31;s:15:\"dashboard/chart\";i:32;s:18:\"dashboard/customer\";i:33;s:13:\"dashboard/map\";i:34;s:16:\"dashboard/online\";i:35;s:15:\"dashboard/order\";i:36;s:16:\"dashboard/recent\";i:37;s:14:\"dashboard/sale\";i:38;s:13:\"design/banner\";i:39;s:13:\"design/layout\";i:40;s:14:\"extension/feed\";i:41;s:19:\"extension/installer\";i:42;s:22:\"extension/modification\";i:43;s:16:\"extension/module\";i:44;s:17:\"extension/openbay\";i:45;s:17:\"extension/payment\";i:46;s:18:\"extension/shipping\";i:47;s:15:\"extension/total\";i:48;s:16:\"feed/google_base\";i:49;s:19:\"feed/google_sitemap\";i:50;s:15:\"feed/openbaypro\";i:51;s:20:\"localisation/country\";i:52;s:21:\"localisation/currency\";i:53;s:21:\"localisation/geo_zone\";i:54;s:21:\"localisation/language\";i:55;s:25:\"localisation/length_class\";i:56;s:21:\"localisation/location\";i:57;s:25:\"localisation/order_status\";i:58;s:26:\"localisation/return_action\";i:59;s:26:\"localisation/return_reason\";i:60;s:26:\"localisation/return_status\";i:61;s:25:\"localisation/stock_status\";i:62;s:22:\"localisation/tax_class\";i:63;s:21:\"localisation/tax_rate\";i:64;s:25:\"localisation/weight_class\";i:65;s:17:\"localisation/zone\";i:66;s:25:\"logistic/driver_face_list\";i:67;s:23:\"logistic/logistic_allot\";i:68;s:24:\"logistic/logistic_allot2\";i:69;s:29:\"logistic/logistic_allot_order\";i:70;s:27:\"logistic/logistic_allot_van\";i:71;s:22:\"logistic/logistic_info\";i:72;s:19:\"marketing/affiliate\";i:73;s:14:\"marketing/area\";i:74;s:12:\"marketing/bd\";i:75;s:23:\"marketing/bd_statistics\";i:76;s:17:\"marketing/contact\";i:77;s:16:\"marketing/coupon\";i:78;s:25:\"marketing/index_promotion\";i:79;s:19:\"marketing/marketing\";i:80;s:23:\"marketing/order_instead\";i:81;s:24:\"marketing/plan_promotion\";i:82;s:19:\"marketing/promotion\";i:83;s:22:\"marketing/smspin_query\";i:84;s:14:\"module/account\";i:85;s:16:\"module/affiliate\";i:86;s:20:\"module/amazon_button\";i:87;s:13:\"module/banner\";i:88;s:17:\"module/bestseller\";i:89;s:15:\"module/carousel\";i:90;s:15:\"module/category\";i:91;s:19:\"module/ebay_listing\";i:92;s:15:\"module/featured\";i:93;s:13:\"module/filter\";i:94;s:22:\"module/google_hangouts\";i:95;s:11:\"module/html\";i:96;s:18:\"module/information\";i:97;s:13:\"module/latest\";i:98;s:16:\"module/pp_button\";i:99;s:15:\"module/pp_login\";i:100;s:16:\"module/slideshow\";i:101;s:14:\"module/special\";i:102;s:12:\"module/store\";i:103;s:17:\"notice/homenotice\";i:104;s:14:\"openbay/amazon\";i:105;s:22:\"openbay/amazon_listing\";i:106;s:22:\"openbay/amazon_product\";i:107;s:16:\"openbay/amazonus\";i:108;s:24:\"openbay/amazonus_listing\";i:109;s:24:\"openbay/amazonus_product\";i:110;s:12:\"openbay/ebay\";i:111;s:20:\"openbay/ebay_profile\";i:112;s:21:\"openbay/ebay_template\";i:113;s:12:\"openbay/etsy\";i:114;s:20:\"openbay/etsy_product\";i:115;s:21:\"openbay/etsy_shipping\";i:116;s:17:\"openbay/etsy_shop\";i:117;s:23:\"payment/amazon_checkout\";i:118;s:24:\"payment/authorizenet_aim\";i:119;s:24:\"payment/authorizenet_sim\";i:120;s:21:\"payment/bank_transfer\";i:121;s:22:\"payment/bluepay_hosted\";i:122;s:24:\"payment/bluepay_redirect\";i:123;s:14:\"payment/cheque\";i:124;s:11:\"payment/cod\";i:125;s:17:\"payment/firstdata\";i:126;s:24:\"payment/firstdata_remote\";i:127;s:21:\"payment/free_checkout\";i:128;s:22:\"payment/klarna_account\";i:129;s:22:\"payment/klarna_invoice\";i:130;s:14:\"payment/liqpay\";i:131;s:14:\"payment/nochex\";i:132;s:15:\"payment/paymate\";i:133;s:16:\"payment/paypoint\";i:134;s:13:\"payment/payza\";i:135;s:26:\"payment/perpetual_payments\";i:136;s:18:\"payment/pp_express\";i:137;s:18:\"payment/pp_payflow\";i:138;s:25:\"payment/pp_payflow_iframe\";i:139;s:14:\"payment/pp_pro\";i:140;s:21:\"payment/pp_pro_iframe\";i:141;s:19:\"payment/pp_standard\";i:142;s:14:\"payment/realex\";i:143;s:21:\"payment/realex_remote\";i:144;s:22:\"payment/sagepay_direct\";i:145;s:22:\"payment/sagepay_server\";i:146;s:18:\"payment/sagepay_us\";i:147;s:24:\"payment/securetrading_pp\";i:148;s:24:\"payment/securetrading_ws\";i:149;s:14:\"payment/skrill\";i:150;s:19:\"payment/twocheckout\";i:151;s:28:\"payment/web_payment_software\";i:152;s:16:\"payment/worldpay\";i:153;s:23:\"purchase/inventory_plan\";i:154;s:21:\"purchase/pre_purchase\";i:155;s:28:\"purchase/pre_purchase_adjust\";i:156;s:28:\"purchase/pre_purchase_upload\";i:157;s:17:\"purchase/purchase\";i:158;s:34:\"purchase/warehouse_allocation_note\";i:159;s:16:\"report/affiliate\";i:160;s:25:\"report/affiliate_activity\";i:161;s:22:\"report/affiliate_login\";i:162;s:16:\"report/bd_coupon\";i:163;s:25:\"report/billing_statements\";i:164;s:19:\"report/check_single\";i:165;s:24:\"report/customer_activity\";i:166;s:22:\"report/customer_credit\";i:167;s:24:\"report/customer_feedback\";i:168;s:20:\"report/customer_info\";i:169;s:21:\"report/customer_login\";i:170;s:22:\"report/customer_online\";i:171;s:21:\"report/customer_order\";i:172;s:22:\"report/customer_reward\";i:173;s:19:\"report/driver_total\";i:174;s:18:\"report/inv_mi_cold\";i:175;s:25:\"report/inv_mi_cold_search\";i:176;s:22:\"report/logistic_driver\";i:177;s:19:\"report/logistic_fee\";i:178;s:20:\"report/logistic_info\";i:179;s:16:\"report/marketing\";i:180;s:17:\"report/order_info\";i:181;s:18:\"report/out_of_info\";i:182;s:21:\"report/product_margin\";i:183;s:26:\"report/product_particulars\";i:184;s:24:\"report/product_promotion\";i:185;s:24:\"report/product_purchased\";i:186;s:19:\"report/product_sale\";i:187;s:21:\"report/product_viewed\";i:188;s:22:\"report/purchase_detail\";i:189;s:21:\"report/purchase_order\";i:190;s:21:\"report/purchase_query\";i:191;s:22:\"report/purchase_report\";i:192;s:20:\"report/sale_bd_query\";i:193;s:18:\"report/sale_coupon\";i:194;s:27:\"report/sale_customer_active\";i:195;s:18:\"report/sale_margin\";i:196;s:17:\"report/sale_order\";i:197;s:18:\"report/sale_return\";i:198;s:20:\"report/sale_shipping\";i:199;s:16:\"report/sale_stat\";i:200;s:19:\"report/sale_station\";i:201;s:15:\"report/sale_tax\";i:202;s:21:\"report/short_confirms\";i:203;s:20:\"report/sorting_staff\";i:204;s:28:\"report/warehouse_check_order\";i:205;s:23:\"report/warehouse_margin\";i:206;s:17:\"sale/custom_field\";i:207;s:13:\"sale/customer\";i:208;s:20:\"sale/customer_ban_ip\";i:209;s:19:\"sale/customer_group\";i:210;s:22:\"sale/financial_confirm\";i:211;s:10:\"sale/order\";i:212;s:16:\"sale/order_audit\";i:213;s:20:\"sale/order_replenish\";i:214;s:17:\"sale/order_urgent\";i:215;s:14:\"sale/recurring\";i:216;s:11:\"sale/refund\";i:217;s:11:\"sale/return\";i:218;s:18:\"sale/return_adjust\";i:219;s:17:\"sale/return_apply\";i:220;s:25:\"sale/return_specification\";i:221;s:12:\"sale/voucher\";i:222;s:18:\"sale/voucher_theme\";i:223;s:15:\"setting/setting\";i:224;s:13:\"setting/store\";i:225;s:16:\"shipping/auspost\";i:226;s:17:\"shipping/citylink\";i:227;s:14:\"shipping/fedex\";i:228;s:13:\"shipping/flat\";i:229;s:13:\"shipping/free\";i:230;s:13:\"shipping/item\";i:231;s:23:\"shipping/parcelforce_48\";i:232;s:15:\"shipping/pickup\";i:233;s:19:\"shipping/royal_mail\";i:234;s:12:\"shipping/ups\";i:235;s:13:\"shipping/usps\";i:236;s:15:\"shipping/weight\";i:237;s:24:\"station/accounting_cycle\";i:238;s:15:\"station/station\";i:239;s:11:\"tool/backup\";i:240;s:14:\"tool/error_log\";i:241;s:11:\"tool/upload\";i:242;s:12:\"total/coupon\";i:243;s:12:\"total/credit\";i:244;s:14:\"total/handling\";i:245;s:16:\"total/klarna_fee\";i:246;s:19:\"total/low_order_fee\";i:247;s:12:\"total/reward\";i:248;s:14:\"total/shipping\";i:249;s:15:\"total/sub_total\";i:250;s:9:\"total/tax\";i:251;s:11:\"total/total\";i:252;s:13:\"total/voucher\";i:253;s:15:\"user/accounting\";i:254;s:8:\"user/api\";i:255;s:14:\"user/container\";i:256;s:16:\"user/container_w\";i:257;s:19:\"user/return_product\";i:258;s:15:\"user/sort_error\";i:259;s:9:\"user/user\";i:260;s:20:\"user/user_permission\";i:261;s:11:\"user/w_user\";i:262;s:37:\"user/warehouse_allocation_note_leader\";}}';
//
////'{i:0;s:19:"accounting/exchange";i:1;s:25:"accounting/return_deposit";i:2;s:11:"agent/agent";i:3;s:16:"catalog/activity";i:4;s:17:"catalog/attribute";i:5;s:23:"catalog/attribute_group";i:6;s:15:"catalog/balance";i:7;s:16:"catalog/category";i:8;s:16:"catalog/download";i:9;s:14:"catalog/filter";i:10;s:19:"catalog/information";i:11;s:17:"catalog/inventory";i:12;s:20:"catalog/manufacturer";i:13;s:14:"catalog/option";i:14;s:13:"catalog/price";i:15;s:15:"catalog/product";i:16;s:27:"catalog/product_application";i:17;s:19:"catalog/product_inv";i:18;s:23:"catalog/product_mannage";i:19;s:21:"catalog/product_price";i:20;s:26:"catalog/product_skubarcode";i:21;s:17:"catalog/recurring";i:22;s:14:"catalog/review";i:23;s:11:"catalog/sku";i:24;s:16:"catalog/supplier";i:25;s:18:"common/column_left";i:26;s:18:"common/filemanager";i:27;s:11:"common/menu";i:28;s:14:"common/profile";i:29;s:12:"common/stats";i:30;s:18:"dashboard/activity";i:31;s:15:"dashboard/chart";i:32;s:18:"dashboard/customer";i:33;s:13:"dashboard/map";i:34;s:16:"dashboard/online";i:35;s:15:"dashboard/order";i:36;s:16:"dashboard/recent";i:37;s:14:"dashboard/sale";i:38;s:13:"design/banner";i:39;s:13:"design/layout";i:40;s:14:"extension/feed";i:41;s:19:"extension/installer";i:42;s:22:"extension/modification";i:43;s:16:"extension/module";i:44;s:17:"extension/openbay";i:45;s:17:"extension/payment";i:46;s:18:"extension/shipping";i:47;s:15:"extension/total";i:48;s:16:"feed/google_base";i:49;s:19:"feed/google_sitemap";i:50;s:15:"feed/openbaypro";i:51;s:20:"localisation/country";i:52;s:21:"localisation/currency";i:53;s:21:"localisation/geo_zone";i:54;s:21:"localisation/language";i:55;s:25:"localisation/length_class";i:56;s:21:"localisation/location";i:57;s:25:"localisation/order_status";i:58;s:26:"localisation/return_action";i:59;s:26:"localisation/return_reason";i:60;s:26:"localisation/return_status";i:61;s:25:"localisation/stock_status";i:62;s:22:"localisation/tax_class";i:63;s:21:"localisation/tax_rate";i:64;s:25:"localisation/weight_class";i:65;s:17:"localisation/zone";i:66;s:25:"logistic/driver_face_list";i:67;s:23:"logistic/logistic_allot";i:68;s:24:"logistic/logistic_allot2";i:69;s:29:"logistic/logistic_allot_order";i:70;s:27:"logistic/logistic_allot_van";i:71;s:22:"logistic/logistic_info";i:72;s:19:"marketing/affiliate";i:73;s:14:"marketing/area";i:74;s:12:"marketing/bd";i:75;s:23:"marketing/bd_statistics";i:76;s:17:"marketing/contact";i:77;s:16:"marketing/coupon";i:78;s:25:"marketing/index_promotion";i:79;s:19:"marketing/marketing";i:80;s:23:"marketing/order_instead";i:81;s:24:"marketing/plan_promotion";i:82;s:19:"marketing/promotion";i:83;s:22:"marketing/smspin_query";i:84;s:14:"module/account";i:85;s:16:"module/affiliate";i:86;s:20:"module/amazon_button";i:87;s:13:"module/banner";i:88;s:17:"module/bestseller";i:89;s:15:"module/carousel";i:90;s:15:"module/category";i:91;s:19:"module/ebay_listing";i:92;s:15:"module/featured";i:93;s:13:"module/filter";i:94;s:22:"module/google_hangouts";i:95;s:11:"module/html";i:96;s:18:"module/information";i:97;s:13:"module/latest";i:98;s:16:"module/pp_button";i:99;s:15:"module/pp_login";i:100;s:16:"module/slideshow";i:101;s:14:"module/special";i:102;s:12:"module/store";i:103;s:17:"notice/homenotice";i:104;s:14:"openbay/amazon";i:105;s:22:"openbay/amazon_listing";i:106;s:22:"openbay/amazon_product";i:107;s:16:"openbay/amazonus";i:108;s:24:"openbay/amazonus_listing";i:109;s:24:"openbay/amazonus_product";i:110;s:12:"openbay/ebay";i:111;s:20:"openbay/ebay_profile";i:112;s:21:"openbay/ebay_template";i:113;s:12:"openbay/etsy";i:114;s:20:"openbay/etsy_product";i:115;s:21:"openbay/etsy_shipping";i:116;s:17:"openbay/etsy_shop";i:117;s:23:"payment/amazon_checkout";i:118;s:24:"payment/authorizenet_aim";i:119;s:24:"payment/authorizenet_sim";i:120;s:21:"payment/bank_transfer";i:121;s:22:"payment/bluepay_hosted";i:122;s:24:"payment/bluepay_redirect";i:123;s:14:"payment/cheque";i:124;s:11:"payment/cod";i:125;s:17:"payment/firstdata";i:126;s:24:"payment/firstdata_remote";i:127;s:21:"payment/free_checkout";i:128;s:22:"payment/klarna_account";i:129;s:22:"payment/klarna_invoice";i:130;s:14:"payment/liqpay";i:131;s:14:"payment/nochex";i:132;s:15:"payment/paymate";i:133;s:16:"payment/paypoint";i:134;s:13:"payment/payza";i:135;s:26:"payment/perpetual_payments";i:136;s:18:"payment/pp_express";i:137;s:18:"payment/pp_payflow";i:138;s:25:"payment/pp_payflow_iframe";i:139;s:14:"payment/pp_pro";i:140;s:21:"payment/pp_pro_iframe";i:141;s:19:"payment/pp_standard";i:142;s:14:"payment/realex";i:143;s:21:"payment/realex_remote";i:144;s:22:"payment/sagepay_direct";i:145;s:22:"payment/sagepay_server";i:146;s:18:"payment/sagepay_us";i:147;s:24:"payment/securetrading_pp";i:148;s:24:"payment/securetrading_ws";i:149;s:14:"payment/skrill";i:150;s:19:"payment/twocheckout";i:151;s:28:"payment/web_payment_software";i:152;s:16:"payment/worldpay";i:153;s:23:"purchase/inventory_plan";i:154;s:21:"purchase/pre_purchase";i:155;s:28:"purchase/pre_purchase_adjust";i:156;s:28:"purchase/pre_purchase_upload";i:157;s:17:"purchase/purchase";i:158;s:34:"purchase/warehouse_allocation_note";i:159;s:16:"report/affiliate";i:160;s:25:"report/affiliate_activity";i:161;s:22:"report/affiliate_login";i:162;s:16:"report/bd_coupon";i:163;s:25:"report/billing_statements";i:164;s:19:"report/check_single";i:165;s:24:"report/customer_activity";i:166;s:22:"report/customer_credit";i:167;s:24:"report/customer_feedback";i:168;s:20:"report/customer_info";i:169;s:21:"report/customer_login";i:170;s:22:"report/customer_online";i:171;s:21:"report/customer_order";i:172;s:22:"report/customer_reward";i:173;s:19:"report/driver_total";i:174;s:18:"report/inv_mi_cold";i:175;s:25:"report/inv_mi_cold_search";i:176;s:22:"report/logistic_driver";i:177;s:19:"report/logistic_fee";i:178;s:20:"report/logistic_info";i:179;s:16:"report/marketing";i:180;s:17:"report/order_info";i:181;s:18:"report/out_of_info";i:182;s:21:"report/product_margin";i:183;s:26:"report/product_particulars";i:184;s:24:"report/product_promotion";i:185;s:24:"report/product_purchased";i:186;s:19:"report/product_sale";i:187;s:21:"report/product_viewed";i:188;s:22:"report/purchase_detail";i:189;s:21:"report/purchase_order";i:190;s:21:"report/purchase_query";i:191;s:22:"report/purchase_report";i:192;s:20:"report/sale_bd_query";i:193;s:18:"report/sale_coupon";i:194;s:27:"report/sale_customer_active";i:195;s:18:"report/sale_margin";i:196;s:17:"report/sale_order";i:197;s:18:"report/sale_return";i:198;s:20:"report/sale_shipping";i:199;s:16:"report/sale_stat";i:200;s:19:"report/sale_station";i:201;s:15:"report/sale_tax";i:202;s:21:"report/short_confirms";i:203;s:20:"report/sorting_staff";i:204;s:28:"report/warehouse_check_order";i:205;s:23:"report/warehouse_margin";i:206;s:17:"sale/custom_field";i:207;s:13:"sale/customer";i:208;s:20:"sale/customer_ban_ip";i:209;s:19:"sale/customer_group";i:210;s:22:"sale/financial_confirm";i:211;s:10:"sale/order";i:212;s:16:"sale/order_audit";i:213;s:20:"sale/order_replenish";i:214;s:17:"sale/order_urgent";i:215;s:14:"sale/recurring";i:216;s:11:"sale/refund";i:217;s:11:"sale/return";i:218;s:18:"sale/return_adjust";i:219;s:17:"sale/return_apply";i:220;s:25:"sale/return_specification";i:221;s:12:"sale/voucher";i:222;s:18:"sale/voucher_theme";i:223;s:15:"setting/setting";i:224;s:13:"setting/store";i:225;s:16:"shipping/auspost";i:226;s:17:"shipping/citylink";i:227;s:14:"shipping/fedex";i:228;s:13:"shipping/flat";i:229;s:13:"shipping/free";i:230;s:13:"shipping/item";i:231;s:23:"shipping/parcelforce_48";i:232;s:15:"shipping/pickup";i:233;s:19:"shipping/royal_mail";i:234;s:12:"shipping/ups";i:235;s:13:"shipping/usps";i:236;s:15:"shipping/weight";i:237;s:24:"station/accounting_cycle";i:238;s:15:"station/station";i:239;s:11:"tool/backup";i:240;s:14:"tool/error_log";i:241;s:11:"tool/upload";i:242;s:12:"total/coupon";i:243;s:12:"total/credit";i:244;s:14:"total/handling";i:245;s:16:"total/klarna_fee";i:246;s:19:"total/low_order_fee";i:247;s:12:"total/reward";i:248;s:14:"total/shipping";i:249;s:15:"total/sub_total";i:250;s:9:"total/tax";i:251;s:11:"total/total";i:252;s:13:"total/voucher";i:253;s:15:"user/accounting";i:254;s:8:"user/api";i:255;s:14:"user/container";i:256;s:16:"user/container_w";i:257;s:19:"user/return_product";i:258;s:15:"user/sort_error";i:259;s:9:"user/user";i:260;s:20:"user/user_permission";i:261;s:11:"user/w_user";i:262;s:37:"user/warehouse_allocation_note_leader";}}';
//;
//$a =
////    serialize($c);
//'a:2:{s:6:"access";a:263:{i:0;s:19:"accounting/exchange";i:1;s:25:"accounting/return_deposit";i:2;s:11:"agent/agent";i:3;s:16:"catalog/activity";i:4;s:17:"catalog/attribute";i:5;s:23:"catalog/attribute_group";i:6;s:15:"catalog/balance";i:7;s:16:"catalog/category";i:8;s:16:"catalog/download";i:9;s:14:"catalog/filter";i:10;s:19:"catalog/information";i:11;s:17:"catalog/inventory";i:12;s:20:"catalog/manufacturer";i:13;s:14:"catalog/option";i:14;s:13:"catalog/price";i:15;s:15:"catalog/product";i:16;s:27:"catalog/product_application";i:17;s:19:"catalog/product_inv";i:18;s:23:"catalog/product_mannage";i:19;s:21:"catalog/product_price";i:20;s:26:"catalog/product_skubarcode";i:21;s:17:"catalog/recurring";i:22;s:14:"catalog/review";i:23;s:11:"catalog/sku";i:24;s:16:"catalog/supplier";i:25;s:18:"common/column_left";i:26;s:18:"common/filemanager";i:27;s:11:"common/menu";i:28;s:14:"common/profile";i:29;s:12:"common/stats";i:30;s:18:"dashboard/activity";i:31;s:15:"dashboard/chart";i:32;s:18:"dashboard/customer";i:33;s:13:"dashboard/map";i:34;s:16:"dashboard/online";i:35;s:15:"dashboard/order";i:36;s:16:"dashboard/recent";i:37;s:14:"dashboard/sale";i:38;s:13:"design/banner";i:39;s:13:"design/layout";i:40;s:14:"extension/feed";i:41;s:19:"extension/installer";i:42;s:22:"extension/modification";i:43;s:16:"extension/module";i:44;s:17:"extension/openbay";i:45;s:17:"extension/payment";i:46;s:18:"extension/shipping";i:47;s:15:"extension/total";i:48;s:16:"feed/google_base";i:49;s:19:"feed/google_sitemap";i:50;s:15:"feed/openbaypro";i:51;s:20:"localisation/country";i:52;s:21:"localisation/currency";i:53;s:21:"localisation/geo_zone";i:54;s:21:"localisation/language";i:55;s:25:"localisation/length_class";i:56;s:21:"localisation/location";i:57;s:25:"localisation/order_status";i:58;s:26:"localisation/return_action";i:59;s:26:"localisation/return_reason";i:60;s:26:"localisation/return_status";i:61;s:25:"localisation/stock_status";i:62;s:22:"localisation/tax_class";i:63;s:21:"localisation/tax_rate";i:64;s:25:"localisation/weight_class";i:65;s:17:"localisation/zone";i:66;s:25:"logistic/driver_face_list";i:67;s:23:"logistic/logistic_allot";i:68;s:24:"logistic/logistic_allot2";i:69;s:29:"logistic/logistic_allot_order";i:70;s:27:"logistic/logistic_allot_van";i:71;s:22:"logistic/logistic_info";i:72;s:19:"marketing/affiliate";i:73;s:14:"marketing/area";i:74;s:12:"marketing/bd";i:75;s:23:"marketing/bd_statistics";i:76;s:17:"marketing/contact";i:77;s:16:"marketing/coupon";i:78;s:25:"marketing/index_promotion";i:79;s:19:"marketing/marketing";i:80;s:23:"marketing/order_instead";i:81;s:24:"marketing/plan_promotion";i:82;s:19:"marketing/promotion";i:83;s:22:"marketing/smspin_query";i:84;s:14:"module/account";i:85;s:16:"module/affiliate";i:86;s:20:"module/amazon_button";i:87;s:13:"module/banner";i:88;s:17:"module/bestseller";i:89;s:15:"module/carousel";i:90;s:15:"module/category";i:91;s:19:"module/ebay_listing";i:92;s:15:"module/featured";i:93;s:13:"module/filter";i:94;s:22:"module/google_hangouts";i:95;s:11:"module/html";i:96;s:18:"module/information";i:97;s:13:"module/latest";i:98;s:16:"module/pp_button";i:99;s:15:"module/pp_login";i:100;s:16:"module/slideshow";i:101;s:14:"module/special";i:102;s:12:"module/store";i:103;s:17:"notice/homenotice";i:104;s:14:"openbay/amazon";i:105;s:22:"openbay/amazon_listing";i:106;s:22:"openbay/amazon_product";i:107;s:16:"openbay/amazonus";i:108;s:24:"openbay/amazonus_listing";i:109;s:24:"openbay/amazonus_product";i:110;s:12:"openbay/ebay";i:111;s:20:"openbay/ebay_profile";i:112;s:21:"openbay/ebay_template";i:113;s:12:"openbay/etsy";i:114;s:20:"openbay/etsy_product";i:115;s:21:"openbay/etsy_shipping";i:116;s:17:"openbay/etsy_shop";i:117;s:23:"payment/amazon_checkout";i:118;s:24:"payment/authorizenet_aim";i:119;s:24:"payment/authorizenet_sim";i:120;s:21:"payment/bank_transfer";i:121;s:22:"payment/bluepay_hosted";i:122;s:24:"payment/bluepay_redirect";i:123;s:14:"payment/cheque";i:124;s:11:"payment/cod";i:125;s:17:"payment/firstdata";i:126;s:24:"payment/firstdata_remote";i:127;s:21:"payment/free_checkout";i:128;s:22:"payment/klarna_account";i:129;s:22:"payment/klarna_invoice";i:130;s:14:"payment/liqpay";i:131;s:14:"payment/nochex";i:132;s:15:"payment/paymate";i:133;s:16:"payment/paypoint";i:134;s:13:"payment/payza";i:135;s:26:"payment/perpetual_payments";i:136;s:18:"payment/pp_express";i:137;s:18:"payment/pp_payflow";i:138;s:25:"payment/pp_payflow_iframe";i:139;s:14:"payment/pp_pro";i:140;s:21:"payment/pp_pro_iframe";i:141;s:19:"payment/pp_standard";i:142;s:14:"payment/realex";i:143;s:21:"payment/realex_remote";i:144;s:22:"payment/sagepay_direct";i:145;s:22:"payment/sagepay_server";i:146;s:18:"payment/sagepay_us";i:147;s:24:"payment/securetrading_pp";i:148;s:24:"payment/securetrading_ws";i:149;s:14:"payment/skrill";i:150;s:19:"payment/twocheckout";i:151;s:28:"payment/web_payment_software";i:152;s:16:"payment/worldpay";i:153;s:23:"purchase/inventory_plan";i:154;s:21:"purchase/pre_purchase";i:155;s:28:"purchase/pre_purchase_adjust";i:156;s:28:"purchase/pre_purchase_upload";i:157;s:17:"purchase/purchase";i:158;s:34:"purchase/warehouse_allocation_note";i:159;s:16:"report/affiliate";i:160;s:25:"report/affiliate_activity";i:161;s:22:"report/affiliate_login";i:162;s:16:"report/bd_coupon";i:163;s:25:"report/billing_statements";i:164;s:19:"report/check_single";i:165;s:24:"report/customer_activity";i:166;s:22:"report/customer_credit";i:167;s:24:"report/customer_feedback";i:168;s:20:"report/customer_info";i:169;s:21:"report/customer_login";i:170;s:22:"report/customer_online";i:171;s:21:"report/customer_order";i:172;s:22:"report/customer_reward";i:173;s:19:"report/driver_total";i:174;s:18:"report/inv_mi_cold";i:175;s:25:"report/inv_mi_cold_search";i:176;s:22:"report/logistic_driver";i:177;s:19:"report/logistic_fee";i:178;s:20:"report/logistic_info";i:179;s:16:"report/marketing";i:180;s:17:"report/order_info";i:181;s:18:"report/out_of_info";i:182;s:21:"report/product_margin";i:183;s:26:"report/product_particulars";i:184;s:24:"report/product_promotion";i:185;s:24:"report/product_purchased";i:186;s:19:"report/product_sale";i:187;s:21:"report/product_viewed";i:188;s:22:"report/purchase_detail";i:189;s:21:"report/purchase_order";i:190;s:21:"report/purchase_query";i:191;s:22:"report/purchase_report";i:192;s:20:"report/sale_bd_query";i:193;s:18:"report/sale_coupon";i:194;s:27:"report/sale_customer_active";i:195;s:18:"report/sale_margin";i:196;s:17:"report/sale_order";i:197;s:18:"report/sale_return";i:198;s:20:"report/sale_shipping";i:199;s:16:"report/sale_stat";i:200;s:19:"report/sale_station";i:201;s:15:"report/sale_tax";i:202;s:21:"report/short_confirms";i:203;s:20:"report/sorting_staff";i:204;s:28:"report/warehouse_check_order";i:205;s:23:"report/warehouse_margin";i:206;s:17:"sale/custom_field";i:207;s:13:"sale/customer";i:208;s:20:"sale/customer_ban_ip";i:209;s:19:"sale/customer_group";i:210;s:22:"sale/financial_confirm";i:211;s:10:"sale/order";i:212;s:16:"sale/order_audit";i:213;s:20:"sale/order_replenish";i:214;s:17:"sale/order_urgent";i:215;s:14:"sale/recurring";i:216;s:11:"sale/refund";i:217;s:11:"sale/return";i:218;s:18:"sale/return_adjust";i:219;s:17:"sale/return_apply";i:220;s:25:"sale/return_specification";i:221;s:12:"sale/voucher";i:222;s:18:"sale/voucher_theme";i:223;s:15:"setting/setting";i:224;s:13:"setting/store";i:225;s:16:"shipping/auspost";i:226;s:17:"shipping/citylink";i:227;s:14:"shipping/fedex";i:228;s:13:"shipping/flat";i:229;s:13:"shipping/free";i:230;s:13:"shipping/item";i:231;s:23:"shipping/parcelforce_48";i:232;s:15:"shipping/pickup";i:233;s:19:"shipping/royal_mail";i:234;s:12:"shipping/ups";i:235;s:13:"shipping/usps";i:236;s:15:"shipping/weight";i:237;s:24:"station/accounting_cycle";i:238;s:15:"station/station";i:239;s:11:"tool/backup";i:240;s:14:"tool/error_log";i:241;s:11:"tool/upload";i:242;s:12:"total/coupon";i:243;s:12:"total/credit";i:244;s:14:"total/handling";i:245;s:16:"total/klarna_fee";i:246;s:19:"total/low_order_fee";i:247;s:12:"total/reward";i:248;s:14:"total/shipping";i:249;s:15:"total/sub_total";i:250;s:9:"total/tax";i:251;s:11:"total/total";i:252;s:13:"total/voucher";i:253;s:15:"user/accounting";i:254;s:8:"user/api";i:255;s:14:"user/container";i:256;s:16:"user/container_w";i:257;s:19:"user/return_product";i:258;s:15:"user/sort_error";i:259;s:9:"user/user";i:260;s:20:"user/user_permission";i:261;s:11:"user/w_user";i:262;s:37:"user/warehouse_allocation_note_leader";}
//s:6:"modify";a:263:{i:0;s:19:"accounting/exchange";i:1;s:25:"accounting/return_deposit";i:2;s:11:"agent/agent";i:3;s:16:"catalog/activity";i:4;s:17:"catalog/attribute";i:5;s:23:"catalog/attribute_group";i:6;s:15:"catalog/balance";i:7;s:16:"catalog/category";i:8;s:16:"catalog/download";i:9;s:14:"catalog/filter";i:10;s:19:"catalog/information";i:11;s:17:"catalog/inventory";i:12;s:20:"catalog/manufacturer";i:13;s:14:"catalog/option";i:14;s:13:"catalog/price";i:15;s:15:"catalog/product";i:16;s:27:"catalog/product_application";i:17;s:19:"catalog/product_inv";i:18;s:23:"catalog/product_mannage";i:19;s:21:"catalog/product_price";i:20;s:26:"catalog/product_skubarcode";i:21;s:17:"catalog/recurring";i:22;s:14:"catalog/review";i:23;s:11:"catalog/sku";i:24;s:16:"catalog/supplier";i:25;s:18:"common/column_left";i:26;s:18:"common/filemanager";i:27;s:11:"common/menu";i:28;s:14:"common/profile";i:29;s:12:"common/stats";i:30;s:18:"dashboard/activity";i:31;s:15:"dashboard/chart";i:32;s:18:"dashboard/customer";i:33;s:13:"dashboard/map";i:34;s:16:"dashboard/online";i:35;s:15:"dashboard/order";i:36;s:16:"dashboard/recent";i:37;s:14:"dashboard/sale";i:38;s:13:"design/banner";i:39;s:13:"design/layout";i:40;s:14:"extension/feed";i:41;s:19:"extension/installer";i:42;s:22:"extension/modification";i:43;s:16:"extension/module";i:44;s:17:"extension/openbay";i:45;s:17:"extension/payment";i:46;s:18:"extension/shipping";i:47;s:15:"extension/total";i:48;s:16:"feed/google_base";i:49;s:19:"feed/google_sitemap";i:50;s:15:"feed/openbaypro";i:51;s:20:"localisation/country";i:52;s:21:"localisation/currency";i:53;s:21:"localisation/geo_zone";i:54;s:21:"localisation/language";i:55;s:25:"localisation/length_class";i:56;s:21:"localisation/location";i:57;s:25:"localisation/order_status";i:58;s:26:"localisation/return_action";i:59;s:26:"localisation/return_reason";i:60;s:26:"localisation/return_status";i:61;s:25:"localisation/stock_status";i:62;s:22:"localisation/tax_class";i:63;s:21:"localisation/tax_rate";i:64;s:25:"localisation/weight_class";i:65;s:17:"localisation/zone";i:66;s:25:"logistic/driver_face_list";i:67;s:23:"logistic/logistic_allot";i:68;s:24:"logistic/logistic_allot2";i:69;s:29:"logistic/logistic_allot_order";i:70;s:27:"logistic/logistic_allot_van";i:71;s:22:"logistic/logistic_info";i:72;s:19:"marketing/affiliate";i:73;s:14:"marketing/area";i:74;s:12:"marketing/bd";i:75;s:23:"marketing/bd_statistics";i:76;s:17:"marketing/contact";i:77;s:16:"marketing/coupon";i:78;s:25:"marketing/index_promotion";i:79;s:19:"marketing/marketing";i:80;s:23:"marketing/order_instead";i:81;s:24:"marketing/plan_promotion";i:82;s:19:"marketing/promotion";i:83;s:22:"marketing/smspin_query";i:84;s:14:"module/account";i:85;s:16:"module/affiliate";i:86;s:20:"module/amazon_button";i:87;s:13:"module/banner";i:88;s:17:"module/bestseller";i:89;s:15:"module/carousel";i:90;s:15:"module/category";i:91;s:19:"module/ebay_listing";i:92;s:15:"module/featured";i:93;s:13:"module/filter";i:94;s:22:"module/google_hangouts";i:95;s:11:"module/html";i:96;s:18:"module/information";i:97;s:13:"module/latest";i:98;s:16:"module/pp_button";i:99;s:15:"module/pp_login";i:100;s:16:"module/slideshow";i:101;s:14:"module/special";i:102;s:12:"module/store";i:103;s:17:"notice/homenotice";i:104;s:14:"openbay/amazon";i:105;s:22:"openbay/amazon_listing";i:106;s:22:"openbay/amazon_product";i:107;s:16:"openbay/amazonus";i:108;s:24:"openbay/amazonus_listing";i:109;s:24:"openbay/amazonus_product";i:110;s:12:"openbay/ebay";i:111;s:20:"openbay/ebay_profile";i:112;s:21:"openbay/ebay_template";i:113;s:12:"openbay/etsy";i:114;s:20:"openbay/etsy_product";i:115;s:21:"openbay/etsy_shipping";i:116;s:17:"openbay/etsy_shop";i:117;s:23:"payment/amazon_checkout";i:118;s:24:"payment/authorizenet_aim";i:119;s:24:"payment/authorizenet_sim";i:120;s:21:"payment/bank_transfer";i:121;s:22:"payment/bluepay_hosted";i:122;s:24:"payment/bluepay_redirect";i:123;s:14:"payment/cheque";i:124;s:11:"payment/cod";i:125;s:17:"payment/firstdata";i:126;s:24:"payment/firstdata_remote";i:127;s:21:"payment/free_checkout";i:128;s:22:"payment/klarna_account";i:129;s:22:"payment/klarna_invoice";i:130;s:14:"payment/liqpay";i:131;s:14:"payment/nochex";i:132;s:15:"payment/paymate";i:133;s:16:"payment/paypoint";i:134;s:13:"payment/payza";i:135;s:26:"payment/perpetual_payments";i:136;s:18:"payment/pp_express";i:137;s:18:"payment/pp_payflow";i:138;s:25:"payment/pp_payflow_iframe";i:139;s:14:"payment/pp_pro";i:140;s:21:"payment/pp_pro_iframe";i:141;s:19:"payment/pp_standard";i:142;s:14:"payment/realex";i:143;s:21:"payment/realex_remote";i:144;s:22:"payment/sagepay_direct";i:145;s:22:"payment/sagepay_server";i:146;s:18:"payment/sagepay_us";i:147;s:24:"payment/securetrading_pp";i:148;s:24:"payment/securetrading_ws";i:149;s:14:"payment/skrill";i:150;s:19:"payment/twocheckout";i:151;s:28:"payment/web_payment_software";i:152;s:16:"payment/worldpay";i:153;s:23:"purchase/inventory_plan";i:154;s:21:"purchase/pre_purchase";i:155;s:28:"purchase/pre_purchase_adjust";i:156;s:28:"purchase/pre_purchase_upload";i:157;s:17:"purchase/purchase";i:158;s:34:"purchase/warehouse_allocation_note";i:159;s:16:"report/affiliate";i:160;s:25:"report/affiliate_activity";i:161;s:22:"report/affiliate_login";i:162;s:16:"report/bd_coupon";i:163;s:25:"report/billing_statements";i:164;s:19:"report/check_single";i:165;s:24:"report/customer_activity";i:166;s:22:"report/customer_credit";i:167;s:24:"report/customer_feedback";i:168;s:20:"report/customer_info";i:169;s:21:"report/customer_login";i:170;s:22:"report/customer_online";i:171;s:21:"report/customer_order";i:172;s:22:"report/customer_reward";i:173;s:19:"report/driver_total";i:174;s:18:"report/inv_mi_cold";i:175;s:25:"report/inv_mi_cold_search";i:176;s:22:"report/logistic_driver";i:177;s:19:"report/logistic_fee";i:178;s:20:"report/logistic_info";i:179;s:16:"report/marketing";i:180;s:17:"report/order_info";i:181;s:18:"report/out_of_info";i:182;s:21:"report/product_margin";i:183;s:26:"report/product_particulars";i:184;s:24:"report/product_promotion";i:185;s:24:"report/product_purchased";i:186;s:19:"report/product_sale";i:187;s:21:"report/product_viewed";i:188;s:22:"report/purchase_detail";i:189;s:21:"report/purchase_order";i:190;s:21:"report/purchase_query";i:191;s:22:"report/purchase_report";i:192;s:20:"report/sale_bd_query";i:193;s:18:"report/sale_coupon";i:194;s:27:"report/sale_customer_active";i:195;s:18:"report/sale_margin";i:196;s:17:"report/sale_order";i:197;s:18:"report/sale_return";i:198;s:20:"report/sale_shipping";i:199;s:16:"report/sale_stat";i:200;s:19:"report/sale_station";i:201;s:15:"report/sale_tax";i:202;s:21:"report/short_confirms";i:203;s:20:"report/sorting_staff";i:204;s:28:"report/warehouse_check_order";i:205;s:23:"report/warehouse_margin";i:206;s:17:"sale/custom_field";i:207;s:13:"sale/customer";i:208;s:20:"sale/customer_ban_ip";i:209;s:19:"sale/customer_group";i:210;s:22:"sale/financial_confirm";i:211;s:10:"sale/order";i:212;s:16:"sale/order_audit";i:213;s:20:"sale/order_replenish";i:214;s:17:"sale/order_urgent";i:215;s:14:"sale/recurring";i:216;s:11:"sale/refund";i:217;s:11:"sale/return";i:218;s:18:"sale/return_adjust";i:219;s:17:"sale/return_apply";i:220;s:25:"sale/return_specification";i:221;s:12:"sale/voucher";i:222;s:18:"sale/voucher_theme";i:223;s:15:"setting/setting";i:224;s:13:"setting/store";i:225;s:16:"shipping/auspost";i:226;s:17:"shipping/citylink";i:227;s:14:"shipping/fedex";i:228;s:13:"shipping/flat";i:229;s:13:"shipping/free";i:230;s:13:"shipping/item";i:231;s:23:"shipping/parcelforce_48";i:232;s:15:"shipping/pickup";i:233;s:19:"shipping/royal_mail";i:234;s:12:"shipping/ups";i:235;s:13:"shipping/usps";i:236;s:15:"shipping/weight";i:237;s:24:"station/accounting_cycle";i:238;s:15:"station/station";i:239;s:11:"tool/backup";i:240;s:14:"tool/error_log";i:241;s:11:"tool/upload";i:242;s:12:"total/coupon";i:243;s:12:"total/credit";i:244;s:14:"total/handling";i:245;s:16:"total/klarna_fee";i:246;s:19:"total/low_order_fee";i:247;s:12:"total/reward";i:248;s:14:"total/shipping";i:249;s:15:"total/sub_total";i:250;s:9:"total/tax";i:251;s:11:"total/total";i:252;s:13:"total/voucher";i:253;s:15:"user/accounting";i:254;s:8:"user/api";i:255;s:14:"user/container";i:256;s:16:"user/container_w";i:257;s:19:"user/return_product";i:258;s:15:"user/sort_error";i:259;s:9:"user/user";i:260;s:20:"user/user_permission";i:261;s:11:"user/w_user";i:262;s:37:"user/warehouse_allocation_note_leader";}}';
//
////    'a:2:{s:6:"access";a:263:{i:0;s:19:"accounting/exchange";i:1;s:25:"accounting/return_deposit";i:2;s:11:"agent/agent";i:3;s:16:"catalog/activity";i:4;s:17:"catalog/attribute";i:5;s:23:"catalog/attribute_group";i:6;s:15:"catalog/balance";i:7;s:16:"catalog/category";i:8;s:16:"catalog/download";i:9;s:14:"catalog/filter";i:10;s:19:"catalog/information";i:11;s:17:"catalog/inventory";i:12;s:20:"catalog/manufacturer";i:13;s:14:"catalog/option";i:14;s:13:"catalog/price";i:15;s:15:"catalog/product";i:16;s:27:"catalog/product_application";i:17;s:19:"catalog/product_inv";i:18;s:23:"catalog/product_mannage";i:19;s:21:"catalog/product_price";i:20;s:26:"catalog/product_skubarcode";i:21;s:17:"catalog/recurring";i:22;s:14:"catalog/review";i:23;s:11:"catalog/sku";i:24;s:16:"catalog/supplier";i:25;s:18:"common/column_left";i:26;s:18:"common/filemanager";i:27;s:11:"common/menu";i:28;s:14:"common/profile";i:29;s:12:"common/stats";i:30;s:18:"dashboard/activity";i:31;s:15:"dashboard/chart";i:32;s:18:"dashboard/customer";i:33;s:13:"dashboard/map";i:34;s:16:"dashboard/online";i:35;s:15:"dashboard/order";i:36;s:16:"dashboard/recent";i:37;s:14:"dashboard/sale";i:38;s:13:"design/banner";i:39;s:13:"design/layout";i:40;s:14:"extension/feed";i:41;s:19:"extension/installer";i:42;s:22:"extension/modification";i:43;s:16:"extension/module";i:44;s:17:"extension/openbay";i:45;s:17:"extension/payment";i:46;s:18:"extension/shipping";i:47;s:15:"extension/total";i:48;s:16:"feed/google_base";i:49;s:19:"feed/google_sitemap";i:50;s:15:"feed/openbaypro";i:51;s:20:"localisation/country";i:52;s:21:"localisation/currency";i:53;s:21:"localisation/geo_zone";i:54;s:21:"localisation/language";i:55;s:25:"localisation/length_class";i:56;s:21:"localisation/location";i:57;s:25:"localisation/order_status";i:58;s:26:"localisation/return_action";i:59;s:26:"localisation/return_reason";i:60;s:26:"localisation/return_status";i:61;s:25:"localisation/stock_status";i:62;s:22:"localisation/tax_class";i:63;s:21:"localisation/tax_rate";i:64;s:25:"localisation/weight_class";i:65;s:17:"localisation/zone";i:66;s:25:"logistic/driver_face_list";i:67;s:23:"logistic/logistic_allot";i:68;s:24:"logistic/logistic_allot2";i:69;s:29:"logistic/logistic_allot_order";i:70;s:27:"logistic/logistic_allot_van";i:71;s:22:"logistic/logistic_info";i:72;s:19:"marketing/affiliate";i:73;s:14:"marketing/area";i:74;s:12:"marketing/bd";i:75;s:23:"marketing/bd_statistics";i:76;s:17:"marketing/contact";i:77;s:16:"marketing/coupon";i:78;s:25:"marketing/index_promotion";i:79;s:19:"marketing/marketing";i:80;s:23:"marketing/order_instead";i:81;s:24:"marketing/plan_promotion";i:82;s:19:"marketing/promotion";i:83;s:22:"marketing/smspin_query";i:84;s:14:"module/account";i:85;s:16:"module/affiliate";i:86;s:20:"module/amazon_button";i:87;s:13:"module/banner";i:88;s:17:"module/bestseller";i:89;s:15:"module/carousel";i:90;s:15:"module/category";i:91;s:19:"module/ebay_listing";i:92;s:15:"module/featured";i:93;s:13:"module/filter";i:94;s:22:"module/google_hangouts";i:95;s:11:"module/html";i:96;s:18:"module/information";i:97;s:13:"module/latest";i:98;s:16:"module/pp_button";i:99;s:15:"module/pp_login";i:100;s:16:"module/slideshow";i:101;s:14:"module/special";i:102;s:12:"module/store";i:103;s:17:"notice/homenotice";i:104;s:14:"openbay/amazon";i:105;s:22:"openbay/amazon_listing";i:106;s:22:"openbay/amazon_product";i:107;s:16:"openbay/amazonus";i:108;s:24:"openbay/amazonus_listing";i:109;s:24:"openbay/amazonus_product";i:110;s:12:"openbay/ebay";i:111;s:20:"openbay/ebay_profile";i:112;s:21:"openbay/ebay_template";i:113;s:12:"openbay/etsy";i:114;s:20:"openbay/etsy_product";i:115;s:21:"openbay/etsy_shipping";i:116;s:17:"openbay/etsy_shop";i:117;s:23:"payment/amazon_checkout";i:118;s:24:"payment/authorizenet_aim";i:119;s:24:"payment/authorizenet_sim";i:120;s:21:"payment/bank_transfer";i:121;s:22:"payment/bluepay_hosted";i:122;s:24:"payment/bluepay_redirect";i:123;s:14:"payment/cheque";i:124;s:11:"payment/cod";i:125;s:17:"payment/firstdata";i:126;s:24:"payment/firstdata_remote";i:127;s:21:"payment/free_checkout";i:128;s:22:"payment/klarna_account";i:129;s:22:"payment/klarna_invoice";i:130;s:14:"payment/liqpay";i:131;s:14:"payment/nochex";i:132;s:15:"payment/paymate";i:133;s:16:"payment/paypoint";i:134;s:13:"payment/payza";i:135;s:26:"payment/perpetual_payments";i:136;s:18:"payment/pp_express";i:137;s:18:"payment/pp_payflow";i:138;s:25:"payment/pp_payflow_iframe";i:139;s:14:"payment/pp_pro";i:140;s:21:"payment/pp_pro_iframe";i:141;s:19:"payment/pp_standard";i:142;s:14:"payment/realex";i:143;s:21:"payment/realex_remote";i:144;s:22:"payment/sagepay_direct";i:145;s:22:"payment/sagepay_server";i:146;s:18:"payment/sagepay_us";i:147;s:24:"payment/securetrading_pp";i:148;s:24:"payment/securetrading_ws";i:149;s:14:"payment/skrill";i:150;s:19:"payment/twocheckout";i:151;s:28:"payment/web_payment_software";i:152;s:16:"payment/worldpay";i:153;s:23:"purchase/inventory_plan";i:154;s:21:"purchase/pre_purchase";i:155;s:28:"purchase/pre_purchase_adjust";i:156;s:28:"purchase/pre_purchase_upload";i:157;s:17:"purchase/purchase";i:158;s:34:"purchase/warehouse_allocation_note";i:159;s:16:"report/affiliate";i:160;s:25:"report/affiliate_activity";i:161;s:22:"report/affiliate_login";i:162;s:16:"report/bd_coupon";i:163;s:25:"report/billing_statements";i:164;s:19:"report/check_single";i:165;s:24:"report/customer_activity";i:166;s:22:"report/customer_credit";i:167;s:24:"report/customer_feedback";i:168;s:20:"report/customer_info";i:169;s:21:"report/customer_login";i:170;s:22:"report/customer_online";i:171;s:21:"report/customer_order";i:172;s:22:"report/customer_reward";i:173;s:19:"report/driver_total";i:174;s:18:"report/inv_mi_cold";i:175;s:25:"report/inv_mi_cold_search";i:176;s:22:"report/logistic_driver";i:177;s:19:"report/logistic_fee";i:178;s:20:"report/logistic_info";i:179;s:16:"report/marketing";i:180;s:17:"report/order_info";i:181;s:18:"report/out_of_info";i:182;s:21:"report/product_margin";i:183;s:26:"report/product_particulars";i:184;s:24:"report/product_promotion";i:185;s:24:"report/product_purchased";i:186;s:19:"report/product_sale";i:187;s:21:"report/product_viewed";i:188;s:22:"report/purchase_detail";i:189;s:21:"report/purchase_order";i:190;s:21:"report/purchase_query";i:191;s:22:"report/purchase_report";i:192;s:20:"report/sale_bd_query";i:193;s:18:"report/sale_coupon";i:194;s:27:"report/sale_customer_active";i:195;s:18:"report/sale_margin";i:196;s:17:"report/sale_order";i:197;s:18:"report/sale_return";i:198;s:20:"report/sale_shipping";i:199;s:16:"report/sale_stat";i:200;s:19:"report/sale_station";i:201;s:15:"report/sale_tax";i:202;s:21:"report/short_confirms";i:203;s:20:"report/sorting_staff";i:204;s:28:"report/warehouse_check_order";i:205;s:23:"report/warehouse_margin";i:206;s:17:"sale/custom_field";i:207;s:13:"sale/customer";i:208;s:20:"sale/customer_ban_ip";i:209;s:19:"sale/customer_group";i:210;s:22:"sale/financial_confirm";i:211;s:10:"sale/order";i:212;s:16:"sale/order_audit";i:213;s:20:"sale/order_replenish";i:214;s:17:"sale/order_urgent";i:215;s:14:"sale/recurring";i:216;s:11:"sale/refund";i:217;s:11:"sale/return";i:218;s:18:"sale/return_adjust";i:219;s:17:"sale/return_apply";i:220;s:25:"sale/return_specification";i:221;s:12:"sale/voucher";i:222;s:18:"sale/voucher_theme";i:223;s:15:"setting/setting";i:224;s:13:"setting/store";i:225;s:16:"shipping/auspost";i:226;s:17:"shipping/citylink";i:227;s:14:"shipping/fedex";i:228;s:13:"shipping/flat";i:229;s:13:"shipping/free";i:230;s:13:"shipping/item";i:231;s:23:"shipping/parcelforce_48";i:232;s:15:"shipping/pickup";i:233;s:19:"shipping/royal_mail";i:234;s:12:"shipping/ups";i:235;s:13:"shipping/usps";i:236;s:15:"shipping/weight";i:237;s:24:"station/accounting_cycle";i:238;s:15:"station/station";i:239;s:11:"tool/backup";i:240;s:14:"tool/error_log";i:241;s:11:"tool/upload";i:242;s:12:"total/coupon";i:243;s:12:"total/credit";i:244;s:14:"total/handling";i:245;s:16:"total/klarna_fee";i:246;s:19:"total/low_order_fee";i:247;s:12:"total/reward";i:248;s:14:"total/shipping";i:249;s:15:"total/sub_total";i:250;s:9:"total/tax";i:251;s:11:"total/total";i:252;s:13:"total/voucher";i:253;s:15:"user/accounting";i:254;s:8:"user/api";i:255;s:14:"user/container";i:256;s:16:"user/container_w";i:257;s:19:"user/return_product";i:258;s:15:"user/sort_error";i:259;s:9:"user/user";i:260;s:20:"user/user_permission";i:261;s:11:"user/w_user";i:262;s:37:"user/warehouse_allocation_note_leader";}}';
////var_dump($a);
//$a = unserialize($a);
//$a['access'][] = 'user/warehouse_management';
//$a['modify'][] = 'user/warehouse_management';
////var_dump($a);
//$a = serialize($a);
//var_dump($a);
//$a = unserialize($a);
//var_dump($a);
?>


<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>鲜世纪库存管理-仓库</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="view/javascript/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="view/javascript/jquery/jquery.simpleplayer.js"></script>
    <!-- <script type="text/javascript" src="js/alert.js"></script> -->
    <style>
        

*{padding: 0;margin: 0;}

/* 清除浮动 */
.clearfix:after {content: "";display: table;clear: both;}
html, body { height: 100%; }
body {    font-family:"Microsoft YaHei"; background:#EBEBEB; background:url(../images/stardust.png);       font-weight: 300;  font-size: 15px;  color: #333;overflow: hidden;}
a {text-decoration: none; color:#000;}
a:hover{color:#F87982;}

/*home*/
#home{padding-top:50px;}

/*logint界面*/
#login{ padding:10px 10px 10px; width:100%; background:#FFF; margin:auto;
border-radius: 3px;
box-shadow: 0 3px 3px rgba(0, 0, 0, 0.3);
}

.current1{
-moz-transform: scale(0);
-webkit-transform: scale(0);
-o-transform: scale(0);
-ms-transform: scale(0);
transform: scale(0);
-moz-transition: all 0.4s ease-in-out;
-webkit-transition: all 0.4s ease-in-out;
-o-transition: all 0.4s ease-in-out;
transition: all 0.4s ease-in-out;
}


.current{
-moz-transform: scale(1);
-webkit-transform: scale(1);
-o-transform: scale(1);
-ms-transform: scale(1);
transform: scale(1);

}
#login h3{ font-size:28px; line-height:25px; font-weight:300; letter-spacing:3px; margin-bottom:20px;  text-align:center;}
#login label{  display:block; height:35px; padding:0 10px; font-size:18px; line-height:35px;  background:#EBEBEB; margin-bottom:10px;position:relative;}
#login label input{  font:20px/20px "Microsoft YaHei"; background:none;  height:20px; border:none; margin:7px 0 0 10px;width:245px;outline:none ; letter-spacing:normal;  z-index:1; position:relative;  }
#login label  span{ display:block;  height:35px; color:#F30; width:100px; position:absolute; top:0; left:190px; text-align:right;padding:0 10px 0 0; z-index:0; display:none; }
#login button{ font-family:"Microsoft YaHei"; cursor:pointer; width:300px;  height:35px; background:#FE4E5B; border:none; font-size:14px; line-height:30px;  letter-spacing:3px; color:#FFF; position:relative; margin-top:10px;
-moz-transition: all 0.2s ease-in;
-webkit-transition: all 0.2s ease-in;
-o-transition: all 0.2s ease-in;
transition: all 0.2s ease-in;}
#login button:hover{ background:#F87982; color:#000;}

/*头像*/
.avator{
    display:block;
    margin:0 auto 20px;
    border-radius:50%;
}


    </style>
</head>
    
    
<body>
    

<div id="home">
    <form id="login" class="current1" method="post">
        <h3>鲜世纪仓库管理</h3>
        <label >
            所属仓库:<select id="warehouse_id" style="width:12rem;height:2rem;margin-bottom: 0.5rem">
            </select>
        </label>
        <label>用户名<input id="username" type="text" name="name" style="width:215px; height: 2rem" /><span>用户名为空</span></label>
        <label>密码<input id="password" type="password" name="pass"  style="height: 2rem" /><span>密码为空</span></label>
        <button type="button" id="login">登入</button>
    </form>
</div>
<div id="get_sql_array"></div>

</body>



<script>

    $(document).ready(function(){
        // getperms();return false;
        $.ajax({
            type : 'POST',
            url : 'invapi.php?method=getWarehouseId',
            data : {
                method : 'getWarehouseId'
            },
            success : function (response){
                console.log(response);
                if(response){
                    var jsonData = eval(response);
                    var html = '<option value=0>-请选择所在仓库-</option>';
                    $.each(jsonData, function(index, value){
                        html += '<option value='+ value.warehouse_id +' >' + value.title + '</option>';
                    });

                    $('#warehouse_id').html(html);
                }

            }
        });
    });





    $(function(){
        $("#login").addClass("current");

        /**
         * 正则检验邮箱
         * email 传入邮箱
         * return true 表示验证通过
         */
        function check_email(email) {
            if (/^[\w\-\.]+@[\w\-]+(\.[a-zA-Z]{2,4}){1,2}$/.test(email)) return true;
        }


        //input 按键事件
        $("input[name]").keyup(function(e){
            //禁止输入空格  把空格替换掉
            if($(this).attr('name')=="pass"&&e.keyCode==32){
                $(this).val(function(i,v){
                    return $.trim(v);
                });
            }
            if($.trim($(this).val())!=""){
                $(this).nextAll('span').eq(0).css({display:'none'});
            }
        });


        //错误信息
        var succ_arr=[];

        //input失去焦点事件
        $("input[name]").focusout(function(e){

            var msg="";
             if($.trim($(this).val())==""){
                  if($(this).attr('name')=='name'){
                          succ_arr[0]=false;
                          msg="登入名为空";
                  }else if($(this).attr('name')=='pass'){
                           succ_arr[1]=false;
                           msg="密码为空";
                  }

            }else{
                  if($(this).attr('name')=='name'){
                          succ_arr[0]=true;

                  }else if($(this).attr('name')=='pass'){
                           succ_arr[1]=true;

                  }
            }
            var a=$(this).nextAll('span').eq(0);
            a.css({display:'block'}).text(msg);
        });


        //Ajax用户注册
        $("button[id='login']").click(function(){
            $("input[name]").focusout();  //让所有的input标记失去一次焦点 来设置msg信息
            for (x in succ_arr){if(succ_arr[x]==false) return;}
            // $("#login").removeClass("current");
            var data=$('#login').serialize(); //序列化表单元素

            var username = $("#username").val();
            var password = $("#password").val();
            var warehouse_id = $("#warehouse_id").val();
            var warehouse_title =  $("#warehouse_id").text();

            var ver = 0;
            <?php if(@$_GET['ver'] == 'db' || @$_GET['return'] == 'l.php'){ ?>
            ver = 'db';
            <?php } ?>
            $.ajax({
                type : 'POST',
                url : 'invapi.php?method=inventory_login',
                data : {
                    method : 'inventory_login',
                    username : username,
                    password : password,
                    warehouse_id : warehouse_id,
                    warehouse_title :warehouse_title,
                    ver : ver
                },
                success : function (response , status , xhr){

                    console.log(response);
                    var jsonData = $.parseJSON(response);
                    if(jsonData.status == 1){
                        alert("用户不存在或密码错误或所选仓库错误");
                    }
                    if(jsonData.status == 2){
                        // if
                          location.href = "<?php echo @$_GET['return']?$_GET['return']:'i.php';?>?auth=xsj2015inv";
                    }
                }
            });

        });
        /**
         有兴趣的可以到这里 自行发送Ajax请求 实现注册功能
         */


    });

    function getperms(){








        var type = 1;
        console.log(123123);
        // var inventory_user_id = parseInt($("#inventory_user_id").text());
        var submit_bad_product = '772,596,869,918,976,1264,1003,1368,1315,872';
        var submit_bad_products = submit_bad_product.split(',');
        var result = false;
        $.each(submit_bad_products, function(index,value){
            if (976 == value) {
                result = true;
                return false;
            }
            console.log(value);
        });
        // return true;
        if (result) {
            $("#inventoryOut").show();
        }
//            if(inventory_user_id == 772 || inventory_user_id == 596 || inventory_user_id == 869  || inventory_user_id == 918 || inventory_user_id == 976 || inventory_user_id == 1264 || inventory_user_id == 1003 || inventory_user_id == 1368 || inventory_user_id == 1315){
//            $("#inventoryOut").show();
//        }
        var warehouse_id = $("#warehouse_id").text();
        var articleIdList = new Array();
        var h3=$("button.invopt");
        for (var i = 0;i< h3.length; i++) {
            var articleId = h3.eq(i).attr("id");
            var articleIdList = articleIdList.concat(articleId);
        };

        $.ajax({
            type: 'POST',
            url: 'invapi.php',
            data: {
                method: 'getperms',
                data : {
                    inventory_user_id : inventory_user_id,
                    type:type,
                },
            },
            success :function (response){
                var jsonData = $.parseJSON(response);
                if (type == 1) {
                    $("#get_sql_array").html(jsonData);
                    return false;
                }

                $.each(jsonData, function(index,value){


                    if(value.perms){
                        var perms = value.perms;
                        var arr_perms = perms.split(",");
                        $.each(arr_perms, function(index,val_id){
                            var id = $.trim(val_id);
                            $("#"+id).show();
                        });
                    }

                    if(value.warehouse_id != 10){
                        $("#inventoryInFreshDistr").hide();
                        $("#inventoryVegCheck").hide();
                        $("#inventoryCheck").hide();
                        $("#freshBasket").hide();
                        $("#inventoryChange").hide();
                    }else{
                        $("#inventoryInFastDistr").hide();
                    }

                });

            }
        });

    }



</script>



</html>