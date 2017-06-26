<?php
// Heading
$_['heading_title']					= 'First Data EMEA Web Service API';

// Text
$_['text_firstdata_remote']			= '<img src="view/image/payment/firstdata.png" alt="First Data" title="First Data" style="border: 1px solid #EEEEEE;" />';
$_['text_payment']					= 'Pagamento';
$_['text_success']					= 'First Data account details modificato correttamente!';
$_['text_edit']                     = 'Modifica Servizio Web API First Data EMEA';
$_['text_card_type']				= 'Tipo Carta';
$_['text_enabled']					= 'Abilitato';
$_['text_merchant_id']				= 'ID Negozio';
$_['text_subaccount']				= 'Subaccount';
$_['text_user_id']					= 'ID Utente';
$_['text_capture_ok']				= 'Cattura avvenuta con successo';
$_['text_capture_ok_order']			= 'Cattura avvenuta con successo, ordine aggiornato come avvenuto cor successo';
$_['text_refund_ok']				= 'Rimborso effettuato con successo';
$_['text_refund_ok_order']			= 'Refund was successful, order status updated to refunded';
$_['text_void_ok']					= 'Void was successful, order status updated to voided';
$_['text_settle_auto']				= 'Vendita';
$_['text_settle_delayed']			= 'Pre Autorizzazione';
$_['text_mastercard']				= 'Mastercard';
$_['text_visa']						= 'Visa';
$_['text_diners']					= 'Diners';
$_['text_amex']						= 'American Express';
$_['text_maestro']					= 'Maestro';
$_['text_payment_info']				= 'Informazioni Pagamento';
$_['text_capture_status']			= 'Pagamento Catturato';
$_['text_void_status']				= 'Pagamento Annullato';
$_['text_refund_status']			= 'Pagamento Rimborsato';
$_['text_order_ref']				= 'Rif Ordine';
$_['text_order_total']				= 'Totale Autorizzato';
$_['text_total_captured']			= 'Totale Catturato';
$_['text_transactions']				= 'Transazioni';
$_['text_column_amount']			= 'Importo';
$_['text_column_type']				= 'Tipo';
$_['text_column_date_added']		= 'Creato';
$_['text_confirm_void']				= 'Sicuri di voler annullare il pagamento?';
$_['text_confirm_capture']			= 'Sicuri di voler catturare il pagamento?';
$_['text_confirm_refund']			= 'Sicuri di voler rimborsare il pagamento?';

// Entry
$_['entry_certificate_path']		= 'Certificate path';
$_['entry_certificate_key_path']	= 'Private key path';
$_['entry_certificate_key_pw']		= 'Private key password';
$_['entry_certificate_ca_path']		= 'CA path';
$_['entry_merchant_id']				= 'ID Negozio';
$_['entry_user_id']					= 'ID Utente';
$_['entry_password']				= 'Password';
$_['entry_total']					= 'Totale';
$_['entry_sort_order']				= 'Ordinamento';
$_['entry_geo_zone']				= 'Zona Geografica';
$_['entry_status']					= 'Stato';
$_['entry_debug']					= 'Debug logging';
$_['entry_auto_settle']				= 'Settlement type';
$_['entry_status_success_settled']	= 'Success - settled';
$_['entry_status_success_unsettled'] = 'Success - not settled';
$_['entry_status_decline']			= 'Declina';
$_['entry_status_void']				= 'Annullato';
$_['entry_status_refund']			= 'Rimborsato';
$_['entry_enable_card_store']		= 'Enable card storage tokens';
$_['entry_cards_accepted']			= 'Tipi di Carte Accettate';

// Help
$_['help_total']					= 'Il totale che deve raggiungere l'ordine prima che diventi attiva questa tipologia di pagamento.;
$_['help_certificate']				= 'Certificates and private keys should be stored outside of your public web folders';
$_['help_card_select']				= 'Ask the user to choose thier card type before they are redirected';
$_['help_notification']				= 'You need to supply this URL to First Data to get payment notifications';
$_['help_debug']					= 'Enabling debug will write sensitive data to a log file. You should always disable unless instructed otherwise .';
$_['help_settle']					= 'If you use pre-auth you must complete a post-auth action within 3-5 days otherwise your transaction will be dropped';

// Tab
$_['tab_account']					= 'Informazioni API';
$_['tab_order_status']				= 'Stato Ordine';
$_['tab_payment']					= 'Informazioni Pagamento';

// Button
$_['button_capture']				= 'Cattura';
$_['button_refund']					= 'Rimborso';
$_['button_void']					= 'Annullato';

// Error
$_['error_merchant_id']				= 'ID Negozio Obbligatorio';
$_['error_user_id']					= 'User ID obbligatorio';
$_['error_password']				= 'Password obbligatoria';
$_['error_certificate']				= 'Certificate path obbligatorio';
$_['error_key']						= 'Certificate key obbligatorio';
$_['error_key_pw']					= 'Certificate key password obbligatorio';
$_['error_ca']						= 'Certificate Authority (CA) obbligatorio';