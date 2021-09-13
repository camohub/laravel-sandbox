<?php

namespace App\Http\Controllers;


use App\Models\Services\ORSRService;


class LurityORSRController extends Controller
{

	public function index($ico, ORSRService $ORSRService)
	{
		$ico = trim($ico);
		$ico = htmlspecialchars($ico);
		$ico = str_replace(' ', '', $ico);

		$ORSRService->setOutputFormat('json');
		$result = $ORSRService->getDetailByICO($ico);

		return response()->json(['result' => $result]);
	}




}
