<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityLogController extends Controller
{
    /**
     * Display the user's activity logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();
        
        // Get the user's activity logs, paginated
        $logs = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('backend.activity.index', [
            'logs' => $logs
        ]);
    }

    /**
     * Store a new activity log via API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'action' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|string|max:255',
            'method' => 'nullable|string|max:50',
            'endpoint' => 'nullable|string|max:255',
            'request_data' => 'nullable|array',
            'entity_data' => 'nullable|array',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the user by token
        $token = $request->input('token');
        $user = $this->getUserByToken($token);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token'
            ], 401);
        }

        // Create a new activity log
        $activityLog = ActivityLog::create([
            'user_id' => $user->id,
            'app_name' => $request->input('app_name'),
            'app_url' => $request->input('app_url'),
            'action' => $request->input('action'),
            'description' => $request->input('description'),
            'method' => $request->input('method', $request->method()),
            'endpoint' => $request->input('endpoint', $request->path()),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_data' => $request->input('request_data'),
            'entity_data' => $request->input('entity_data'),
            'status' => $request->input('status', 'success'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Activity logged successfully',
            'data' => $activityLog
        ], 201);
    }

    /**
     * Get a user by their personal access token.
     *
     * @param  string  $token
     * @return \App\Models\User|null
     */
    protected function getUserByToken($token)
    {
        $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        
        if (!$tokenModel) {
            return null;
        }
        
        return $tokenModel->tokenable;
    }
}
