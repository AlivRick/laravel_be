<?php

// namespace App\Http\Controllers;

// use App\Models\UserPromotion;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;
// use App\Traits\ApiResponse;

// class UserPromotionController extends Controller
// {
//     use ApiResponse;

//     public function index()
//     {
//         $userPromotions = UserPromotion::with(['user', 'promotion'])->get();
//         return $this->createSuccessResponse($userPromotions);
//     }

//     public function store(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'user_id' => 'required|string|size:24|exists:user,user_id',
//             'promotion_id' => 'required|string|size:24|exists:promotion,promotion_id',
//         ]);

//         if ($validator->fails()) {
//             return $this->createErrorResponse($validator->errors()->first(), 400);
//         }

//         $userPromotion = UserPromotion::create($validator->validated());
//         return $this->createSuccessResponse($userPromotion->load(['user', 'promotion']), 201);
//     }

//     public function show($id)
//     {
//         $userPromotion = UserPromotion::with(['user', 'promotion'])->find($id);
//         if (!$userPromotion) {
//             return $this->createErrorResponse('User promotion not found', 404);
//         }
//         return $this->createSuccessResponse($userPromotion);
//     }

//     public function update(Request $request, $id)
//     {
//         $userPromotion = UserPromotion::find($id);
//         if (!$userPromotion) {
//             return $this->createErrorResponse('User promotion not found', 404);
//         }

//         $validator = Validator::make($request->all(), [
//             'is_used' => 'sometimes|boolean',
//             'used_at' => 'nullable|date',
//         ]);

//         if ($validator->fails()) {
//             return $this->createErrorResponse($validator->errors()->first(), 400);
//         }

//         $userPromotion->update($validator->validated());
//         return $this->createSuccessResponse($userPromotion->load(['user', 'promotion']));
//     }

//     public function destroy($id)
//     {
//         $userPromotion = UserPromotion::find($id);
//         if (!$userPromotion) {
//             return $this->createErrorResponse('User promotion not found', 404);
//         }

//         $userPromotion->delete();
//         return $this->createSuccessResponse(['message' => 'User promotion deleted successfully']);
//     }
// } 