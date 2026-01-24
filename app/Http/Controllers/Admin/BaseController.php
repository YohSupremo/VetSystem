<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    /**
     * The layout that should be used for responses.
     */
    protected $layout = 'admin.layouts.app';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Get the authenticated user.
     */
    protected function user()
    {
        return Auth::user();
    }
}