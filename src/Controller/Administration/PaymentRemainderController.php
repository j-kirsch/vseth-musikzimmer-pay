<?php

/*
 * This file is part of the vseth-semesterly-reports project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Administration;

use App\Controller\Administration\Base\BaseController;
use App\Entity\PaymentRemainder;
use App\Model\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/payment_remainder")
 */
class PaymentRemainderController extends BaseController
{
    /**
     * @Route("/new", name="administration_payment_remainder_new")
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function newAction(Request $request, TranslatorInterface $translator)
    {
        $paymentRemainder = new PaymentRemainder();
        $paymentRemainder->setName($translator->trans('default.name', [], 'entity_payment_remainder'));
        $paymentRemainder->setSubject($translator->trans('default.subject', [], 'entity_payment_remainder'));
        $paymentRemainder->setBody($translator->trans('default.body', ['support_email' => $this->getParameter('REPLY_EMAIL')], 'entity_payment_remainder'));

        $paymentRemainder->setFee(0);
        $paymentRemainder->setDueAt((new \DateTime())->add(new \DateInterval('P1M')));

        //process form
        $saved = false;
        $myForm = $this->handleCreateForm(
            $request,
            $paymentRemainder,
            function () use ($paymentRemainder, $translator, &$saved) {
                if (!$this->ensureValidPaymentRemainder($paymentRemainder, $translator)) {
                    return false;
                }

                $saved = true;

                return true;
            }
        );
        if ($myForm instanceof Response) {
            return $myForm;
        }

        if ($saved) {
            return $this->redirectToRoute('administration');
        }

        return $this->render('administration/payment_remainder/new.html.twig', ['form' => $myForm->createView()]);
    }

    /**
     * @Route("/{paymentRemainder}/edit", name="administration_payment_remainder_edit")
     *
     * @return Response
     */
    public function editAction(Request $request, PaymentRemainder $paymentRemainder, TranslatorInterface $translator)
    {
        //process form
        $myForm = $this->handleUpdateForm($request, $paymentRemainder, function () use ($paymentRemainder, $translator) {
            return $this->ensureValidPaymentRemainder($paymentRemainder, $translator);
        });

        if ($myForm instanceof Response) {
            return $myForm;
        }

        return $this->render('administration/payment_remainder/edit.html.twig', ['form' => $myForm->createView()]);
    }

    /**
     * @return bool
     */
    private function ensureValidPaymentRemainder(PaymentRemainder $paymentRemainder, TranslatorInterface $translator)
    {
        if (mb_strrpos($paymentRemainder->getBody(), '(url)') === false) {
            $error = $translator->trans('new.error.missing_url', [], 'administration_payment_remainder');
            $this->displayError($error);

            return false;
        }

        return true;
    }

    /**
     * get the breadcrumbs leading to this controller.
     *
     * @return Breadcrumb[]
     */
    protected function getIndexBreadcrumbs()
    {
        return array_merge(parent::getIndexBreadcrumbs(), [
            new Breadcrumb(
                $this->generateUrl('administration'),
                $this->getTranslator()->trans('index.title', [], 'administration')
            ),
        ]);
    }
}
