<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestFramework\DataFaker;

class VindiciaRefundItemTest extends TestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->sku = $this->faker->randomCharacters(
            DataFaker::ALPHABET_LOWER,
            $this->faker->intBetween(3, 15)
        );
        $currency = $this->faker->currency();
        $this->item = $this->faker->refundItem($currency);
    }

    public function testConstructWithParams()
    {
        $item = new VindiciaRefundItem(array('sku' => $this->sku));
        $this->assertSame($this->sku, $item->getSku());
    }

    public function testInitializeWithParams()
    {
        $item = new VindiciaRefundItem();
        $this->assertSame($item, $item->initialize(array('sku' => $this->sku)));
        $this->assertSame($this->sku, $item->getSku());
    }

    public function testGetParameters()
    {
        $item = new VindiciaRefundItem();
        $this->assertSame($item, $item->setSku($this->sku));
        $this->assertSame(array('sku' => $this->sku), $item->getParameters());
    }

    public function testSku()
    {
        $item = new VindiciaRefundItem();
        $this->assertSame($item, $item->setSku($this->sku));
        $this->assertSame($this->sku, $item->getSku());
    }

    public function testAmount()
    {
        $item = new VindiciaRefundItem();
        $amount = $this->faker->monetaryAmount($this->faker->currency());
        $this->assertSame($item, $item->setAmount($amount));
        $this->assertSame($amount, $item->getAmount());
    }

    public function testTransactionItemIndexNumber()
    {
        $item = new VindiciaRefundItem();
        $transactionItemIndexNumber = $this->faker->intBetween(1, 15);
        $this->assertSame($item, $item->setTransactionItemIndexNumber($transactionItemIndexNumber));
        $this->assertSame($transactionItemIndexNumber, $item->getTransactionItemIndexNumber());
    }

    public function testTaxOnly()
    {
        $item = new VindiciaRefundItem();
        $taxOnly = $this->faker->bool();
        $this->assertSame($item, $item->setTaxOnly($taxOnly));
        $this->assertSame($taxOnly, $item->getTaxOnly());
    }

    /**
     * @expectedException \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Refund item requires amount if taxOnly is not set to true.
     */
    public function testValidateAmountRequired()
    {
        $this->item->setTaxOnly(false);
        $this->item->setAmount(null);
        $this->item->validate();
    }

    /**
     * @expectedException \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Refund item requires sku or transactionItemIndexNumber.
     */
    public function testValidateSkuOrTransactionItemIndexNumberRequired()
    {
        $this->item->setSku(null);
        $this->item->setTransactionItemIndexNumber(null);
        $this->item->validate();
    }
}
