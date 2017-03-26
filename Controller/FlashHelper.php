<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\ResourceBundle\Controller;

use Miky\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Miky\Component\Resource\Metadata\MetadataInterface;
use Miky\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;


class FlashHelper implements FlashHelperInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function addSuccessFlash(RequestConfiguration $requestConfiguration, $actionName, ResourceInterface $resource = null)
    {
        $metadata = $requestConfiguration->getMetadata();
        $flashMessage = $requestConfiguration->getFlashMessage($actionName);

        if (false === $flashMessage) {
            return;
        }

        $translatedMessage = $this->translateMessage($flashMessage, $metadata);

        if ($flashMessage === $translatedMessage) {
            $translatedMessage = $this->translateMessage(sprintf('adevis.resource.%s', $actionName), $metadata);
        }

        $this->session->getBag('flashes')->add('success', $translatedMessage);
    }

    /**
     * {@inheritdoc}
     */
    public function addFlashFromEvent(RequestConfiguration $requestConfiguration, ResourceControllerEvent $event)
    {
        $translatedMessage = $this->translator->trans($event->getMessage(), $event->getMessageParameters(), 'flashes');
        $this->session->getBag('flashes')->add($event->getMessageType(), $translatedMessage);
    }

    /**
     * @param string $flashMessage
     * @param MetadataInterface $metadata
     *
     * @return string
     */
    private function translateMessage($flashMessage, MetadataInterface $metadata)
    {
        return $this->translator->trans($flashMessage, ['%resource%' => ucfirst($metadata->getHumanizedName())], 'flashes');
    }
}
