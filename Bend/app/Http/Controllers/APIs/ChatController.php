<?php
namespace App\Http\Controllers\APIs;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
class ChatController extends Controller
{
    public function createChatSendMessage(Request $request)
    {
        // Add the authenticated user to the chat
        $txUser = Auth::user();
        // Create a new chat
        $chat = Chat::create(['ownership'=>$txUser->id]);
        $rxUser_id= $request->input('rxUser_id');
        $chat->users()->attach($txUser->id);
        $chat->users()->attach($rxUser_id);
        // Send a message to the chat
        $message = $this->sendMessage($request->input('content'), $chat, $txUser, $rxUser_id);

        return response()->json(['chat' => $chat, 'message' => $message]);
    }

    public function sendMessageToExistingChat(Request $request)
    {
        // Find the chat
        $chat = Chat::findOrFail($request->input('chatId'));

        // Get the authenticated user
        $txUser = Auth::user();
        $rxUser_id= $request->input('rxUser_id');
        $chat->users()->attach($txUser->id);
        $chat->users()->attach($rxUser_id);
        // Send a message to the chat
        $message = $this->sendMessage($request->input('content'), $chat, $txUser, $rxUser_id);

        return response()->json(['chat' => $chat, 'message' => $message]);
    }

    public function editMessage(Request $request, $messageId)
    {
        // Find the message
        $message = Message::findOrFail($messageId);

        // Check if the authenticated user is the message owner
        $this->authorize('update', $message);
        // in case we need to do this authorization action without the policy comment the upper line and uncommint the next code snippet
        // if($message->ownership != Auth::user()->id)
        // {
        //     return response()->json(['message' => 'note authorized'])->status(403);
        // }
        // Update the message content
        $message->content = $request->input('content');
        $message->save();

        return response()->json(['message' => $message]);
    }

    public function deleteMessageforMe($messageId)
    {
        // Find the message
        $message = Message::findOrFail($messageId);

        // Check if the authenticated user is the message owner
        $this->authorize('delete_for_me', $message);

        // Delete the message
        $message->users()->updateExistingPivot(Auth::user()->id, ['status' => 'deleted']);

        return response()->json(['message' => 'Message deleted successfully']);
    }
    public function deleteMessageforAll($messageId)
    {
        // Find the message
        $message = Message::findOrFail($messageId);

        // Check if the authenticated user is the message owner
        $this->authorize('delete_for_all', $message);

        // Delete the message
        $message->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }

    // Helper function to create and send a message
    private function sendMessage($content, $chat, $txUser, $rxUser_id)
    {
        $message = new Message([
            'ownership'=>$txUser->id,
            'content' => $content,
            'type' => 'text', // Assuming it's a text message
        ]);

        // Save the message to the chat and associate it with the user
        $chat->messages()->save($message);
        $message->users()->attach($txUser->id, ['status' => 'sent']);
        $message->users()->attach($rxUser_id, ['status' => 'waiting']);

        return $message;
    }
    public function deleteChat($chatId)
    {
        // Find the chat
        $chat = Chat::findOrFail($chatId);
        $this->authorize('delete', $chat);
        // Delete the chat along with its messages
        $chat->messages()->delete();
        $chat->delete();

        return response()->json(['message' => 'Chat and messages deleted successfully']);
    }
}
