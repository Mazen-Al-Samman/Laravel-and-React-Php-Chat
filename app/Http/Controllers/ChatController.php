<?php

namespace App\Http\Controllers;

use App\Models\ChatsModel;
use App\Models\User;

class ChatController extends Controller
{
    public function index($id)
    {
        $userModel = User::find($id);
        $usersList = User::where('id', '!=', auth()->id())->get();
        $chats = ChatsModel::where(['sender_id' => $id])->where(['receiver_id' => auth()->id()])
            ->orWhere(['receiver_id' => $id])->where(['sender_id' => auth()->id()])
            ->orderBy('id')->get();
        if (empty($userModel)) throw new \Exception("User Not Found");
        return view('chat', ['user' => $userModel, 'users' => $usersList, 'chats' => $chats]);
    }
}
