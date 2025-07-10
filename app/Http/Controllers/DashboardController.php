<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Location;
use App\Models\Mutation;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $productCount = Product::count();
        $locationCount = Location::count();
        $mutationCount = Mutation::count();
        $userCount = User::count();
        // Optionally, fetch recent mutations or logs
        $recentMutations = Mutation::with(['user'])->latest()->take(5)->get();

        return view('dashboard', [
            'productCount' => $productCount,
            'locationCount' => $locationCount,
            'mutationCount' => $mutationCount,
            'userCount' => $userCount,
            'recentMutations' => $recentMutations,
        ]);
    }
} 