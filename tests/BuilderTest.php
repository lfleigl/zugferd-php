<?php

namespace Easybill\ZUGFeRD\Tests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Easybill\ZUGFeRD\Builder;
use Easybill\ZUGFeRD\Model\Address;
use Easybill\ZUGFeRD\Model\AllowanceCharge;
use Easybill\ZUGFeRD\Model\Date;
use Easybill\ZUGFeRD\Model\Document;
use Easybill\ZUGFeRD\Model\Note;
use Easybill\ZUGFeRD\Model\Trade\Amount;
use Easybill\ZUGFeRD\Model\Trade\BillingPeriod;
use Easybill\ZUGFeRD\Model\Trade\CreditorFinancialAccount;
use Easybill\ZUGFeRD\Model\Trade\CreditorFinancialInstitution;
use Easybill\ZUGFeRD\Model\Trade\Delivery;
use Easybill\ZUGFeRD\Model\Trade\Item\LineDocument;
use Easybill\ZUGFeRD\Model\Trade\Item\LineItem;
use Easybill\ZUGFeRD\Model\Trade\Item\Price;
use Easybill\ZUGFeRD\Model\Trade\Item\Product;
use Easybill\ZUGFeRD\Model\Trade\Item\Quantity;
use Easybill\ZUGFeRD\Model\Trade\Item\SpecifiedTradeAgreement;
use Easybill\ZUGFeRD\Model\Trade\Item\SpecifiedTradeDelivery;
use Easybill\ZUGFeRD\Model\Trade\Item\SpecifiedTradeMonetarySummation;
use Easybill\ZUGFeRD\Model\Trade\Item\SpecifiedTradeSettlement;
use Easybill\ZUGFeRD\Model\Trade\MonetarySummation;
use Easybill\ZUGFeRD\Model\Trade\PaymentMeans;
use Easybill\ZUGFeRD\Model\Trade\PaymentTerms;
use Easybill\ZUGFeRD\Model\Trade\ReferencedDocument;
use Easybill\ZUGFeRD\Model\Trade\Settlement;
use Easybill\ZUGFeRD\Model\Trade\Tax\TaxRegistration;
use Easybill\ZUGFeRD\Model\Trade\Tax\TradeTax;
use Easybill\ZUGFeRD\Model\Trade\Trade;
use Easybill\ZUGFeRD\Model\Trade\TradeParty;
use Easybill\ZUGFeRD\SchemaValidator;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{

    /**
     * @before
     */
    public function setupAnnotationRegistry(): void
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    public function testGetXML(): void
    {
        $doc = new Document(Document::TYPE_COMFORT);
        $doc->getHeader()
            ->setId('RE1337')
            ->setName('RECHNUNG')
            ->setDate(new Date(new \DateTime('20130305'), 102))
            ->addNote(new Note('Test Node 1'))
            ->addNote(new Note('Test Node 2'))
            ->addNote(new Note('easybill GmbH
            Düsselstr. 21
            41564 Kaarst
            
            Geschäftsführer:
            Christian Szardenings
            Ronny Keyser', 'REG'));


        $trade = $doc->getTrade();

        $trade->setDelivery(new Delivery('20130305', 102));

        $this->setAgreement($trade);
        $this->setLineItem($trade);
        $this->setSettlement($trade);

        $builder = Builder::create();
        $xml = $builder->getXML($doc);

        // file_put_contents(__DIR__ . '/builder.zugferd.xml', $xml);
        $this->assertStringEqualsFile(__DIR__ . '/builder.zugferd.xml', $xml);

        SchemaValidator::isValid($xml);
    }

    private function setAgreement(Trade $trade): void
    {
        $trade->getAgreement()
            ->setBuyerReference('AB-312')
            ->setSeller(
                new TradeParty('Lieferant GmbH',
                    new Address('80333', 'Lieferantenstraße 20', null, 'München', 'DE'),
                    [
                        new TaxRegistration('FC', '201/113/40209'),
                        new TaxRegistration('VA', 'DE123456789'),
                    ]
                )
            )->setBuyer(
                new TradeParty('Kunden AG Mitte',
                    new Address('69876', 'Hans Muster', 'Kundenstraße 15', 'Frankfurt', 'DE')
                )
            )->setBuyerOrder(new ReferencedDocument('0234587234'));
    }

    private function setLineItem(Trade $trade): void
    {
        $tradeAgreement = new SpecifiedTradeAgreement();

        $grossPrice = new Price(9.90, 'EUR', false);
        $grossPrice
            ->addAllowanceCharge(new AllowanceCharge(false, 1.80));

        $tradeAgreement->setGrossPrice($grossPrice);
        $tradeAgreement->setNetPrice(new Price(9.90, 'EUR', false));

        $lineItemTradeTax = new TradeTax();
        $lineItemTradeTax->setCode('VAT');
        $lineItemTradeTax->setPercent(19.00);
        $lineItemTradeTax->setCategory('S');

        $lineItemSettlement = new SpecifiedTradeSettlement();
        $lineItemSettlement
            ->setTradeTax($lineItemTradeTax)
            ->setMonetarySummation(new SpecifiedTradeMonetarySummation(198.00));

        $lineItem = new LineItem();
        $lineItem
            ->setTradeAgreement($tradeAgreement)
            ->setDelivery(new SpecifiedTradeDelivery(new Quantity('C62', 20.00)))
            ->setSettlement($lineItemSettlement)
            ->setProduct(new Product('TB100A4', 'Trennblätter A4'))
            ->setLineDocument(new LineDocument('1'))
            ->getLineDocument()
            ->addNote(new Note('Testcontent in einem LineDocument'));

        $trade->addLineItem($lineItem);
    }

    private function setSettlement(Trade $trade): void
    {
        $settlement = new Settlement('2013-471102', 'EUR');
        $settlement->setPaymentTerms(new PaymentTerms('Zahlbar innerhalb von 20 Tagen (bis zum 05.10.2016) unter Abzug von 3% Skonto (Zahlungsbetrag = 1.766,03 €). Bis zum 29.09.2016 ohne Abzug.', new Date('20130404')));

        $settlement->setPaymentMeans(new PaymentMeans());
        $settlement->getPaymentMeans()
            ->setCode('31')
            ->setInformation('Überweisung')
            ->setPayeeAccount(new CreditorFinancialAccount('DE08700901001234567890', '', ''))
            ->setPayeeInstitution(new CreditorFinancialInstitution('GENODEF1M04', '', ''));

        $tradeTax = new TradeTax();
        $tradeTax->setCode('VAT');
        $tradeTax->setPercent(7.00);
        $tradeTax->setBasisAmount(new Amount(275.00, 'EUR'));
        $tradeTax->setCalculatedAmount(new Amount(19.25, 'EUR'));

        $tradeTax2 = new TradeTax();
        $tradeTax2->setCode('VAT');
        $tradeTax2->setPercent(19.00);
        $tradeTax2->setBasisAmount(new Amount(198.00, 'EUR'));
        $tradeTax2->setCalculatedAmount(new Amount(37.62, 'EUR'));

        $settlement
            ->addTradeTax($tradeTax)
            ->addTradeTax($tradeTax2)
            ->addAllowanceCharge(
                (new AllowanceCharge(false, 1, 'EUR', true))
                    ->setReason('Sondernachlass')
                    ->addCategoryTradeTax(
                        (new TradeTax())
                            ->setCode('VAT')
                            ->setCategory('S')
                            ->setPercent(19)
                    )
            )
            ->setMonetarySummation(
                new MonetarySummation(198.00, 0.00, 0.00, 198.00, 37.62, 235.62, 'EUR')
            );

        $billingPeriod = new BillingPeriod(
            new Date('20130104'),
            new Date('20130204')
        );
        $settlement->setBillingPeriod($billingPeriod);

        $trade->setSettlement($settlement);
    }

}
