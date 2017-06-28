<?php

namespace Easybill\ZUGFeRD\Model\Trade;

use Easybill\ZUGFeRD\Model\Trade\Tax\TradeTax;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\AccessorOrder;

/**
 * Class Settlement
 *
 * @AccessorOrder("custom", custom = {"paymentReference", "currency", "paymentMeans", "tradeTaxes", "paymentTerms", "monetarySummation"})
 * @XmlNamespace(uri="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:12", prefix="ram")
 * @XmlNamespace(uri="urn:un:unece:uncefact:data:standard:UnqualifiedDataType:15", prefix="udt")
 * @package Easybill\ZUGFeRD\Model\Trade
 */
class Settlement
{

    /**
     * @var string
     * @Type("string")
     * @XmlElement(cdata = false, namespace = "urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:12")
     * @SerializedName("PaymentReference")
     */
    private $paymentReference = '';

    /**
     * @var string
     * @Type("string")
     * @XmlElement(cdata = false, namespace = "urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:12")
     * @SerializedName("InvoiceCurrencyCode")
     */
    private $currency = 'EUR';

    /**
     * @var PaymentMeans
     * @Type("Easybill\ZUGFeRD\Model\Trade\PaymentMeans")
     * @XmlElement(cdata = false, namespace = "urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:12")
     * @SerializedName("SpecifiedTradeSettlementPaymentMeans")
     */
    private $paymentMeans;

    /**
     * @var TradeTax[]
     * @Type("array<Easybill\ZUGFeRD\Model\Trade\Tax\TradeTax>")
     * @XmlList(inline = true, entry = "ApplicableTradeTax", namespace="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:12")
     */
    private $tradeTaxes = array();

    /**
     * @var MonetarySummation
     * @Type("Easybill\ZUGFeRD\Model\Trade\MonetarySummation")
     * @XmlElement(cdata = false, namespace = "urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:12")
     * @SerializedName("SpecifiedTradeSettlementMonetarySummation")
     */
    private $monetarySummation;

    /**
     * @var PaymentTerms
     * @Type("Easybill\ZUGFeRD\Model\Trade\PaymentTerms")
     * @XmlElement(cdata = false, namespace = "urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:12")
     * @SerializedName("SpecifiedTradePaymentTerms")
     */
    private $paymentTerms;

    /**
     * Settlement constructor.
     *
     * @param string $paymentReference
     * @param string $currency
     */
    public function __construct($paymentReference = '', $currency = 'EUR')
    {
        $this->paymentReference = $paymentReference;
        $this->currency = $currency;
        $this->paymentMeans = new PaymentMeans();
    }

    /**
     * @return string
     */
    public function getPaymentReference()
    {
        return $this->paymentReference;
    }

    /**
     * @param string $paymentReference
     *
     * @return self
     */
    public function setPaymentReference($paymentReference)
    {
        $this->paymentReference = $paymentReference;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return \Easybill\ZUGFeRD\Model\Trade\PaymentMeans
     */
    public function getPaymentMeans()
    {
        return $this->paymentMeans;
    }

    /**
     * @param \Easybill\ZUGFeRD\Model\Trade\PaymentMeans $paymentMeans
     *
     * @return self;
     */
    public function setPaymentMeans(PaymentMeans $paymentMeans)
    {
        $this->paymentMeans = $paymentMeans;
        return $this;
    }

    /**
     * @return \Easybill\ZUGFeRD\Model\Trade\Tax\TradeTax[]
     */
    public function getTradeTaxes()
    {
        return $this->tradeTaxes;
    }

    /**
     * @param \Easybill\ZUGFeRD\Model\Trade\Tax\TradeTax $tradeTax
     *
     * @return self
     */
    public function addTradeTax(TradeTax $tradeTax)
    {
        $this->tradeTaxes[] = $tradeTax;
        return $this;
    }

    /**
     * @return \Easybill\ZUGFeRD\Model\Trade\MonetarySummation
     */
    public function getMonetarySummation()
    {
        return $this->monetarySummation;
    }

    /**
     * @param \Easybill\ZUGFeRD\Model\Trade\MonetarySummation $monetarySummation
     *
     * @return self
     */
    public function setMonetarySummation($monetarySummation)
    {
        $this->monetarySummation = $monetarySummation;
        return $this;
    }

    /**
     * @return \Easybill\ZUGFeRD\Model\Trade\PaymentTerms
     */
    public function getPaymentTerms()
    {
        return $this->paymentTerms;
    }

    /**
     * @param \Easybill\ZUGFeRD\Model\Trade\PaymentTerms $paymentTerms
     *
     * @return self
     */
    public function setPaymentTerms($paymentTerms)
    {
        $this->paymentTerms = $paymentTerms;
        return $this;
    }

}