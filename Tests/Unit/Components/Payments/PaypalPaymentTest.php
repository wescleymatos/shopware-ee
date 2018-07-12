<?php
/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by Wirecard AG and are explicitly not part
 * of the Wirecard AG range of products and services.
 *
 * They have been tested and approved for full functionality in the standard configuration
 * (status on delivery) of the corresponding shop system. They are under General Public
 * License version 3 (GPLv3) and can be used, developed and passed on to third parties under
 * the same terms.
 *
 * However, Wirecard AG does not provide any guarantee or accept any liability for any errors
 * occurring when used in an enhanced, customized shop system configuration.
 *
 * Operation in an enhanced, customized configuration is at your own risk and requires a
 * comprehensive test phase by the user of the plugin.
 *
 * Customers use the plugins at their own risk. Wirecard AG does not guarantee their full
 * functionality neither does Wirecard AG assume liability for any disadvantages related to
 * the use of the plugins. Additionally, Wirecard AG does not guarantee the full functionality
 * for customized shop systems or installed plugins of other vendors of plugins within the same
 * shop system.
 *
 * Customers are responsible for testing the plugin's functionality before starting productive
 * operation.
 *
 * By installing the plugin into the shop system the customer agrees to these terms of use.
 * Please do not use the plugin if you do not agree to these terms of use!
 */

namespace WirecardShopwareElasticEngine\Tests\Unit\Components\Payments;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;
use Shopware\Components\Routing\RouterInterface;
use Shopware\Models\Plugin\Plugin;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Wirecard\PaymentSdk\Config\Config;
use Wirecard\PaymentSdk\Config\PaymentMethodConfig;
use Wirecard\PaymentSdk\Transaction\PayPalTransaction;
use WirecardShopwareElasticEngine\Components\Data\PaymentConfig;
use WirecardShopwareElasticEngine\Components\Payments\PaypalPayment;
use WirecardShopwareElasticEngine\Exception\UnknownTransactionTypeException;
use WirecardShopwareElasticEngine\Tests\Unit\PaymentTestCase;
use WirecardShopwareElasticEngine\WirecardShopwareElasticEngine;

class PaypalPaymentTest extends PaymentTestCase
{
    /** @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $em;

    /** @var \Shopware_Components_Config|\PHPUnit_Framework_MockObject_MockObject $config */
    private $config;

    /** @var InstallerService|\PHPUnit_Framework_MockObject_MockObject $config */
    private $installer;

    /** @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject $config */
    private $router;

    /** @var PaypalPayment */
    protected $payment;

    public function setUp()
    {
        $this->em        = $this->createMock(EntityManagerInterface::class);
        $this->config    = $this->createMock(\Shopware_Components_Config::class);
        $this->installer = $this->createMock(InstallerService::class);
        $this->router    = $this->createMock(RouterInterface::class);

        $plugin = $this->createMock(Plugin::class);
        $this->installer->method('getPluginByName')->willReturn($plugin);

        $this->config->method('getByNamespace')->willReturnMap([
            [WirecardShopwareElasticEngine::NAME, 'wirecardElasticEngine' . 'PaypalMerchantId', null, 'MAID'],
            [WirecardShopwareElasticEngine::NAME, 'wirecardElasticEngine' . 'PaypalSecret', null, 'Secret'],
        ]);
        $this->payment = new PaypalPayment($this->em, $this->config, $this->installer, $this->router);
    }

    public function testGetPaymentOptions()
    {
        $this->assertEquals('Wirecard PayPal', $this->payment->getLabel());
        $this->assertEquals('wirecard_elastic_engine_paypal', $this->payment->getName());
        $this->assertPaymentOptions($this->payment->getPaymentOptions(), 'wirecard_elastic_engine_paypal',
            'Wirecard PayPal', 1);
    }

    public function testGetTransaction()
    {
        $this->assertInstanceOf(PayPalTransaction::class, $this->payment->getTransaction());
    }

    public function testGetPaymentConfig()
    {
        $config = $this->payment->getPaymentConfig();

        $this->assertInstanceOf(PaymentConfig::class, $config);
        $this->assertNull($config->getBaseUrl());
        $this->assertNull($config->getHttpUser());
        $this->assertNull($config->getHttpPassword());
    }

    public function testGetTransactionConfig()
    {
        /** @var Shop|\PHPUnit_Framework_MockObject_MockObject $shop */
        /** @var ParameterBagInterface|\PHPUnit_Framework_MockObject_MockObject $parameters */

        $shop       = $this->createMock(Shop::class);
        $parameters = $this->createMock(ParameterBagInterface::class);

        $config = $this->payment->getTransactionConfig($shop, $parameters);

        $this->assertInstanceOf(Config::class, $config);
        $this->assertNull($config->getBaseUrl());
        $this->assertNull($config->getHttpUser());
        $this->assertNull($config->getHttpPassword());
        $this->assertInstanceOf(PaymentMethodConfig::class, $config->get(PayPalTransaction::NAME));
        $this->assertEquals('MAID', $config->get(PayPalTransaction::NAME)->getMerchantAccountId());
        $this->assertEquals('Secret', $config->get(PayPalTransaction::NAME)->getSecret());
    }

    public function testGetTransactionType()
    {
        $this->expectException(UnknownTransactionTypeException::class);
        $this->assertEquals('', $this->payment->getTransactionType());
    }
}
