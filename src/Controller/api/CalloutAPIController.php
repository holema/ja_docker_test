<?php

namespace App\Controller\api;

use App\Helper\JitsiAdminController;
use App\Service\api\CheckAuthorizationService;
use App\Service\Callout\CallOutSessionAPIDialService;
use App\Service\Callout\CallOutSessionAPIHoldService;
use App\Service\Callout\CallOutSessionAPIRemoveService;
use App\Service\Callout\CalloutSessionAPIService;
use App\Service\Lobby\ToModeratorWebsocketService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api/v1/call/out', name: 'callout_api_')]
class CalloutAPIController extends JitsiAdminController
{
    public function __construct(
        ManagerRegistry                      $managerRegistry,
        TranslatorInterface                  $translator,
        LoggerInterface                      $logger,
        ParameterBagInterface                $parameterBag,
        private CalloutSessionAPIService     $calloutSessionAPIService,
        private CallOutSessionAPIDialService $callOutSessionAPIDialService,


    )
    {
        parent::__construct($managerRegistry, $translator, $logger, $parameterBag);
        $this->token = 'Bearer ' . $parameterBag->get('SIP_CALLER_SECRET');
    }


    #[Route('/', name: 'pool')]
    public function index(Request $request): Response
    {
        $check = CheckAuthorizationService::checkHEader($request, $this->token);
        if ($check) {
            return $check;
        }
        $calloutSessions = $this->calloutSessionAPIService->getCalloutPool();
        return new JsonResponse($calloutSessions);
    }

    #[Route('/dial/{calloutSessionId}', name: 'dial')]
    public function dial($calloutSessionId, Request $request): Response
    {
        $check = CheckAuthorizationService::checkHEader($request, $this->token);
        if ($check) {
            return $check;
        }

        $res = $this->callOutSessionAPIDialService->dialSession($calloutSessionId);
        return new JsonResponse($res);
    }

    #[Route('/refuse/{calloutSessionId}', name: 'refuse')]
    public function refuse($calloutSessionId, Request $request, CallOutSessionAPIRemoveService $callOutSessionAPIRemoveService): Response
    {
        $check = CheckAuthorizationService::checkHEader($request, $this->token);
        if ($check) {
            return $check;
        }
        $res = $callOutSessionAPIRemoveService->refuse($calloutSessionId);
        return new JsonResponse($res);
    }

    #[Route('/error/{calloutSessionId}', name: 'error')]
    public function error($calloutSessionId, Request $request, CallOutSessionAPIRemoveService $callOutSessionAPIRemoveService): Response
    {
        $check = CheckAuthorizationService::checkHEader($request, $this->token);
        if ($check) {
            return $check;
        }
        $res = $callOutSessionAPIRemoveService->error($calloutSessionId);
        return new JsonResponse($res);
    }

    #[Route('/timeout/{calloutSessionId}', name: 'timeout')]
    public function timeout($calloutSessionId, Request $request, CallOutSessionAPIHoldService $callOutSessionAPIHoldService): Response
    {
        $check = CheckAuthorizationService::checkHEader($request, $this->token);
        if ($check) {
            return $check;
        }
        $res = $callOutSessionAPIHoldService->timeout($calloutSessionId);
        return new JsonResponse($res);
    }


    #[Route('/later/{calloutSessionId}', name: 'later')]
    public function later($calloutSessionId, Request $request, CallOutSessionAPIHoldService $callOutSessionAPIHoldService): Response
    {
        $check = CheckAuthorizationService::checkHEader($request, $this->token);
        if ($check) {
            return $check;
        }
        $res = $callOutSessionAPIHoldService->later($calloutSessionId);
        return new JsonResponse($res);
    }

    #[Route('/occupied/{calloutSessionId}', name: 'occupied')]
    public function occupied($calloutSessionId, Request $request, CallOutSessionAPIHoldService $callOutSessionAPIHoldService): Response
    {
        $check = CheckAuthorizationService::checkHEader($request, $this->token);
        if ($check) {
            return $check;
        }
        $res = $callOutSessionAPIHoldService->occupied($calloutSessionId);
        return new JsonResponse($res);
    }

}
