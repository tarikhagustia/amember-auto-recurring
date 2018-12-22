<?php

/**
 * @author Tarikh Agustia [agustia.tarikh150@gmail.com]
 * @version 0.1 [description]
 *
 * This plugin allow you to craete recurring invoice by due date
 */
class Am_Plugin_AutoRecurring extends Am_Plugin
{
  const PLUGIN_STATUS = self::STATUS_PRODUCTION;
  const PLUGIN_REVISION = '0.1';
  const ADMIN_PERM_ID = 'auto-recurring';

  /**
   * This method will call by cronjob will check daily
   * @method onHourly
   * @param  Am_Event $event return datetime
   * @return [type]          [description]
   */
  public function onDaily(Am_Event $event)
  {
      $date = date('Y-m-d');
      // get for rebill based on this day
      $bills = $this->getDi()->invoiceTable->findForRebill($date);
      foreach ($bills as $key => $bill) {
          // Create invoice
          $invoice = $this->getDi()->invoiceRecord;
          $invoice->setUser($this->getDi()->userTable->load($bill->user_id));

          foreach ($bill->getItems() as $key => $product) {
              $invoice->add(Am_Di::getInstance()->productTable->load($product->item_id), $product->qty);
          }
          $invoice->due_date = $date;
          $rebill_date = new DateTime($date);
          $rebill_date = $rebill_date->add(new DateInterval("P1M"))->format('Y-m-d');
          $invoice->rebill_date = $rebill_date;
          $invoice->setPaysystem($bill->paysys_id);
          $errors = $invoice->validate();

          if (!$errors) {
              // Error happend
              $this->getDi()->adminLogTable->log("AUTO RECURRING : Add recurring invoice failed errors {$errors}", 'invoice');
              // $event->addReturn("Gw Dipanggil !");
          }
          $invoice->calculate();
          $invoice = $invoice->save();

          // TODO: Send Email
          $et = Am_Mail_Template::load('invoice_pay_link', $invoice->getUser()->lang ? $invoice->getUser()->lang : null);
          $et->setUser($invoice->getUser());
          $et->setUrl($this->getDi()->surl("pay/{$invoice->getSecureId('payment-link')}", false));
          // $et->setMessage($vars['message']);
          $et->setInvoice($invoice);
          $et->setInvoice_text($invoice->render());
          $et->setInvoice_html($invoice->renderHtml());
          $et->setProduct_title(implode(", ", array_map(
              function ($item){
                  return $item->title;
              },
              $invoice->getProducts()
                  )));
          $et->send($invoice->getUser());

          $this->getDi()->adminLogTable->log("AUTO RECURRING : Add recurring success", 'invoice');
      }
      if (!count($bills) > 0) {
        $this->getDi()->adminLogTable->log("AUTO RECURRING : Rebill not found for " . $date, 'invoice');
      }
      $event->addReturn(true);
  }
}
?>
