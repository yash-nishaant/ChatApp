<?php

namespace App\Http\Controllers;

use App\User;
use App\Message;
use Illuminate\Http\Request;
use App\Events\NewMessage;

class ContactsController extends Controller
{
    public function index()
    {
        $contacts = User::where('id', '!=', auth()->id())->get();

        $unreadMessageIDs = Message::select(\DB::raw('`from` as sender_id, count(`from`) as message_count'))
                        ->where('to', auth()->id())
                        ->where('read', false)
                        ->groupBy('from')->get();
        
        $contacts = $contacts->map(function($contact) use ($unreadMessageIDs){
            $contactUnread = $unreadMessageIDs->where('sender_id', $contact->id)->first();
            $contact->unread = $contactUnread ? $contactUnread->message_count : 0;
            return $contact;
        });

        return response()->json($contacts);
    }

    public function getMessages($id)
    {
        Message::where('from', $id)->where('to', auth()->id())->update(['read' => true]);

        $messages = Message::where(function($q) use ($id){
            $q->where('from', auth()->id());
            $q->where('to', $id);    
        })->orWhere(function($q) use ($id) {
            $q->where('from', $id);
            $q->where('to', auth()->id());
        })->get();
        return response()->json($messages); 
    }

    public function send(Request $request)
    {
        $message = Message::create([
            'from' => auth()->id(),
            'to' => $request->contact_id,
            'text' => $request->text,
        ]);
        
        broadcast(new NewMessage($message));

        return response()->json($message);
    }
}
