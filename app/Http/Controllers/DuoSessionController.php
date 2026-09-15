<?php

namespace App\Http\Controllers;

use App\Models\DuoSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DuoSessionController extends Controller
{
    // GET /api/session/{code}
    public function show(string $code): JsonResponse
    {
        $session = DuoSession::find(strtoupper($code));

        if (!$session) {
            return response()->json(['error' => 'session introuvable'], 404);
        }

        return response()->json($session);
    }

    // POST /api/session
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code'    => ['required', 'string', 'max:6'],
            'nameA'   => ['nullable', 'string', 'max:32'],
            'nameB'   => ['nullable', 'string', 'max:32'],
            'answers' => ['nullable', 'array'],
            'idx'     => ['nullable', 'integer', 'min:0'],
            'game'    => ['nullable', 'array'], // <-- AJOUTÉ : sans ça, l'état du jeu (dés, choix...) était jeté silencieusement
            'ttt'     => ['nullable', 'array'],
            'chat'    => ['nullable', 'array'],
        ]);

        $code = strtoupper($data['code']);

        $session = DuoSession::updateOrCreate(
            ['code' => $code],
            [
                'name_a'  => $data['nameA']   ?? null,
                'name_b'  => $data['nameB']   ?? null,
                'answers' => $data['answers'] ?? [],
                'idx'     => $data['idx']     ?? 0,
                'game'    => $data['game']    ?? null,
                'ttt'     => $data['ttt']     ?? null,
                'chat'    => $data['chat']    ?? [],
            ]
        );

        return response()->json($session);
    }

    // POST /api/session/{code}/game
    // Fusionne un patch PARTIEL dans le champ "game", de façon atomique (verrou de ligne).
    // C'est ce qui évite que le lancer de dé de A et celui de B, faits en même temps,
    // ne s'écrasent l'un l'autre : chacun n'envoie QUE ce qu'il vient de changer,
    // et le serveur le fusionne avec le tout dernier état en base, pas avec une copie périmée.
    public function updateGame(Request $request, string $code): JsonResponse
    {
        $data = $request->validate([
            'patch' => ['required', 'array'],
        ]);

        $code = strtoupper($code);

        $session = DB::transaction(function () use ($code, $data) {
            $session = DuoSession::where('code', $code)->lockForUpdate()->first();

            if (!$session) {
                abort(404, 'session introuvable');
            }

            $session->game = array_merge($session->game ?? [], $data['patch']);
            $session->save();

            return $session;
        });

        return response()->json($session);
    }

// Même logique que updateGame : patch partiel fusionné de façon atomique.
public function updateTtt(Request $request, string $code): JsonResponse
{
    $data = $request->validate([
        'patch' => ['required', 'array'],
    ]);

    $code = strtoupper($code);

    $session = DB::transaction(function () use ($code, $data) {
        $session = DuoSession::where('code', $code)->lockForUpdate()->first();

        if (!$session) {
            abort(404, 'session introuvable');
        }

        $session->ttt = array_merge($session->ttt ?? [], $data['patch']);
        $session->save();

        return $session;
    });

    return response()->json($session);
}

// POST /session/{code}/answers
// Fusionne UNE SEULE réponse (idx + a|b) dans le champ "answers", de façon atomique.
// Comme pour game/ttt : verrou de ligne + on ne merge que la clé modifiée,
// jamais tout le sous-tableau {a, b}, sinon on écraserait la réponse de l'autre
// si elle vient d'être enregistrée entre le chargement et l'écriture.
public function updateAnswer(Request $request, string $code): JsonResponse
{
    $data = $request->validate([
        'idx'   => ['required', 'integer', 'min:0'],
        'key'   => ['required', 'in:a,b'],
        'value' => ['required', 'string', 'max:5000'],
    ]);

    $code = strtoupper($code);

    $session = DB::transaction(function () use ($code, $data) {
        $session = DuoSession::where('code', $code)->lockForUpdate()->first();

        if (!$session) {
            abort(404, 'session introuvable');
        }

        $answers = $session->answers ?? [];
        $idx = (string) $data['idx']; // clé en string : évite tout réindexage par PHP/JSON

        if (!isset($answers[$idx]) || !is_array($answers[$idx])) {
            $answers[$idx] = ['a' => '', 'b' => ''];
        }

        $answers[$idx][$data['key']] = $data['value'];

        $session->answers = $answers;
        $session->save();

        return $session;
    });

    return response()->json($session);
}

    // POST /session/{code}/chat — ajoute un message (append atomique).
    public function appendChat(Request $request, string $code): JsonResponse
    {
        $data = $request->validate([
            'from' => ['required', 'in:a,b'],
            'text' => ['required', 'string', 'max:2000'],
        ]);

        $code = strtoupper($code);

        $session = DB::transaction(function () use ($code, $data) {
            $session = DuoSession::where('code', $code)->lockForUpdate()->first();

            if (!$session) {
                abort(404, 'session introuvable');
            }

            $chat = $session->chat ?? [];
            $chat[] = [
                'id'   => uniqid('', true),
                'from' => $data['from'],
                'text' => trim($data['text']),
                'at'   => now()->toIso8601String(),
            ];

            if (count($chat) > 400) {
                $chat = array_slice($chat, -400);
            }

            $session->chat = $chat;
            $session->save();

            return $session;
        });

        return response()->json($session);
    }
}