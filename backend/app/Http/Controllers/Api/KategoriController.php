<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KategoriController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): AnonymousResourceCollection
    {
        $kategori = Kategori::orderBy('nama', 'asc')->get();

        return KategoriResource::collection($kategori);
    }
}

