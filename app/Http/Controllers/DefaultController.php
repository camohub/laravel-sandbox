<?php

namespace App\Http\Controllers;


class DefaultController extends Controller
{

	public function index()
	{
		return view('default.index', ['test' => 'aaaaaaaaaaaaaaaaaaaaaaa']);
	}

}
