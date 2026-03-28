<?php

namespace App\Http\Controllers\Api;

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
                'comments' => 'sometimes|string|max:500',
                'filters' => 'sometimes|array',
                'config' => 'sometimes|array',
                'date' => 'sometimes|string|max:20',
                'hour' => 'sometimes|string|max:20',
                'is_scheduling' => 'sometimes|boolean',
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
                DB::table('requests')
                    ->where('user_id', $user->id)
                    ->whereIn('status', ['REQUIRED', 'ACCEPTED'])
                    ->update(['status' => 'CANCELLED']);

                $requestUuid = md5(uniqid((string)$user->id, true));
                $requestId = DB::table('requests')->insertGetId([
                    'uuid' => $requestUuid,
                    'status' => 'REQUIRED',
                    'type_request_id' => (int)$homeId,
                    'user_id' => (int)$user->id,
                    'payment_method_id' => (int)$validated['payment_method_id'],
                    'offered_price' => (float)$validated['offered_price'],
                    'type' => 'trip',
                    'distance' => isset($validated['filters']['distance'])
                        ? (string)$validated['filters']['distance']
                        : null,
                    'requested_rating' => isset($validated['filters']['requested_rating'])
                        ? (int)$validated['filters']['requested_rating']
                        : 3,
                    'taxi_company' => isset($validated['filters']['taxi_company'])
                        ? (string)$validated['filters']['taxi_company']
                        : '-1',
                    'comments' => isset($validated['comments']) ? (string)$validated['comments'] : '',
                    'longitude' => (string)$waypoints[0]['longitude'],
                    'latitude' => (string)$waypoints[0]['latitude'],
                    'is_counterofferable' => 0,
                    'is_scheduling' => isset($validated['is_scheduling']) && $validated['is_scheduling'] ? 1 : 0,
                    'date' => isset($validated['date']) ? (string)$validated['date'] : null,
                    'hour' => isset($validated['hour']) ? (string)$validated['hour'] : null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table('request_trips')->insert([
                    'uuid' => md5(uniqid('trip', true)),
                    'parent_id' => $requestId,
                    'number_of_passengers' => isset($validated['config']['number_of_passengers'])
                        ? (int)$validated['config']['number_of_passengers']
                        : 1,
                    'car_with_grill' => !empty($validated['config']['car_with_grill']) ? 1 : 0,
                    'baby_chair' => !empty($validated['config']['baby_chair']) ? 1 : 0,
                    'travel_with_pets' => !empty($validated['config']['travel_with_pet']) ? 1 : 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                foreach ($waypoints as $index => $waypoint) {
                    DB::table('request_waypoints')->insert([
                        'uuid' => md5(uniqid('wp', true)),
                        'parent_id' => $requestId,
                        'type' => (string)$waypoint['type'],
                        'address' => (string)$waypoint['address'],
                        'order' => isset($waypoint['order'])
                            ? (int)$waypoint['order']
                            : ($index + 1),
                        'status' => 'holding',
                        'latitude' => (string)$waypoint['latitude'],
                        'longitude' => (string)$waypoint['longitude'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                $createdRequest = [
                    'id' => $requestId,
                    'uuid' => $requestUuid,
                    'status' => 'REQUIRED',
                ];
            });

            Log::info('Trip request created', [
                'request_id' => $createdRequest['id'],
                'user_id' => $user->id,
                'waypoints_count' => count($waypoints),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Solicitud de viaje creada exitosamente',
                'item' => [
                    'request_id' => $createdRequest['id'],
                    'uuid' => $createdRequest['uuid'],
                    'status' => $createdRequest['status'],
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

            $updated = DB::table('requests')->where('user_id', $user->id)
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

            $requestItem = DB::table('requests')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$requestItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Solicitud no encontrada',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'item' => [
                    'request' => $requestItem,
                    'waypoints' => DB::table('request_waypoints')->where('parent_id', $requestItem->id)->orderBy('order')->get(),
                    'trip' => DB::table('request_trips')->where('parent_id', $requestItem->id)->first(),
                ],
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
