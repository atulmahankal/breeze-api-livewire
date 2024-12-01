<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //?search=3210&sort=name&order=desc&page=3&perpage=5

        $query = User::query();

        // Total users count (before filtering)
        $totalRecords = $query->count();

        // Filter query
        $filteredQuery = $query
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->search}%")
                    ->orWhere('email', 'LIKE', "%{$request->search}%")
                    ->orWhere('contact', 'LIKE', "%{$request->search}%");
            })
            ->when($request->has('name'), function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->name}%");
            })
            ->when($request->has('email'), function ($query) use ($request) {
                $query->where('email', 'LIKE', "%{$request->email}%");
            })
            ->when($request->has('contact'), function ($query) use ($request) {
                $query->where('contact', 'LIKE', "%{$request->contact}%");
            })
            ->when($request->has('sort') && $request->has('order'), function ($query) use ($request) {
                $sortColumn = $request->sort;
                $sortOrder = $request->order;

                // Validate the 'order' value to ensure it's either 'asc' or 'desc'
                if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
                    $sortOrder = 'asc';
                }
                $query->orderBy($sortColumn, $sortOrder);
            })
            ->select('id', 'name', 'email');

        // Filtered records count
        $filteredRecords = $filteredQuery->count();

        // Paginate the filtered results
        $paginatedResults = $filteredQuery->paginate($request->perpage ?? 10);
        // return $paginatedResults;

        return (new UserCollection($paginatedResults))
            ->additional([
                'total_records' => $totalRecords,
                'filtered_records' => $filteredRecords,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->update($request); // Delegate to update method
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = User::select('id', 'name', 'email')->where('id', $id)->first();

        if (!$record) {
            return response()->json(['message' => 'No record found.'], 404); // Handle not found
        }

        return new UserResource($record);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id = null)
    {
        // Create Request
        $userRequest = new UserRequest();

        // Update request method
        $userRequest->setMethod($request->method());

        // Authenticate request
        if (!$userRequest->authorize()) {
            throw new \Illuminate\Auth\Access\AuthorizationException;
        }

        // Get only validated data
        $validatedData = $request->validate($userRequest->rules());

        return DB::transaction(function () use ($validatedData, $id) {
            // Find or create record
            $record = $id ? User::find($id) : new User;

            if (!$record) {
                return response()->json(['message' => 'No record found.'], 404);
            }

            // Update the record
            $oldPassword = $record->password ?? Hash::make('password');

            $record->fill([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => (isset($validatedData['password']) && $validatedData['password'])
                    ? Hash::make($validatedData['password'])
                    : $oldPassword
            ])->save();

            return (new UserResource($record))
                ->additional([
                    'message' => 'User created successfully',
                ])
                ->response()
                ->setStatusCode($id ? 200 : 201);   // Return appropriate status codes
        }, 3); // Retry up to 3 times in case of a deadlock
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find record
        $record = User::where('id', $id)->first();

        if (!$record) {
            return response()->json(['message' => 'No Record found.'], 404); // Handle not found
        }
        $record->delete();
        return response()->noContent()->setStatusCode(204);
    }
}
