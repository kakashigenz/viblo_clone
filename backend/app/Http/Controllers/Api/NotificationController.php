<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = auth()->guard()->user();
        $res = $this->service->getList($user);
        return $res;
    }

    public function markAllRead()
    {
        $user = auth()->guard()->user();
        $this->service->markAllRead($user);
        return ['message' => 'success'];
    }

    public function markAsRead(string $id)
    {
        $user = auth()->guard()->user();
        $this->service->markAsRead($user, $id);
        return ['message' => 'success'];
    }
}
