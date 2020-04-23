<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Message;
use App\Events\NewMessageEvent;
use App\Events\MessagesSeenEvent;
use App\Events\MessageReceivedEvent;
use App\Events\MessagesReceivedEvent;

class ContactsController extends Controller
{
    // Constructor
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Get contact by ID
    public function getContact($id)
    {
        $user = User::find($id);

        // Get latest message
        $message = Message::where('from', $user->id)
          ->orderBy('created_at', 'desc')
          ->first();

        // Set necessary properties
        $user->lastMessage = $message;
        $user->unread = 1;
        $user->scrolls = 0;

        // Return user
        return $user;
    }

    // Get all contacts
    public function getContacts()
    {
        $contactIDs = array_map('intval', explode(',', auth()->user()->contacts));
        $contacts = User::find($contactIDs);

        foreach ($contacts as $contact) {
          Message::where('to', auth()->user()->id)
            ->where('from', $contact->id)
            ->where('received', 0)
            ->update(['received' => 1]);
        }

        $unreadPerContact = Message::selectRaw('`from` as sender_id, count(`from`) as messages_count')
          ->where('to', auth()->user()->id)
          ->where('read', false)
          ->groupBy('from')
          ->get();

        $contacts = $contacts->map(function($contact) use ($unreadPerContact) {
          $contact->scrolls = 0;
          $contact->online = false;

          $unreadContact = $unreadPerContact->where('sender_id', $contact->id)->first();
          $contact->unread = $unreadContact ? $unreadContact->messages_count : 0;

          $contact->lastMessage = Message::select('*')
            ->where(function($query) use ($contact) {
              $query->where('from', auth()->user()->id);
              $query->where('to', $contact->id);
            })->orWhere(function($query) use ($contact) {
              $query->where('from', $contact->id);
              $query->where('to', auth()->user()->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

            return $contact;
        });

        $contactsToReturn = [];
        foreach ($contacts as $contact) {
          if ($contact->lastMessage) {
            $contactsToReturn[] = $contact;
          }
        }

        usort($contactsToReturn, function($a, $b) {
          return strtotime($b->lastMessage->created_at) - strtotime($a->lastMessage->created_at);
        });

        return $contactsToReturn;
    }

    // Get all messages between this contact and current user
    public function getMessages(Request $request, $contact_id)
    {
        $currentUser = auth()->user();
        $scrolls = intval($request->query('scrolls'));

        $messages = Message::where(function($query) use ($contact_id, $currentUser) {
          $query->where('from', $currentUser->id)->where('to', $contact_id);
        })->orWhere(function($query) use ($contact_id, $currentUser) {
          $query->where('from', $contact_id)->where('to', $currentUser->id);
        })
        ->orderBy('created_at', 'desc')
        ->skip(50 * $scrolls)
        ->take(50)
        ->get();

        $messages = $messages->toArray();

        // Sort messages based on created_at
        usort($messages, function($a, $b) {
          return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        return $messages;
    }

    // Send message
    public function sendMessage(Request $request)
    {
        // Insert message
        $message = Message::create([
          'from' => auth()->user()->id,
          'to' => $request->contact_id,
          'text' => $request->text,
          'read' => false,
          'received' => false,
          'sent' => true
        ]);

        // Broadcast message
        broadcast(new NewMessageEvent($message));

        // Return message
        return $message;
    }

    // Update message and broadcast 'MessagesSeenEvent'
    public function readMessage(Request $request)
    {
        // Update 'read' column
        Message::where('from', $request->contact_id)
          ->where('to', auth()->user()->id)
          ->update(['read' => true]);

        // Broadcast 'MessagesSeenEvent'
        broadcast(new MessagesSeenEvent(
          $request->currentUser_id,
          $request->contact_id)
        );
    }

    // Update message and broadcast 'MessageReceivedEvent'
    public function receiveMessage(Request $request)
    {
        // Update 'received' column
        Message::where('from', $request->contactWhoSent)
          ->where('to', auth()->user()->id)
          ->update(['received' => true]);

        // Broadcast 'MessageReceivedEvent'
        broadcast(new MessageReceivedEvent(
          $request->contactWhoSent,
          $request->message_id
        ));
    }

    // Add contact
    public function addContact(Request $request)
    {
        $email = $request->email;
        $currentUser = auth()->user();

        // If user tries to add himself
        if ($currentUser->email === $email)
          return response(['status' => 0, 'message' => 'You can\'t add yourself as a contact.']);

        // Get contact
        $contact = User::where('email', $email)->first();

        // If email doesn't exist
        if (!$contact)
          return response(['status' => 0, 'message' => 'User not found.']);

        // Get messages between this contact and current user
        $messages = Message::where(function($query) use ($contact, $currentUser) {
          $query->where('from', $contact->id)->where('to', $currentUser->id);
        })->orWhere(function($query) use ($contact, $currentUser) {
          $query->where('from', $currentUser->id)->where('to', $contact->id);
        })->get();

        // If user is trying to add this contact twice
        if (count($messages))
          return response(['status' => 0, 'message' => 'Contact already exists.']);

        if ($contact->contacts)
          $contact->contacts .= ','.$currentUser->id;
        else
          $contact->contacts = ''.$currentUser->id;
        $contact->save();

        if ($currentUser->contacts)
          $currentUser->contacts .= ','.$contact->id;
        else
          $currentUser->contacts = ''.$contact->id;
        $currentUser->save();

        // Insert first message
        $message = Message::create([
          'from' => $currentUser->id,
          'to' => $contact->id,
          'text' => 'Hello! I would like to add you as a contact.',
          'read' => false,
          'received' => false,
          'sent' => true
        ]);

        // Broadcast message
        broadcast(new NewMessageEvent($message));

        // Add properties
        $contact->lastMessage = $message;
        $contact->online = false;

        // Response
        return response([
          'status' => 1,
          'message' => 'Contact added.',
          'contact' => $contact
        ]);
    }
}
