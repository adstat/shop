<?php
// Heading
$_['heading_title']					= 'BluePay Redirect (Requires SSL)';

// Text
$_['text_payment']					= 'Pagamento';
$_['text_success']					= 'BluePay Redirect account details modificato correttamente!';
$_['text_edit']                     = 'Edit BluePay Redirect (Requires SSL)';
$_['text_bluepay_redirect']			= '<a href="http://www.bluepay.com/preferred-partner/opencart" target="_blank"><img src="view/image/payment/bluepay.jpg" alt="BluePay Redirect" title="BluePay Redirect" style="border: 1px solid #EEEEEE;" /></a>';
$_['text_sim']						= 'Simulator';
$_['text_test']						= 'Test';
$_['text_live']						= 'Live';
$_['text_sale']					    = 'Vendita';
$_['text_authenticate']				= 'Authenticate';
$_['text_release_ok']				= 'Release was successful';
$_['text_release_ok_order']			= 'Release was successful';
$_['text_rebate_ok']				= 'Rebate was successful';
$_['text_rebate_ok_order']			= 'Rebate was successful, order status updated to rebated';
$_['text_void_ok']					= 'Void was successful, order status updated to voided';
$_['text_payment_info']				= 'Informazioni Pagamento';
$_['text_release_status']			= 'Payment released';
$_['text_void_status']				= 'Pagamento Annullato';
$_['text_rebate_status']			= 'Payment rebated';
$_['text_order_ref']				= 'Rif Ordine';
$_['text_order_total']				= 'Totale Autorizzato';
$_['text_total_released']			= 'Total released';
$_['text_transactions']				= 'Transazioni';
$_['text_column_amount']			= 'Importo';
$_['text_column_type']				= 'Tipo';
$_['text_column_date_added']		= 'Creato';
$_['text_confirm_void']				= 'Are you sure you want to void the payment?';
$_['text_confirm_release']			= 'Are you sure you want to release the payment?';
$_['text_confirm_rebate']			= 'Are you sure you want to rebate the payment?';

// Entry
$_['entry_vendor']					= 'Account ID';
$_['entry_secret_key']				= "Secret Key";
$_['entry_test']					= 'Modalit&agrave; Test';
$_['entry_transaction']				= 'Metodo Transazione';
$_['entry_total']					= 'Totale';
$_['entry_order_status']			= 'Stato Ordine';
$_['entry_geo_zone']				= 'Zona Geografica';
$_['entry_status']					= 'Stato';
$_['entry_sort_order']				= 'Ordinamento';
$_['entry_debug']					= 'Debug logging';
$_['entry_card']					= 'Store Cards';

// Help
$_['help_total']					= 'Il totale che deve raggiungere l'ordine prima che diventi attiva questa tipologia di pagamento.';
$_['help_debug']					= 'Enabling debug will write sensitive data to a log file. You should always disable unless instructed otherwise';
$_['help_transaction']				= 'Transaction method MUST be set to Payment to allow subscription payments';
$_['help_cron_job_token']			= 'Make this long and hard to guess';
$_['help_cron_job_url']				= 'Set a cron job to call this URL';

// Button
$_['btn_release']					= 'Release';
$_['btn_rebate']					= 'Rebate / refund';
$_['btn_void']						= 'Annullato';

// Error
$_['error_permission']				= 'Attenzione: Non si hanno i permessi per poter modificare payment BluePay!';
$_['error_account_id']				= 'ID account obbligatorio!';
$_['error_secret_key']				= 'Chiave Segreta Obbligatoria!';