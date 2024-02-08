<?php

declare(strict_types=1);

namespace PosVirtualeBanca\Service;

use IGFS_CG_API\Init\IgfsCgInit;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStateHandler;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Payment\Cart\AsyncPaymentTransactionStruct;
use Shopware\Core\Checkout\Payment\Cart\PaymentHandler\AsynchronousPaymentHandlerInterface;
use Shopware\Core\Checkout\Payment\Exception\AsyncPaymentProcessException;
use Shopware\Core\Checkout\Payment\Exception\CustomerCanceledAsyncPaymentException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;


class PosVirtualeBanca implements AsynchronousPaymentHandlerInterface
{

    private OrderTransactionStateHandler $transactionStateHandler;
    public SystemConfigService           $systemConfigService;
    private EntityRepository             $languageRepository;
    private EntityRepository             $orderRepository;
    private RouterInterface              $router;

    public function __construct(
        OrderTransactionStateHandler $transactionStateHandler,
        SystemConfigService $systemConfigService,
        EntityRepository $languageRepository,
        EntityRepository $orderRepository,
        RouterInterface $router
    ) {
        $this->transactionStateHandler = $transactionStateHandler;
        $this->systemConfigService     = $systemConfigService;
        $this->languageRepository      = $languageRepository;
        $this->orderRepository         = $orderRepository;
        $this->router                  = $router;
    }

    public function getSettings(): array
    {
        $config             = $this->systemConfigService->get('PosVirtualeBanca.config');
        $settings['trType'] = 'AUTH';
        if ($config['IfgsUseSandbox']) {
            $settings['tid']  = $config['sandboxTerminalID'];
            $settings['kSig'] = $config['sandboxSignature'];
            $settings['url']  = $config['sandboxPaymentServerUrl'];
        } else {
            $settings['tid']  = $config['terminalID'];
            $settings['kSig'] = $config['signature'];
            $settings['url']  = $config['paymentServerUrl'];
        }

        return $settings;
    }

    public function pay(AsyncPaymentTransactionStruct $transaction, RequestDataBag $dataBag, SalesChannelContext $salesChannelContext): RedirectResponse
    {
        $settings = $this->getSettings();

        require_once __DIR__ . '/../lib/IGFS_CG_API/BaseIgfsCg.php';
        require_once __DIR__ . '/../lib/IGFS_CG_API/IgfsUtils.php';
        require_once __DIR__ . '/../lib/IGFS_CG_API/init/BaseIgfsCgInit.php';
        require_once __DIR__ . '/../lib/IGFS_CG_API/init/IgfsCgInit.php';

        $order         = $transaction->getOrder();
        $orderCustomer = $order->getOrderCustomer();
        $total         = number_format($order->getAmountTotal(), 2, '', '');
        $language      = $order->getLanguage();
        $langIsoCode   = "EN";
        if ($language->getName() == 'Deutsch') {
            $langIsoCode = "DE";
        } elseif ($language->getName() == 'Italiano' || $language->getName() == 'Italienisch') {
            $langIsoCode = "IT";
        }
        $urlBack = $this->getPaymentFinalizeUrl() . '?state=canceled&ORDERID=' . $order->getOrderNumber();
        $urlDone = $this->getPaymentFinalizeUrl() . '?state=success&ORDERID=' . $order->getOrderNumber();

        $igfsCgInit               = new IgfsCgInit();
        $igfsCgInit->serverURL    = $settings['url'];
        $igfsCgInit->tid          = $settings['tid'];
        $igfsCgInit->kSig         = $settings['kSig'];
        $igfsCgInit->shopID       = $order->getOrderNumber();
        $igfsCgInit->shopUserRef  = $orderCustomer->getEmail();
        $igfsCgInit->trType       = $settings['trType'];
        $igfsCgInit->currencyCode = $order->getCurrency()->getIsoCode();
        $igfsCgInit->amount       = $total; //Amount without comma, 1,00EUR will be 100
        $igfsCgInit->langID       = $langIsoCode; //Language iso code
        $igfsCgInit->notifyURL    = $urlDone;
        $igfsCgInit->errorURL     = $urlBack;


        if (!$igfsCgInit->execute()) {
            $redirectUrl = $igfsCgInit->rc;
        } else {
            $redirectUrl = $igfsCgInit->redirectURL;
        }

        $customFields = $order->getCustomFields();
        if ($customFields == null) {
            $customFields = [];
        }

        $transactionUrl = $transaction->getReturnUrl();
        $queryString    = parse_url($transactionUrl, PHP_URL_QUERY);
        parse_str($queryString, $queryParams);
        $customFields = array_merge($customFields, ['_sw_payment_token' => $queryParams['_sw_payment_token']]);

        if ($igfsCgInit->paymentID) {
            $newCustomFields = array_merge($customFields, ['posVitualBancaPaymentID' => $igfsCgInit->paymentID]);
        }

        $this->orderRepository->update(
            [
                [
                    'id'           => $order->getId(),
                    'customFields' => $newCustomFields,
                ],
            ],
            $salesChannelContext->getContext()
        );

        return new RedirectResponse($redirectUrl);
    }

    private function getPaymentFinalizeUrl()
    {
        return $this->router->generate('frontend.pos-payment-finalize', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function finalize(AsyncPaymentTransactionStruct $transaction, Request $request, SalesChannelContext $salesChannelContext): void
    {
        $paymentState  = $request->query->get('state');
        $context       = $salesChannelContext->getContext();
        $transactionId = $transaction->getOrderTransaction()->getId();

        if ($paymentState == 'canceled') {
            $this->transactionStateHandler->cancel($transaction->getOrderTransaction()->getId(), $context);
            throw new CustomerCanceledAsyncPaymentException(
                $transactionId, 'Customer canceled the payment on the SIA page'
            );
        }

        if ($paymentState == 'failed') {
            $this->transactionStateHandler->fail($transaction->getOrderTransaction()->getId(), $context);
            throw new CustomerCanceledAsyncPaymentException(
                $transactionId, 'Customer failed the payment on the SIA page'
            );
        }

        if ($paymentState === 'success') {
            // Payment completed, set transaction status to "paid"
            $this->transactionStateHandler->paid($transaction->getOrderTransaction()->getId(), $context);
        } else {
            // Payment not completed, set transaction status to "open"
            $this->transactionStateHandler->reopen($transaction->getOrderTransaction()->getId(), $context);
        }
    }
}
