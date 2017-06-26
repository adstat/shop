<?php
// Heading
$_['heading_title']					= 'Realex Redirect';

// Text
$_['text_payment']				  	 = 'Payment';
$_['text_success']					= 'Realex account details modificato correttamente!';
$_['text_edit']                     = 'Edit Realex Redirect';
$_['text_live']						= 'Live';
$_['text_demo']						= 'Demo';
$_['text_card_type']				= 'Tipo Carta';
$_['text_enabled']					= 'Abilitato';
$_['text_use_default']				= 'Use default';
$_['text_merchant_id']				= 'ID Commerciante';
$_['text_subaccount']				= 'Subaccount';
$_['text_secret']					= 'Shared secret';
$_['text_card_visa']				= 'Visa';
$_['text_card_master']				= 'Mastercard';
$_['text_card_amex']				= 'American Express';
$_['text_card_switch']				= 'Switch/Maestro';
$_['text_card_laser']				= 'Laser';
$_['text_card_diners']				= 'Diners';
$_['text_capture_ok']				= 'Capture was successful';
$_['text_capture_ok_order']			= 'Capture was successful, order status updated to success - settled';
$_['text_rebate_ok']				= 'Rebate was successful';
$_['text_rebate_ok_order']			= 'Rebate was successful, order status updated to rebated';
$_['text_void_ok']					= 'Void was successful, order status updated to voided';
$_['text_settle_auto']				= 'Auto';
$_['text_settle_delayed']			= 'Delayed';
$_['text_settle_multi']				= 'Multi';
$_['text_url_message']				= 'You must supply the store URL to your Realex account manager before going live';
$_['text_payment_info']				= 'Informazioni Pagamento';
$_['text_capture_status']			= 'Pagamento Catturato';
$_['text_void_status']				= 'Pagamento Annullato';
$_['text_rebate_status']			= 'Payment rebated';
$_['text_order_ref']				= 'Rif Ordine';
$_['text_order_total']				= 'Totale Autorizzato';
$_['text_total_captured']			= 'Totale Catturato';
$_['text_transactions']				= 'Transazioni';
$_['text_column_amount']			= 'Importo';
$_['text_column_type']				= 'Tipo';
$_['text_column_date_added']		= 'Creato';
$_['text_confirm_void']				= 'Are you sure you want to void the payment?';
$_['text_confirm_capture']			= 'Are you sure you want to capture the payment?';
$_['text_confirm_rebate']			= 'Are you sure you want to rebate the payment?';
$_['text_realex']					= '<a target="_BLANK" href="http://www.realexpayments.co.uk/partner-refer?id=opencart"><img src="view/image/payment/realex.png" alt="Realex" title="Realex" style="border: 1px solid #EEEEEE;" /></a>';

// Entry
$_['entry_merchant_id']				= 'ID Commerciante';
$_['entry_secret']					= 'Shared secret';
$_['entry_rebate_password']			= 'Rebate password';
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
$_['entry_status_decline_pending']	= 'Decline - offline auth';
$_['entry_status_decline_stolen']	= 'Decline - lost or stolen card';
$_['entry_status_decline_bank']		= 'Decline - bank error';
$_['entry_status_void']				= 'Annullato';
$_['entry_status_rebate']			= 'Rebated';
$_['entry_notification_url']		= 'Notification URL';

// Help
$_['help_total']					= 'Il totale che deve raggiungere l'ordine prima che diventi attiva questa tipologia di pagamento.;
$_['help_card_select']				= 'Chiedi all\'utente di scegliere il tipo di carta prima di reindirizzarlo';
$_['help_notification']				= 'You need to supply this URL to Realex to get payment notifications';
$_['help_debug']					= 'Enabling debug will write sensitive data to a log file. You should always disable unless instructed otherwise';
$_['help_dcc_settle']				= 'If your subaccount is DCC enabled you must use Autosettle';

// Tab
$_['tab_api']					= 'Informazioni API';
$_['tab_account']					= 'Account';
$_['tab_sub_account']				= 'Account';
$_['tab_order_status']				= 'Stato Ordine';
$_['tab_payment']					= 'Informazioni Pagamento';
$_['tab_advanced']					= 'Avanzate';

// Button
$_['button_capture']				= 'Cattura';
$_['button_rebate']					= 'Rebate / refund';
$_['button_void']					= 'Annullato';

// Error
$_['error_merchant_id']				= 'ID Commerciante obbligatorio';
$_['error_secret']					= 'Shared secret obbligatorio';
$_['error_live_url']				= 'Live URL obbligatorio';
$_['error_demo_url']				= 'Demo URL obbligatorio';
$_['error_data_missing']			= 'Dati Mancanti';
$_['error_use_select_card']			= 'You must have "Select Card" enabled for subaccount routing by card type to work';