<?php

namespace Plugin\BpmLinkPayment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\Order")
 */
trait OrderTrait {

  /**
   * 決済ステータスを保持するカラム.
   *
   * dtb_order.bmp_link_payment_payment_status_id
   *
   * @var BpmLinkPaymentPaymentStatus
   * @ORM\ManyToOne(targetEntity="Plugin\BpmLinkPayment\Entity\PaymentStatus")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="bpm_link_payment_payment_status_id", referencedColumnName="id")
   * })
   */
  private $BpmLinkPaymentPaymentStatus;


  /**
   * 決済完了時のBPMが発行する承認番号を保持するカラム.
   *
   * dtb_order.bpm_link_payment_tran_code
   *
   * @var BpmLinkPaymentTranCode
   * @ORM\Column(name="bpm_link_payment_tran_code", type="string", length=255, nullable=true)
   */
  private $BpmLinkPaymentTranCode;

  /**
   * 決済完了時のクレジット決済明細票記名を保持するカラム.
   *
   * dtb_order.bpm_link_payment_dba
   *
   * @var BpmLinkPaymentDba
   * @ORM\Column(name="bpm_link_payment_dba", type="string", length=255, nullable=true)
   */
  private $BpmLinkPaymentDba;


  /**
   * @return PaymentStatus
   */
  public function getBpmLinkPaymentPaymentStatus()
  {
    return $this->BpmLinkPaymentPaymentStatus;
  }

  /**
   * @param PaymentStatus $BpmLinkPaymentPaymentStatus|null
   */
  public function setBpmLinkPaymentPaymentStatus(PaymentStatus $BpmLinkPaymentPaymentStatus = null)
  {
      $this->BpmLinkPaymentPaymentStatus = $BpmLinkPaymentPaymentStatus;
  }


  public function getBpmLinkPaymentTranCode(){
    return $this->BpmLinkPaymentTranCode;
  }

  public function setBpmLinkPaymentTranCode($BpmLinkPaymentTranCode = null){
    $this->BpmLinkPaymentTranCode = $BpmLinkPaymentTranCode;
  }


  public function getBpmLinkPaymentDba(){
    return $this->BpmLinkPaymentDba;
  }

  public function setBpmLinkPaymentDba($BpmLinkPaymentDba = null){
    $this->BpmLinkPaymentDba = $BpmLinkPaymentDba;
  }
}