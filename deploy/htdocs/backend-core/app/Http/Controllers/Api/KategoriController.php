<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KategoriController extends Controller
{
    /** Daftar semua kategori. */
    public function index(): AnonymousResourceCollection
    {
        return KategoriResource::collection(Kategori::orderBy('nama')->get());
    }
}
