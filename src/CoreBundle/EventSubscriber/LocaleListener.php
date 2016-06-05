<?php

namespace CoreBundle\EventSubscriber;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $acceptLanguage = ['fr', 'en'];

        $locale = $request->query->get('_locale', null);

        if (in_array($locale, $acceptLanguage)) {
            $this->defaultLocale = $locale;
        } else {
            $languages = $request->getLanguages();

            foreach ($languages as $language) {
                if (in_array($language, $acceptLanguage)) {
                    $this->defaultLocale = $language;
                    break;
                }
            }
        }

        $request->setLocale($this->defaultLocale);
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 16)),
        );
    }
}
