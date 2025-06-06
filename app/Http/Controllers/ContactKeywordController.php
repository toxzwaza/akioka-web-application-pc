<?php

namespace App\Http\Controllers;

use App\Models\ContactKeyword;
use Illuminate\Http\Request;

class ContactKeywordController extends Controller
{
    //
    public function store(Request $request)
    {
        $status = true;

        $keyword = $request->keyword;
        $user_id = $request->user_id;

        try {
            $contact_keyword = new ContactKeyword();
            $contact_keyword->user_id = $user_id;
            $contact_keyword->keyword = $keyword;
            $contact_keyword->save();
        } catch (\Exception $e) {
            $status = false;
        }
        return response()->json(['status' => $status]);
    }

    public function getContactKeyword(Request $request){
        $contact_keywords = ContactKeyword::select('contact_keywords.id','users.id as user_id', 'users.name', 'contact_keywords.keyword')->join('users', 'users.id', 'contact_keywords.user_id')->get();

        return response()->json($contact_keywords);
    }

    public function delete(Request $request){
        $contact_keyword_id = $request->contact_keyword_id;
        $status = true;

        try{
            ContactKeyword::where('id', $contact_keyword_id)->delete();
        }catch(\Exception $e){
            $status = false;
        }

        return response()->json(['status' => $status]);
    }
}
