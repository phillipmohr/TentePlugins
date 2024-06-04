<?php declare(strict_types=1);

namespace Nwgncy\TenteTheme\Storefront\Controller;
 
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Swag\CmsExtensions\Form\Route\AbstractFormRoute;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Nwgncy\CrmConnector\Service\CrmService;


#[Route(defaults: ['_routeScope' => ['storefront']])]
class CustomFormController extends StorefrontController
{
    public function __construct(
        AbstractFormRoute $formRoute,
        CrmService $crmService
    ) {
        $this->formRoute = $formRoute;
        $this->crmService = $crmService;
    }

    #[Route(path: '/nwgncy/tente-theme/form', name: 'frontend.nwgncy.tente-theme.form.send', methods: ['POST'], defaults: ['XmlHttpRequest' => true, '_captcha' => true])]
    public function sendForm(RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $response = [];

        $crmData = $data->all();

        $data->set('country', '018a661385287098beb972cab59280bb');
        $data->set('language', '018a6613851372a1b113d4f1a582146f');
        $data->set('function', '018a6613851372a1b113d4f1a582146f');

        try {
            $message = $this->formRoute
                ->send($data->toRequestDataBag(), $context)
                ->getResult()
                ->getSuccessMessage();

            $result = $this->crmService->processCustomFormEvent($context, $crmData);

            if ($result) {
                
                if (!$message) {
                    $message = $this->trans('contact.success');
                }
    
                $response = [
                    'redirect' => 'true',
                    'type' => 'success',
                    'alert' => $message,
                ];
                
            } else {
                $response[] = [
                    'type' => 'danger',
                    'alert' => $this->trans('customFormRequestError.message')
                ];
            }

        } catch (ConstraintViolationException $formViolations) {
            $violations = [];
            foreach ($formViolations->getViolations() as $violation) {
                $violations[] = $violation->getMessage();
            }

            $response[] = [
                'type' => 'danger',
                'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                    'type' => 'danger',
                    'list' => $violations,
                ]),
            ];
        }
        return new JsonResponse($response);
    }
}

