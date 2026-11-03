<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdatePasswordRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return response()->noContent();
    }

    public function updateName(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        $request->user()->update($validated);

        return response()->noContent();
    }

    public function destroy(string $id)
    {
        //
    }
}
