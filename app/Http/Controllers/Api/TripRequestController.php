<?php

namespace App\Http\Controllers\Api;

use App\Request as RideRequest;
use App\RequestTrip;
use App\RequestWaypoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class TripRequestController extends BaseController
{
    /**
     * POST v1/type-requests/{homeId}/requests-trip
     */
    public function createTripRequest(Request $request, $homeId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no autenticado',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'waypoints' => 'required|array|min:2',
                'waypoints.*.latitude' => 'required|numeric',
                'waypoints.*.longitude' => 'required|numeric',
                'waypoints.*.address' => 'required|string|max:500',
                'waypoints.*.type' => 'required|in:origin,checkpoint,destination',
                'offered_price' => 'required|numeric|min:1',
                'payment_method_id' => 'required|integer|min:0',
                'comments' => 'nullable|string|max:500',
                'filters' => 'nullable|array',
                'config' => 'nullable|array',
                'date' => 'nullable|string|max:20',
                'hour' => 'nullable|string|max:20',
                'is_scheduling' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validación fallida',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();
            $waypoints = $validated['waypoints'];

            if ($waypoints[0]['type'] !== 'origin') {
                return response()->json([
                    'status' => false,
                    'message' => 'El primer punto debe ser origen',
                ], 422);
            }

            if ($waypoints[count($waypoints) - 1]['type'] !== 'destination') {
                return response()->json([
                    'status' => false,
                    'message' => 'El último punto debe ser destino',
                ], 422);
            }

            $createdRequest = null;
            DB::transaction(function () use ($user, $homeId, $validated, $waypoints, &$createdRequest) {
                RideRequest::where('user_id', $user->id)
                    ->whereIn('status', ['REQUIRED', 'ACCEPTED'])
                    ->update(['status' => 'CANCELLED']);

                $requestItem = new RideRequest();
                $requestItem->uuid = md5(uniqid((string)$user->id, true));
                $requestItem->status = 'REQUIRED';
                $requestItem->type_request_id = (int)$homeId;
                $requestItem->user_id = (int)$user->id;
                $requestItem->payment_method_id = (int)$validated['payment_method_id'];
                $requestItem->offered_price = (float)$validated['offered_price'];
                $requestItem->type = 'trip';
                $requestItem->distance = isset($validated['filters']['distance'])
                    ? (string)$validated['filters']['distance']
                    : null;
                $requestItem->requested_rating = isset($validated['filters']['requested_rating'])
                    ? (int)$validated['filters']['requested_rating']
                    : 3;
                $requestItem->taxi_company = isset($validated['filters']['taxi_company'])
                    ? (string)$validated['filters']['taxi_company']
                    : '-1';
                $requestItem->comments = isset($validated['comments']) ? (string)$validated['comments'] : '';
                $requestItem->longitude = (string)$waypoints[0]['longitude'];
                $requestItem->latitude = (string)$waypoints[0]['latitude'];
                $requestItem->is_counterofferable = 0;
                $requestItem->is_scheduling = isset($validated['is_scheduling']) && $validated['is_scheduling'] ? 1 : 0;
                $requestItem->date = isset($validated['date']) ? (string)$validated['date'] : null;
                $requestItem->hour = isset($validated['hour']) ? (string)$validated['hour'] : null;
                $requestItem->save();

                $tripItem = new RequestTrip();
                $tripItem->uuid = md5(uniqid('trip', true));
                $tripItem->parent_id = $requestItem->id;
                $tripItem->number_of_passengers = isset($validated['config']['number_of_passengers'])
                    ? (int)$validated['config']['number_of_passengers']
                    : 1;
                $tripItem->car_with_grill = !empty($validated['config']['car_with_grill']) ? 1 : 0;
                $tripItem->baby_chair = !empty($validated['config']['baby_chair']) ? 1 : 0;
                $tripItem->travel_with_pets = !empty($validated['config']['travel_with_pet']) ? 1 : 0;
                $tripItem->save();

                foreach ($waypoints as $index => $waypoint) {
                    $waypointItem = new RequestWaypoint();
                    $waypointItem->uuid = md5(uniqid('wp', true));
                    $waypointItem->parent_id = $requestItem->id;
                    $waypointItem->type = (string)$waypoint['type'];
                    $waypointItem->address = (string)$waypoint['address'];
                    $waypointItem->order = isset($waypoint['order'])
                        ? (int)$waypoint['order']
                        : ($index + 1);
                    $waypointItem->status = 'holding';
                    $waypointItem->latitude = (string)$waypoint['latitude'];
                    $waypointItem->longitude = (string)$waypoint['longitude'];
                    $waypointItem->save();
                }

                $createdRequest = $requestItem;
            });

            Log::info('Trip request created', [
                'request_id' => $createdRequest->id,
                'user_id' => $user->id,
                'waypoints_count' => count($waypoints),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Solicitud de viaje creada exitosamente',
                'item' => [
                    'request_id' => $createdRequest->id,
                    'uuid' => $createdRequest->uuid,
                    'status' => $createdRequest->status,
                    'waypoints_count' => count($waypoints),
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating trip request', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Error al crear la solicitud de viaje',
            ], 500);
        }
    }

    /**
     * PUT v1/requests/cancel
     */
    public function cancelPendingRequests(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no autenticado',
                ], 401);
            }

            $updated = RideRequest::where('user_id', $user->id)
                ->whereIn('status', ['REQUIRED', 'ACCEPTED'])
                ->update(['status' => 'CANCELLED']);

            return response()->json([
                'status' => true,
                'message' => 'Solicitudes canceladas',
                'cancelled_count' => $updated,
            ]);
        } catch (\Exception $e) {
            Log::error('Error cancelling requests', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Error al cancelar solicitudes',
            ], 500);
        }
    }

    /**
     * GET v1/requests/ride/user/available
     */
    public function getAvailableRides(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no autenticado',
                ], 401);
            }

            // Mantener compatibilidad con Flutter: si no existe un ride listo,
            // devolvemos status=false para que la app no intente parsear un driver_request incompleto.
            return response()->json([
                'status' => false,
                'message' => 'Sin viaje activo',
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching active ride', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Error al obtener viaje activo',
            ], 500);
        }
    }

    /**
     * GET v1/requests/{id}
     */
    public function getTripRequest($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuario no autenticado',
                ], 401);
            }

            $requestItem = RideRequest::where('id', $id)
                ->where('user_id', $user->id)
                ->with(['request_waypoints', 'request_trips'])
                ->first();

            if (!$requestItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Solicitud no encontrada',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'item' => $requestItem,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching request details', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Error al obtener solicitud',
            ], 500);
        }
    }
}
