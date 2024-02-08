<?php declare(strict_types=1);

namespace PosVirtualeBanca;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Util\PluginIdProvider;


class PosVirtualeBanca extends Plugin
{
    public function install(InstallContext $context): void
    {
        $this->addPaymentMethod($context->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        $this->setPaymentMethodIsActive(false, $uninstallContext->getContext());
    }

    public function activate(ActivateContext $activateContext): void
    {
        $this->setPaymentMethodIsActive(true, $activateContext->getContext());
        parent::activate($activateContext);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->setPaymentMethodIsActive(false, $deactivateContext->getContext());
        parent::deactivate($deactivateContext);
    }

    private function addPaymentMethod(Context $context): void
    {
        $paymentMethodExists = $this->getPaymentMethodId();
        if ($paymentMethodExists) {
            return;
        }

        $pluginProvider = $this->container->get(PluginIdProvider::class);
        $pluginId       = $pluginProvider->getPluginIdByBaseClass(get_class($this), $context);

        $siaPaymentData = [
            'handlerIdentifier' => Service\PosVirtualeBanca::class,
            'name'              => 'POS Virtuale Banca',
            'description'       => 'POS Virtuale Banca',
            'pluginId'          => $pluginId,
            'afterOrderEnabled' => true,
        ];

        /** @var EntityRepository $paymentRepository */
        $paymentRepository = $this->container->get('payment_method.repository');
        $paymentRepository->create([$siaPaymentData], $context);
    }

    private function setPaymentMethodIsActive(bool $active, Context $context): void
    {
        /** @var EntityRepository $paymentRepository */
        $paymentRepository = $this->container->get('payment_method.repository');
        $paymentMethodId   = $this->getPaymentMethodId();

        // Payment dost not even exist, so nothing to (de-)activate here
        if (!$paymentMethodId) {
            return;
        }

        $paymentMethod = [
            'id'     => $paymentMethodId,
            'active' => $active,
        ];

        $paymentRepository->update([$paymentMethod], $context);
    }

    private function getPaymentMethodId(): ?string
    {
        /** @var EntityRepository $paymentRepository */
        $paymentRepository = $this->container->get('payment_method.repository');

        // Fetch ID for update
        $paymentCriteria = (new Criteria())->addFilter(new EqualsFilter('handlerIdentifier', Service\PosVirtualeBanca::class));
        return $paymentRepository->searchIds($paymentCriteria, Context::createDefaultContext())->firstId();
    }
}
