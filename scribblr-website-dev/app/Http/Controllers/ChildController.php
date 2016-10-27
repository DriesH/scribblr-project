<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Child;

use App\User;

use Auth;

use DateTime;

class ChildController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function childrenDashboard()
    {
        $userId   = Auth::user()->id;
        $children = User::findOrFail($userId)->Children()->get();

        if ( $children->count() == 0 ) {
            return view('dashboard', [
              'children' => 'empty'
            ]);
        }
        else {
            return view('dashboard', [
              'children' => $children
            ]);
        }

    }

    public function newChild (Request $request) {
        $this->validate($request, [
        'childName' => 'required',
        'gender' => 'required',
        'dateOfBirth' => 'required|date|date_format:d-m-Y|before:today',
        ]);

        $childName    = $request->json('childName');
        $gender       = $request->json('gender');
        $_dateOfBirth = $request->json('dateOfBirth');
        $dateOfBirth  = date('d-m-Y', strtotime($_dateOfBirth));
        $userId       = Auth::user()->id;

        $newChild = Child::create([
            'childName' => $childName,
            'dateOfBirth' => $dateOfBirth,
            'gender' => $gender,
            'user_id' => $userId
        ]);

        return $newChild;
    }

    public function getChildren () {
        $userId = Auth::user()->id;

        $userChildren = User::find($userId)->children()->get();

        return json_encode($userChildren);
    }

    public function updateChild ($id, Request $request) {

        $childNameUpdate = $request->json('childName');
        $genderUpdate = $request->json('childName');
        $_dateOfBirthUpdate = $request->json('dateOfBirth');


        $selectedChild = Child::where('id', $id)->update([
            'childName' => $childName,
            'dateOfBirth' => $dateOfBirth,
            'gender' => $gender
        ]);

    }
}
