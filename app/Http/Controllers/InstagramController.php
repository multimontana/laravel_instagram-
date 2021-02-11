<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstagramRequest;
use App\Services\InstagramService;
use Illuminate\Http\JsonResponse;


class InstagramController extends Controller
{
    /**
     * @var InstagramService
     */
    private InstagramService $instagramService;

    public function __construct(InstagramService $instagramService)
    {
        $this->instagramService = $instagramService;
    }

    public function index($id = '4997563433')
    {
        return view('instagram.index', ['id' => $id]);
    }

    public function getUserInfo(InstagramRequest $request)
    {
        $data = $this->instagramService
            ->getUserInfo($request->all());
        return response()
            ->json(['data' => $data], JsonResponse::HTTP_CREATED);
    }
}
