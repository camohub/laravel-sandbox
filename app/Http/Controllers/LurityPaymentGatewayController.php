<?php

namespace App\Http\Controllers;


use Braintree\Gateway;
use Braintree\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LurityPaymentGatewayController extends Controller
{

	protected $transactionSuccessStatuses = [
		Transaction::AUTHORIZED,
		Transaction::AUTHORIZING,
		Transaction::SETTLED,
		Transaction::SETTLING,
		Transaction::SETTLEMENT_CONFIRMED,
		Transaction::SETTLEMENT_PENDING,
		Transaction::SUBMITTED_FOR_SETTLEMENT
	];


	public function index()
	{
		return view('lurityPaymentGateway.index', []);
	}


	public function clientToken()
	{
		$gateway = $this->createGateway();
		$clientToken = $gateway->clientToken()->generate();
		/*$clientToken = $gateway->clientToken()->generate(["customerId" => $aCustomerId]);*/

		Log::debug($clientToken);

		return response()->json(['clientToken' => $clientToken]);
	}


	public function checkout(Request $request)
	{
		$gateway = $this->createGateway();

		$amount = number_format((float)$request->get('amount'), 2, '.', '');
		$paymentMethodNonce = $request->get('payment_method_nonce');

		$result = $gateway->transaction()->sale([
			'amount' => $amount,
			'paymentMethodNonce' => $paymentMethodNonce,
			//'deviceData' => $deviceDataFromTheClient,
			'options' => [
				'submitForSettlement' => True
			]
		]);

		if ($result->success || !is_null($result->transaction))
		{
			$transaction = $result->transaction;
			return redirect()->route('gateway-transaction', ['id' => $transaction->id]);
		}
		else
		{
			$errors = [];
			foreach($result->errors->deepAll() as $error) $errors[] = "Error: {$error->code}: {$error->message}.";

			return response()->json(['errors' => $errors]);
		}
	}


	public function transaction($id)
	{
		$gateway = $this->createGateway();
		$transaction = $gateway->transaction()->find($id);

		if (in_array($transaction->status, $this->transactionSuccessStatuses))
		{
			$result = [];
			$result['success'] = 'Your test transaction has been successfully processed. See the Braintree API response and try again.';

			$result['transaction'] = [
				'id' => $transaction->id,
				'type' => $transaction->type,
				'amount' => $transaction->amount,
				'status' => $transaction->status,
				'createdAt' => $transaction->createdAt->format('Y-m-d H:i:s'),
				'updatedAt' => $transaction->updatedAt->format('Y-m-d H:i:s'),
			];

			$result['creditCardDetails'] = [
				'token' => $transaction->creditCardDetails->token,
				'bin' => $transaction->creditCardDetails->bin,
				'last4' => $transaction->creditCardDetails->last4,
				'cardType' => $transaction->creditCardDetails->cardType,
				'cardHolderName' => $transaction->creditCardDetails->cardholderName,
				'expirationDate' => $transaction->creditCardDetails->expirationDate,
				'customerLocation' => $transaction->creditCardDetails->customerLocation,
			];

			if( !is_null($transaction->customerDetails->id) )
			{
				$result['customerDetail'] = [
					'id' => $transaction->customerDetails->id,
					'firstName' => $transaction->customerDetails->firstName,
					'lastName' => $transaction->customerDetails->lastName,
					'email' => $transaction->customerDetails->email,
					'company' => $transaction->customerDetails->company,
					'website' => $transaction->customerDetails->website,
					'phone' => $transaction->customerDetails->phone,
					'fax' => $transaction->customerDetails->fax,
				];
			}

			return response()->json(['transactionDetails' => $result]);
		}
		else
		{
			return response()->json(['errors' => ["Your test transaction has a status of " . $transaction->status . ". See the Braintree API response and try again."]]);
		}
	}


	protected function createGateway()
	{
		$gateway = new Gateway([
			'environment' => 'sandbox',
			'merchantId' => config('braintree-gateway.gateway-merchantId'),
			'publicKey' => config('braintree-gateway.gateway-publicKey'),
			'privateKey' => config('braintree-gateway.gateway-privateKey'),
		]);

		return $gateway;
	}


}
