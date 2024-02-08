<?php

declare(strict_types=1);

namespace PosVirtualeBanca\Controller;

use IGFS_CG_API\Init\IgfsCgVerify;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStateHandler;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class PosVirtualeBancaController extends StorefrontController
{
    private EntityRepository   $orderRepository;
    private RouterInterface    $router;
    public SystemConfigService $systemConfigService;

    public function __construct(
        EntityRepository $orderRepository,
        RouterInterface $router,
        SystemConfigService $systemConfigService
    ) {
        $this->orderRepository     = $orderRepository;
        $this->router              = $router;
        $this->systemConfigService = $systemConfigService;
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

    /**
     * @Route("/pos-payment-finalize", name="frontend.pos-payment-finalize", methods={"GET"})
     */
    public function posPaymentFinalize(Request $request, SalesChannelContext $context): Response
    {
        require_once __DIR__ . '/../lib/IGFS_CG_API/Exception/IgfsMissingParException.php';
        require_once __DIR__ . '/../lib/IGFS_CG_API/Level3Info.php';
        require_once __DIR__ . '/../lib/IGFS_CG_API/BaseIgfsCg.php';
        require_once __DIR__ . '/../lib/IGFS_CG_API/IgfsUtils.php';
        require_once __DIR__ . '/../lib/IGFS_CG_API/init/BaseIgfsCgInit.php';
        require_once __DIR__ . '/../lib/IGFS_CG_API/init/IgfsCgInit.php';
        require_once __DIR__ . '/../lib/IGFS_CG_API/init/IgfsCgVerify.php';

        $state       = $request->get('state');
        $orderNumber = $request->get('ORDERID');


        if ($state === "success" && $orderNumber) {
            $settings = $this->getSettings();

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('orderNumber', $orderNumber));
            $order        = $this->orderRepository->search($criteria, $context->getContext())->first();
            $customFields = $order->getCustomFields();
            $finalizeUrl  = $this->router->generate(
                'payment.finalize.transaction',
                ['_sw_payment_token' => $customFields['_sw_payment_token']],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            if (array_key_exists('posVitualBancaPaymentID', $customFields)) {
                $IgfsCgVerify            = new IgfsCgVerify();
                $IgfsCgVerify->serverURL = $settings['url'];
                $IgfsCgVerify->tid       = $settings['tid'];
                $IgfsCgVerify->kSig      = $settings['kSig'];
                $IgfsCgVerify->shopID    = $order->getOrderNumber();
                $IgfsCgVerify->paymentID = $customFields['posVitualBancaPaymentID'];

                $paymentState = $IgfsCgVerify->execute();
                if ($paymentState) {
                    $redirectUrl = $finalizeUrl . '&state=success';
                } else {
                    $redirectUrl = $finalizeUrl . '&state=failed';
                }
            } else {
                $redirectUrl = $finalizeUrl . '&state=failed';
            }
        } else {
            $redirectUrl = $this->router->generate('frontend.home.page');
        }

        return $redirectUrl ? $this->redirect($redirectUrl) : $this->redirectToRoute('frontend.home.page');
    }
}
