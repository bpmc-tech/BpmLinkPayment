<?php
//PaymentController

namespace Plugin\BpmLinkPayment\Controller;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\ShoppingService;
use Eccube\Service\OrderStateMachine;
use Eccube\Service\MailService;
use Plugin\BpmLinkPayment\Entity\PaymentStatus;
use Plugin\BpmLinkPayment\Repository\PaymentStatusRepository;
use Plugin\BpmLinkPayment\Entity\Config;
use Plugin\BpmLinkPayment\Repository\ConfigRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentController extends AbstractController {

  /**
   * @var OrderRepository
   */
  protected $orderRepository;

  /**
   * @var OrderStatusRepository
   */
  protected $orderStatusRepository;

  /**
   * @var PaymentStatusRepository
   */
  protected $paymentStatusRepository;

  /**
   * @var PurchaseFlow
   */
  protected $purchaseFlow;

  /**
   * @var CartService
   */
  protected $cartService;

  /**
   * @var OrderStateMachine
   */
  protected $orderStateMachine;

  /**
   * @var ConfigRepository
   */
  protected $configRepository;

  /**
   * @var MailService
   */
  protected $mailService;


  /**
   * PaymentController constructor.
   *
   * @param OrderRepository $orderRepository
   * @param OrderStatusRepository $orderStatusRepository
   * @param PaymentStatusRepository $paymentStatusRepository
   * @param PurchaseFlow $shoppingPurchaseFlow,
   * @param CartService $cartService
   * @param OrderStateMachine $orderStateMachine
   */
  public function __construct(
      OrderRepository $orderRepository,
      OrderStatusRepository $orderStatusRepository,
      PaymentStatusRepository $paymentStatusRepository,
      PurchaseFlow $shoppingPurchaseFlow,
      CartService $cartService,
      OrderStateMachine $orderStateMachine,
      ConfigRepository $configRepository,
      MailService $mailService
  ) {
      $this->orderRepository = $orderRepository;
      $this->orderStatusRepository = $orderStatusRepository;
      $this->paymentStatusRepository = $paymentStatusRepository;
      $this->purchaseFlow = $shoppingPurchaseFlow;
      $this->cartService = $cartService;
      $this->orderStateMachine = $orderStateMachine;
      $this->configRepository = $configRepository;
      $this->mailService = $mailService;
  }

  /**
   * @Route("/bpm_link_payment/{order_no}/{pre_order_id}/bridge", name="bpm_link_payment_bridge")
   *
   * @param Request $request
   *
   * @return RedirectResponse
   */
  public function bridge(Request $request) {
    $orderNo = $request->attributes->get('order_no');
    $preOrderId = $request->attributes->get('pre_order_id');

    $Order = $this->getOrderByNo($orderNo);
    if (!$Order) {
      throw new NotFoundHttpException();
    }

    if($Order->getPreOrderId() != $preOrderId){
      throw new NotFoundHttpException();
    }

    if ($this->getUser() != $Order->getCustomer()) {
      throw new NotFoundHttpException();
    }

    $items = $Order->getProductOrderItems();
    $item_len = count($items);
    $product_name = $items[0]->getProductName();
    if($item_len > 1) {
      $product_name .= " (他".($item_len-1)."点)";
    }
    $paymentTotal = $Order->getPaymentTotal();
    $currencyCode = $Order->getCurrencyCode();
    if($currencyCode === 'JPY') {
      $paymentTotal = (int)$paymentTotal;
    }

    $baseUrl = $request->getUriForPath('/');
    $cancelUrl = $baseUrl."bpm_link_payment".'/'.$orderNo.'/'.$preOrderId.'/back';
    $callbackUrl = $baseUrl."bpm_link_payment".'/'.$orderNo.'/'.$preOrderId.'/complete';
    $customer = $Order->getCustomer();

    $email = $Order->getEmail();
    $phoneNumber = $Order->getPhoneNumber();

    $config = $this->configRepository->find(1);
    $api_domain = $config->getApiDomain();
    $api_token = $config->getApiToken();

    /////////////////
    $email = htmlspecialchars($email);
    $phoneNumber = htmlspecialchars($phoneNumber);
    $product_name = htmlspecialchars($product_name);

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <title>BPM Link Payment</title>
  <meta charset="utf-8">
  <meta http-equiv="Cache-Control" content="no-cache">
</head>
<body>
  <div style="text-align: center;">
    <img src="https://cdn.bpmc.jp/img/logo.png">
    <p style="color: #999;">
      自動でBPMクレジットカード決済ページに移動します。<br>
      移動しない場合は「支払ページへ移動」をタップしてください。
    </p>
    <form method="POST" action="https://{$api_domain}/link/{$api_token}/payment" id="form">
      <input type="hidden" name="product" value="{$product_name}" />
      <input type="hidden" name="amount" value="{$paymentTotal}" />
      <input type="hidden" name="currency_code" value="{$currencyCode}" />
      <input type="hidden" name="shop_tracking" value="{$preOrderId}" />
      <input type="hidden" name="callback_url" value="{$callbackUrl}" />
      <input type="hidden" name="cancel_url" value="{$cancelUrl}" />
      <input type="hidden" name="email" value="{$email}" />
      <input type="hidden" name="tel" value="{$phoneNumber}" />
      <input type="hidden" name="shop_data1" value="" />
      <input type="hidden" name="shop_data2" value="" />
      <input type="hidden" name="shop_data3" value="" />
      <button type="submit">支払ページへ移動</button>
    </form>
  </div>
  <script type="text/javascript">
(function(){
  document.getElementById('form').submit();
})();
  </script>
</body>
</html>
HTML;
    return new Response($html);
  }


  /**
   * @Route("/bpm_link_payment/{order_no}/{pre_order_id}/back", name="bpm_link_payment_back")
   *
   * @param Request $request
   *
   * @return RedirectResponse
   */
  public function back(Request $request) {
    $orderNo = $request->attributes->get('order_no');
    $preOrderId = $request->attributes->get('pre_order_id');

    $Order = $this->getOrderByNo($orderNo);
    if (!$Order) {
      throw new NotFoundHttpException();
    }

    if($Order->getPreOrderId() != $preOrderId){
      throw new NotFoundHttpException();
    }

    if ($this->getUser() != $Order->getCustomer()) {
      throw new NotFoundHttpException();
    }

    // 受注ステータスを購入処理中へ変更
    $OrderStatus = $this->orderStatusRepository->find(OrderStatus::PROCESSING);
    $Order->setOrderStatus($OrderStatus);

    // 決済ステータスを未決済へ変更
    $PaymentStatus = $this->paymentStatusRepository->find(PaymentStatus::OUTSTANDING);
    $Order->setBpmLinkPaymentPaymentStatus($PaymentStatus);

    // purchaseFlow::rollbackを呼び出し, 購入処理をロールバックする.
    $this->purchaseFlow->rollback($Order, new PurchaseContext());

    $this->entityManager->flush();

    return $this->redirectToRoute('shopping');
  }

  /**
   * 完了画面へ遷移する.
   *
   * @Route("/bpm_link_payment/{order_no}/{pre_order_id}/complete", name="bpm_link_payment_complete")
   */
  public function complete(Request $request) {

    $orderNo = $request->attributes->get('order_no');
    $preOrderId = $request->attributes->get('pre_order_id');

    $Order = $this->getOrderByNo($orderNo, true);
    if (!$Order) {
      throw new NotFoundHttpException();
    }

    if($Order->getPreOrderId() != $preOrderId){
      throw new NotFoundHttpException();
    }

    if ($this->getUser() != $Order->getCustomer()) {
      throw new NotFoundHttpException();
    }


    // カートを削除する
    $this->cartService->clear();

    // FIXME 完了画面を表示するため, 受注IDをセッションに保持する
    $this->session->set('eccube.front.shopping.order.id', $Order->getId());

    $this->entityManager->flush();

    return $this->redirectToRoute('shopping_complete');
  }


  /**
   * 結果通知URLを受け取る.
   *
   * @Route("/bpm_link_payment/receive_complete", name="bpm_link_payment_receive_complete")
   */
  public function receiveComplete(Request $request) {

    // 受注番号を受け取る
    $preOrderId = $request->get('shop_tracking');
    $tran_code = $request->get('tran_code');
    $dba = $request->get('dba');

    $Order = $this->getOrderByPreOrderId($preOrderId);
    if (!$Order) {
      throw new NotFoundHttpException();
    }
    // 受注ステータスを入金済みへ変更
    $OrderStatus = $this->orderStatusRepository->find(OrderStatus::PAID);
    $Order->setOrderStatus($OrderStatus);

    // 決済ステータスを実売上へ変更
    $PaymentStatus = $this->paymentStatusRepository->find(PaymentStatus::ACTUAL_SALES);
    $Order->setBpmLinkPaymentPaymentStatus($PaymentStatus);

    // 決済承認番号を設定
    $Order->setBpmLinkPaymentTranCode($tran_code);

    // 明細書名を設定
    $Order->setBpmLinkPaymentDba($dba);

    // 注文完了メールにメッセージを追加
    $Order->appendCompleteMailMessage('');

    // purchaseFlow::commitを呼び出し, 購入処理を完了させる.
    $this->purchaseFlow->commit($Order, new PurchaseContext());

    log_info('[注文処理] 注文メールの送信を行います.', [$Order->getId()]);
    $this->mailService->sendOrderMail($Order);

    $this->entityManager->flush();

    return new Response('OK!!');
  }


  /**
   * 注文番号で受注を検索する.
   *
   * @param $orderNo
   *
   * @return Order
   */
  private function getOrderByNo($orderNo, $ignore_status = false) {
    
    /** @var OrderStatus $pendingOrderStatus */
    $pendingOrderStatus = $this->orderStatusRepository->find(OrderStatus::PENDING);

    $outstandingPaymentStatus = $this->paymentStatusRepository->find(PaymentStatus::OUTSTANDING);

    /** @var Order $Order */
    $condition = [
      'order_no' => $orderNo,
    ];

    if($ignore_status === false){
      $condition['OrderStatus'] = $pendingOrderStatus;
      $condition['BpmLinkPaymentPaymentStatus'] = $outstandingPaymentStatus;
    }

    $Order = $this->orderRepository->findOneBy($condition);

    return $Order;
  }

  /**
   * 注文番号で受注を検索する.
   *
   * @param $preOrderId
   *
   * @return Order
   */
  private function getOrderByPreOrderId($preOrderId) {
    /** @var OrderStatus $pendingOrderStatus */
    $pendingOrderStatus = $this->orderStatusRepository->find(OrderStatus::PENDING);

    $outstandingPaymentStatus = $this->paymentStatusRepository->find(PaymentStatus::OUTSTANDING);

    /** @var Order $Order */
    $Order = $this->orderRepository->findOneBy([
      'pre_order_id' => $preOrderId,
      'OrderStatus' => $pendingOrderStatus,
      'BpmLinkPaymentPaymentStatus' => $outstandingPaymentStatus,
    ]);

    return $Order;
  }
}