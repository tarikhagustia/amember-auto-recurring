<?php

/**
 * @author Tarikh Agustia [agustia.tarikh150@gmail.com]
 * @version 0.1 [description]
 *
 * This plugin allow you to craete recurring invoice by due date
 */
class Am_Plugin_AutoRecurring extends Am_Plugin
{
  const PLUGIN_STATUS = self::STATUS_DEV;
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
      $bills = $this->getDi()->invoiceTable->selectObjects("SELECT * FROM ?_invoice WHERE rebill_times > 0 AND DATE(tm_added) = ?", $date);
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
              $this->getDi()->adminLogTable->log("Add recurring invoice failed errors {$errors}", 'invoice');
              // $event->addReturn("Gw Dipanggil !");
          }
          $invoice->calculate();
          $invoice = $invoice->save();

          $this->getDi()->adminLogTable->log("Add recurring success", 'invoice');
      }
      $event->addReturn(true);
  }
}
?>
