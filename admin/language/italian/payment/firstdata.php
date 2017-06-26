<?php
// Heading
$_['heading_title']					= 'First Data EMEA Connect (3DSecure enabled)';

// Text
$_['text_payment']					= 'Pagamento';
$_['text_success']					= 'First Data account details modificato correttamente!';
$_['text_edit']                     = 'Edit First Data EMEA Connect (3DSecure enabled)';
$_['text_notification_url']			= 'Notification URL';
$_['text_live']						= 'Live';
$_['text_demo']						= 'Demo';
$_['text_enabled']					= 'Abilitato';
$_['text_merchant_id']				= 'ID Negozio';
$_['text_secret']					= 'Shared secret';
$_['text_capture_ok']				= 'Capture was successful';
$_['text_capture_ok_order']			= 'Capture was successful, order status updated to success - settled';
$_['text_void_ok']					= 'Void was successful, order status updated to voided';
$_['text_settle_auto']				= 'Vendita';
$_['text_settle_delayed']			= 'Pre Autorizzazione';
$_['text_success_void']				= 'Transaction has been voided';
$_['text_success_capture']			= 'Transaction has been captured';
$_['text_firstdata']				= '<img src="view/image/payment/firstdata.png" alt="First Data" title="First Data" style="border: 1px solid #EEEEEE;" />';
$_['text_payment_info']				= 'Informazioni Pagamento';
$_['text_capture_status']			= 'Pagamento Catturato';
$_['text_void_status']				= 'Pagamento Annullato';
$_['text_order_ref']				= 'Rif Ordine';
$_['text_order_total']				= 'Totale Autorizzato';
$_['text_total_captured']			= 'Totale Catturato';
$_['text_transactions']				= 'Transazioni';
$_['text_column_amount']			= 'Importo';
$_['text_column_type']				= 'Tipo';
$_['text_column_date_added']		= 'Creato';
$_['text_confirm_void']				= 'Are you sure you want to void the payment?';
$_['text_confirm_capture']			= 'Are you sure you want to capture the payment?';

// Entry
$_['entry_merchant_id']				= 'ID Negozio';
$_['entry_secret']					= 'Shared secret';
$_['entry_total']					= 'Totale';
$_['entry_sort_order']				= 'Ordinamento';
$_['entry_geo_zone']				= 'Zona Geografica';
$_['entry_status']					= 'Stato';
$_['entry_debug']					= 'Debug logging';
$_['entry_live_demo']				= 'Live / Demo';
$_['entry_auto_settle']				= 'Settlement type';
$_['entry_card_select']				= 'Seleziona Carta';
$_['entry_tss_check']				= 'TSS checks';
$_['entry_live_url']				= 'Live connection URL';
$_['entry_demo_url']				= 'Demo connection URL';
$_['entry_status_success_settled']	= 'Success - settled';
$_['entry_status_success_unsettled'] = 'Success - not settled';
$_['entry_status_decline']			= 'Declina';
$_['entry_status_void']				= 'Annullato';
$_['entry_enable_card_store']		= 'Enable card storage tokens';

// Help
$_['help_total']					= 'Il totale che deve raggiungere l'ordine prima che diventi attiva questa tipologia di pagamento.;
$_['help_notification']				= 'You need to supply this URL to First Data to get payment notifications';
$_['help_debug']					= 'Enabling debug will write sensitive data to a log file. You should always disable unless instructed otherwise';
$_['help_settle']					= 'If you use pre-auth you must complete a post-auth action within 3-5 days otherwise your transaction will be dropped';

// Tab
$_['tab_account']					= 'Informazioni API';
$_['tab_order_status']				= 'Stato Ordine';
$_['tab_payment']					= 'Informazioni Pagamento';
$_['tab_advanced']					= 'Avanzate';

// Button
$_['button_capture']				= 'Cattura';
$_['button_void']					= 'Annullato';

// Error
$_['error_merchant_id']				= 'ID Negozio Obbligatorio';
$_['error_secret']					= 'Shared secret obbligatorio';
$_['error_live_url']				= 'Live URL obbligatorio';
$_['error_demo_url']				= 'Demo URL obbligatorio';
$_['error_data_missing']			= 'Dati Mancanti';
$_['error_void_error']				= 'Unable to void transaction';
$_['error_capture_error']			= 'Unable to capture transaction';