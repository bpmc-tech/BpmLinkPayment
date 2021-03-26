<?php


namespace Plugin\BpmLinkPayment\Repository\Query;

use Eccube\Doctrine\Query\WhereClause;
use Eccube\Doctrine\Query\WhereCustomizer;
use Eccube\Repository\QueryKey;

class AdminOrderBpmLinkPaymentCustomizer extends WhereCustomizer {


  protected function createStatements($params, $queryKey) {
    $rtn = [];

    if (!empty($params['bpm_link_payment_tran_code']) && $params['bpm_link_payment_tran_code']){

       $rtn[] = WhereClause::like('o.BpmLinkPaymentTranCode', ':BpmLinkPaymentTranCode', ['BpmLinkPaymentTranCode' => '%'.$params['bpm_link_payment_tran_code'].'%' ]);

    }


    if (!empty($params['bpm_link_payment_payment_status_id']) && $params['bpm_link_payment_payment_status_id']){

      $rtn[] = WhereClause::in('o.BpmLinkPaymentPaymentStatus', ':BpmLinkPaymentPaymentStatus', ['BpmLinkPaymentPaymentStatus' => $params['bpm_link_payment_payment_status_id']]);

    }

    return $rtn;
  }


  public function getQueryKey() {
    return QueryKey::ORDER_SEARCH_ADMIN;
  }
}
?>