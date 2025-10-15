<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;

class AddressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        $addresses = Address::where('user_id', $user->id)->orderByDesc('is_default')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $addresses]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:500',
            'apartment' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:32',
            'email' => 'required|email|max:255',
            'label' => 'nullable|string|max:50',
            'is_default' => 'boolean',
        ]);
        $data['user_id'] = $user->id;
        if (!empty($data['is_default'])) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }
        $address = Address::create($data);
        return response()->json(['success' => true, 'data' => $address]);
    }

    public function update(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        $address = Address::where('user_id', $user->id)->findOrFail($id);
        $data = $request->validate([
            'full_name' => 'sometimes|required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'street_address' => 'sometimes|required|string|max:500',
            'apartment' => 'nullable|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'province' => 'sometimes|required|string|max:255',
            'postal_code' => 'sometimes|required|string|max:10',
            'phone' => 'sometimes|required|string|max:32',
            'email' => 'sometimes|required|email|max:255',
            'label' => 'nullable|string|max:50',
            'is_default' => 'boolean',
        ]);
        if (!empty($data['is_default'])) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }
        $address->update($data);
        return response()->json(['success' => true, 'data' => $address]);
    }

    public function destroy(int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        $address = Address::where('user_id', $user->id)->findOrFail($id);
        $address->delete();
        return response()->json(['success' => true]);
    }

    public function makeDefault(int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        Address::where('user_id', $user->id)->update(['is_default' => false]);
        $address = Address::where('user_id', $user->id)->findOrFail($id);
        $address->is_default = true;
        $address->save();
        return response()->json(['success' => true, 'data' => $address]);
    }
}


