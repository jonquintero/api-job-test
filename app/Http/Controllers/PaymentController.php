<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\PaymentData;
use App\Http\Requests\UpsertPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Jobs\PaymentJob;
use App\Models\Payment;
use App\Actions\UpsertPaymentAction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Request as RequestFacade;

class PaymentController extends Controller
{
    public function __construct(private readonly UpsertPaymentAction $upsertPaymentAction)
    {
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return PaymentResource::collection( Payment::query()->with('client')
            ->filter(RequestFacade::only('client'))
            ->paginate(15)
            ->withQueryString());

    }

    /**
     * @param UpsertPaymentRequest $request
     * @return Application|ResponseFactory|\Illuminate\Http\Response
     */
    public function store(UpsertPaymentRequest $request): \Illuminate\Http\Response|Application|ResponseFactory
    {

        $response =  $this->upsert($request, new Payment());

            PaymentJob::dispatch($response);
            return response('su pago esta siendo procesado le avisaremos mediante un email una vez terminado el proceso')
            ->setStatusCode(Response::HTTP_CREATED);
    }

    private function upsert(UpsertPaymentRequest $request, Payment $payment)
    {
        $paymentData = PaymentData::fromRequest($request);

        return $this->upsertPaymentAction->execute($payment, $paymentData);
    }
}
