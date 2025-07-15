<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class StudentRegistrationController extends Controller
{
    /**
     * Show the student registration form.
     */
    public function create()
    {
        return view('auth.student-register');
    }

    /**
     * Handle student registration.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate unique student ID
        $studentId = $this->generateStudentId();

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'student_id' => $studentId,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'enrollment_date' => now(),
            'status' => 'active',
        ]);

        // Assign Student role
        $user->assignRole('Student');

        // Generate a token for the user
        $token = $user->createToken('student-portal-token')->plainTextToken;

        // Redirect to college portal with token
        return redirect()->away('https://college.pnmtc.edu.gh/auth/callback?token=' . $token);
    }

    /**
     * Generate a unique student ID.
     */
    private function generateStudentId(): string
    {
        $year = date('Y');
        $prefix = 'STU' . $year;
        
        // Get the last student ID for this year
        $lastStudent = User::where('student_id', 'like', $prefix . '%')
            ->orderBy('student_id', 'desc')
            ->first();

        if ($lastStudent) {
            // Extract the number and increment
            $lastNumber = (int) substr($lastStudent->student_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
